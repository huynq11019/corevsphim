<?php

namespace App\Crawlers;

use Ophim\Crawler\OphimCrawler\Crawler as BaseCrawler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ophim\Core\Models\Movie;
use Ophim\Crawler\OphimCrawler\Collector;

class CustomCrawler extends BaseCrawler
{
    public function handle()
    {
        Log::info('CustomCrawler: Started', ['link' => $this->link]);
        
        try {
            // Lấy dữ liệu từ API
            $response = Http::get($this->link);
            $payload = $response->json();
            
            Log::info('CustomCrawler: API Response Structure', [
                'status_code' => $response->status(),
                'response_keys' => array_keys($payload ?? []),
                'has_data' => isset($payload['data']),
                'has_data_item' => isset($payload['data']['item']),
                'has_movie' => isset($payload['movie']),
                'payload_sample' => $payload ? array_slice($payload, 0, 3, true) : 'null'
            ]);
            
            // Transform payload structure để match với Crawler expectations
            if (!isset($payload['movie'])) {
                Log::info('CustomCrawler: Transforming payload structure');
                
                if (isset($payload['data']['item'])) {
                    // API trả về dữ liệu trong data.item
                    $movieData = $payload['data']['item'];
                    $episodes = $movieData['episodes'] ?? [];
                    
                    $payload = [
                        'movie' => $movieData,
                        'episodes' => $episodes
                    ];
                    
                    Log::info('CustomCrawler: Transformed from data.item structure', [
                        'movie_name' => $movieData['name'] ?? 'unknown',
                        'movie_id' => $movieData['_id'] ?? 'unknown',
                        'episodes_count' => count($episodes)
                    ]);
                } elseif (isset($payload['_id']) || isset($payload['name'])) {
                    // Dữ liệu ở root level
                    Log::info('CustomCrawler: Transforming from root level structure');
                    $payload = [
                        'movie' => $payload,
                        'episodes' => $payload['episodes'] ?? []
                    ];
                } else {
                    Log::error('CustomCrawler: Cannot transform payload structure', [
                        'available_keys' => array_keys($payload ?? []),
                        'payload' => $payload
                    ]);
                    throw new \Exception('API response structure not supported');
                }
            }
            
            Log::info('CustomCrawler: Final payload structure', [
                'has_movie' => isset($payload['movie']),
                'movie_keys' => isset($payload['movie']) ? array_keys($payload['movie']) : 'missing',
                'movie_name' => $payload['movie']['name'] ?? 'unknown'
            ]);
            
            Log::info('CustomCrawler: Payload validated, checking exclusions');
            $this->checkIsInExcludedList($payload);
            
            // Tìm movie trong database
            $movie = Movie::where('update_handler', static::class)
                ->where('update_identity', $payload['movie']['_id'])
                ->first();
                
            Log::info('CustomCrawler: Movie lookup', [
                'movie_id' => $payload['movie']['_id'] ?? 'missing',
                'found_existing' => !is_null($movie)
            ]);
            
            // Tạo checksum để kiểm tra thay đổi
            $body = json_encode($payload);
            $checksum = md5($body);
            
            if (!$this->hasChange($movie, $checksum) && $this->forceUpdate == false) {
                Log::info('CustomCrawler: No changes detected, skipping update');
                return false;
            }
            
            Log::info('CustomCrawler: Processing movie data', ['force_update' => $this->forceUpdate]);
            
            // Sử dụng Collector để extract thông tin
            $info = (new Collector($payload, $this->fields, $this->forceUpdate))->get();
            
            Log::info('CustomCrawler: Collector data extracted', [
                'fields' => array_keys($info),
                'movie_name' => $info['name'] ?? 'unknown'
            ]);
            
            // Tạo hoặc update movie
            if ($movie) {
                Log::info('CustomCrawler: Updating existing movie', ['movie_id' => $movie->id]);
                $movie->updated_at = now();
                $movie->update(collect($info)->only($this->fields)->merge(['update_checksum' => $checksum])->toArray());
            } else {
                Log::info('CustomCrawler: Creating new movie');
                $movie = Movie::create(array_merge($info, [
                    'update_handler' => static::class,
                    'update_identity' => $payload['movie']['_id'],
                    'update_checksum' => $checksum
                ]));
            }
            
            // Sync relationships
            $this->syncRelationships($movie, $payload);
            
            Log::info('CustomCrawler: Movie processing completed successfully', [
                'movie_id' => $movie->id,
                'movie_name' => $movie->name
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('CustomCrawler: Error occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    protected function checkIsInExcludedList($payload)
    {
        Log::info('CustomCrawler: Checking excluded list', [
            'has_movie' => isset($payload['movie']),
            'movie_keys' => isset($payload['movie']) ? array_keys($payload['movie']) : 'missing'
        ]);
        
        if (!isset($payload['movie'])) {
            throw new \Exception('Movie data not found in payload');
        }
        
        $movieData = $payload['movie'];
        
        // Check type exclusion
        if (isset($movieData['type'])) {
            $newType = $movieData['type'];
            if (in_array($newType, $this->excludedType)) {
                Log::warning('CustomCrawler: Movie excluded by type', ['type' => $newType]);
                throw new \Exception("Thuộc định dạng đã loại trừ: {$newType}");
            }
        }
        
        // Check category exclusion
        if (isset($movieData['category'])) {
            $newCategories = collect($movieData['category'])->pluck('name')->toArray();
            if (array_intersect($newCategories, $this->excludedCategories)) {
                Log::warning('CustomCrawler: Movie excluded by categories', ['categories' => $newCategories]);
                throw new \Exception("Thuộc thể loại đã loại trừ");
            }
        }
        
        // Check region exclusion
        if (isset($movieData['country'])) {
            $newRegions = collect($movieData['country'])->pluck('name')->toArray();
            if (array_intersect($newRegions, $this->excludedRegions)) {
                Log::warning('CustomCrawler: Movie excluded by regions', ['regions' => $newRegions]);
                throw new \Exception("Thuộc quốc gia đã loại trừ");
            }
        }
        
        Log::info('CustomCrawler: Movie passed exclusion checks');
    }
    
    protected function syncRelationships($movie, $payload)
    {
        Log::info('CustomCrawler: Starting relationship sync');
        
        try {
            $this->syncActors($movie, $payload);
            $this->syncDirectors($movie, $payload);
            $this->syncCategories($movie, $payload);
            $this->syncRegions($movie, $payload);
            $this->syncTags($movie, $payload);
            $this->syncStudios($movie, $payload);
            $this->updateEpisodes($movie, $payload);
            
            Log::info('CustomCrawler: Relationship sync completed');
        } catch (\Exception $e) {
            Log::error('CustomCrawler: Error syncing relationships', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    protected function hasChange(?Movie $movie, $checksum)
    {
        return is_null($movie) || ($movie->update_checksum != $checksum);
    }
}
