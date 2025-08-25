<?php

namespace App\Models;

use Ophim\Core\Models\Episode as BaseEpisode;
use Illuminate\Database\Eloquent\Builder;

class Episode extends BaseEpisode
{
    protected $fillable = [
        'name',
        'slug',
        'movie_id',
        'server',
        'type',
        'link',
        'status',
        'view',
        // New shorts fields
        'is_short',
        'hashtags',
        'likes',
        'dislikes',
        'shares',
        'duration_seconds'
    ];

    protected $casts = [
        'is_short' => 'boolean',
        'hashtags' => 'array'
    ];

    // Relationships for shorts
    public function interactions()
    {
        return $this->hasMany(EpisodeInteraction::class);
    }

    public function shortComments()
    {
        return $this->hasMany(Comment::class, 'episode_id')
                   ->whereNull('parent_id')
                   ->orderBy('created_at', 'desc');
    }

    // Scopes for shorts
    public function scopeShorts(Builder $query)
    {
        return $query->where('is_short', true);
    }

    public function scopeRegularEpisodes(Builder $query)
    {
        return $query->where('is_short', false);
    }

    public function scopeTrendingShorts(Builder $query)
    {
        return $query->shorts()
            ->where('status', 'active')
            ->orderByDesc('likes')
            ->orderByDesc('view');
    }

    public function scopeLatestShorts(Builder $query)
    {
        return $query->shorts()
            ->where('status', 'active')
            ->latest();
    }

    public function scopePopularShorts(Builder $query)
    {
        return $query->shorts()
            ->where('status', 'active')
            ->orderByDesc('view')
            ->orderByDesc('likes');
    }

    // Helper methods
    public function isShort()
    {
        return $this->is_short;
    }

    public function getHashtagsStringAttribute()
    {
        return $this->hashtags ? implode(', ', $this->hashtags) : '';
    }

    public function getDurationFormattedAttribute()
    {
        if (!$this->duration_seconds) return '';

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getFormattedViewsAttribute()
    {
        if ($this->view >= 1000000) {
            return round($this->view / 1000000, 1) . 'M';
        } elseif ($this->view >= 1000) {
            return round($this->view / 1000, 1) . 'K';
        }
        return $this->view;
    }

    // Interaction methods
    public function hasUserInteraction($type, $userId = null, $userIp = null)
    {
        // If no parameters provided, use current user
        if ($userId === null && $userIp === null) {
            $userId = auth()->id();
            $userIp = request()->ip();

            if (!$userId && !$userIp) {
                return false;
            }
        }

        $query = $this->interactions()->where('type', $type);

        return $query->where(function ($q) use ($userId, $userIp) {
            if ($userId) {
                $q->where('user_id', $userId);
            }
            if ($userIp) {
                $q->orWhere('user_ip', $userIp);
            }
        })->exists();
    }

    public function addInteraction($type, $userId = null, $userIp = null)
    {
        $interaction = EpisodeInteraction::firstOrCreate([
            'episode_id' => $this->id,
            'user_id' => $userId,
            'user_ip' => $userIp,
            'type' => $type
        ]);

        if ($interaction->wasRecentlyCreated) {
            switch ($type) {
                case 'view':
                    $this->increment('view');
                    break;
                case 'like':
                    $this->increment('likes');
                    break;
                case 'dislike':
                    $this->increment('dislikes');
                    break;
                case 'share':
                    $this->increment('shares');
                    break;
            }
        }

        return $interaction;
    }

    public function removeInteraction($type, $userId = null, $userIp = null)
    {
        $deleted = $this->interactions()
            ->where('type', $type)
            ->where(function ($q) use ($userId, $userIp) {
                if ($userId) {
                    $q->where('user_id', $userId);
                }
                if ($userIp) {
                    $q->orWhere('user_ip', $userIp);
                }
            })
            ->delete();

        if ($deleted) {
            switch ($type) {
                case 'like':
                    $this->decrement('likes');
                    break;
                case 'dislike':
                    $this->decrement('dislikes');
                    break;
                case 'share':
                    $this->decrement('shares');
                    break;
            }
        }

        return $deleted;
    }

    // Override getUrl for shorts
    public function getUrl()
    {
        if ($this->is_short) {
            return route('shorts.show', $this->slug);
        }

        return parent::getUrl();
    }

    // Video and poster URL methods for shorts
    public function getVideoUrl()
    {
        if ($this->is_short && $this->link) {
            // Handle different link formats
            if (is_array($this->link)) {
                return $this->link[0]['link_m3u8'] ?? $this->link[0]['link_mp4'] ?? '';
            }

            // If it's a string URL
            if (filter_var($this->link, FILTER_VALIDATE_URL)) {
                return $this->link;
            }

            // Parse JSON if needed
            if (is_string($this->link)) {
                $linkData = json_decode($this->link, true);
                if ($linkData && is_array($linkData)) {
                    return $linkData[0]['link_m3u8'] ?? $linkData[0]['link_mp4'] ?? '';
                }
            }
        }

        return '';
    }

    public function getPosterUrl()
    {
        // Use movie poster if available
        if ($this->movie && $this->movie->poster_url) {
            return $this->movie->poster_url;
        }

        // Use movie thumb url as fallback
        if ($this->movie && $this->movie->thumb_url) {
            return $this->movie->thumb_url;
        }

        // Default shorts placeholder
        return '/images/shorts-placeholder.jpg';
    }

    // Accessor for count attributes used in views
    public function getLikesCountAttribute()
    {
        return $this->likes ?? 0;
    }

    public function getDislikesCountAttribute()
    {
        return $this->dislikes ?? 0;
    }

    public function getSharesCountAttribute()
    {
        return $this->shares ?? 0;
    }

    public function getCommentsCountAttribute()
    {
        return $this->shortComments()->count();
    }

    public function getViewTotalAttribute()
    {
        return $this->view ?? 0;
    }
}
