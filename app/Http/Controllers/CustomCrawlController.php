<?php

namespace App\Http\Controllers;

use Ophim\Crawler\OphimCrawler\Controllers\CrawlController as BaseCrawlController;
use Illuminate\Http\Request;
use App\Library\CustomOption as Option;
use Illuminate\Support\Facades\Http;
use App\Crawlers\CustomCrawler;
use Illuminate\Support\Facades\Log;

class CustomCrawlController extends BaseCrawlController
{
    // public function fetch(Request $request)
    // {
    //     // Custom logic here
    //     return parent::fetch($request);
    // }

    public function fetch(Request $request)
    {
        try {
            $data = collect();

            $request['link'] = preg_split('/[\n\r]+/', $request['link']);

            foreach ($request['link'] as $link) {
                if (preg_match('/(.*?)(\/phim\/)(.*?)/', $link)) {
                    $link = sprintf('%s/phim/%s', Option::get('domain', 'https://ophim1.com'), explode('phim/', $link)[1]);
                    $response = json_decode(file_get_contents($link), true);
                    $data->push(collect($response['movie'])->only('name', 'slug')->toArray());
                } else {
                    for ($i = $request['from']; $i <= $request['to']; $i++) {
                        $response = json_decode(Http::timeout(30)->get($link, [
                            'page' => $i
                        ]), true);
                        if ($response['status']) {
                            $data->push(...$response['items']);
                        }
                    }
                }
            }

            return $data->shuffle();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function crawl(Request $request)
    {
        // $pattern = sprintf('%s/phim/{slug}', config('ophim_crawler.domain', 'https://xxvnapi.com/api'));
        $pattern = 'https://platform.phoai.vn/webhook/xxvnapi/detail?slug={slug}';
        try {
            $link = str_replace('{slug}', $request['slug'], $pattern);

            Log::info('CustomCrawlController: Starting crawl', [
                'slug' => $request['slug'],
                'link' => $link,
                'fields' => request('fields', []),
                'excludedCategories' => request('excludedCategories', []),
                'excludedRegions' => request('excludedRegions', []),
                'excludedType' => request('excludedType', []),
                'forceUpdate' => request('forceUpdate', false)
            ]);

            $crawler = (new CustomCrawler($link, request('fields', []), request('excludedCategories', []), request('excludedRegions', []), request('excludedType', []), request('forceUpdate', false)))->handle();

            Log::info('CustomCrawlController: Crawl completed successfully');

        } catch (\Exception $e) {
            Log::error('CustomCrawlController: Crawl failed', [
                'error' => $e->getMessage(),
                'slug' => $request['slug'],
                'link' => $link ?? 'unknown'
            ]);
            return response()->json(['message' => $e->getMessage(), 'wait' => false], 500);
        }
        return response()->json(['message' => 'OK', 'wait' => $crawler ?? true]);
    }
}
