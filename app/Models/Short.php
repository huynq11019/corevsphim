<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Short extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'video_url',
        'thumbnail_url',
        'duration',
        'quality',
        'views',
        'likes',
        'dislikes',
        'shares',
        'hashtags',
        'status',
        'is_featured',
        'sort_order',
        'user_id',
        'source'
    ];

    protected $casts = [
        'hashtags' => 'array',
        'is_featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Auto generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($short) {
            if (empty($short->slug)) {
                $short->slug = Str::slug($short->title . '-' . time());
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(ShortComment::class);
    }

    public function interactions()
    {
        return $this->hasMany(ShortInteraction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeTrending($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7))
                    ->orderBy('views', 'desc');
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getFormattedViewsAttribute()
    {
        if ($this->views >= 1000000) {
            return round($this->views / 1000000, 1) . 'M';
        } elseif ($this->views >= 1000) {
            return round($this->views / 1000, 1) . 'K';
        }
        return $this->views;
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function addInteraction($type, $userId = null, $ipAddress = null)
    {
        $interaction = ShortInteraction::firstOrCreate([
            'short_id' => $this->id,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'type' => $type
        ]);

        if ($interaction->wasRecentlyCreated) {
            $this->increment($type === 'view' ? 'views' : $type . 's');
        }

        return $interaction;
    }

    public function hasUserInteraction($type, $userId = null, $ipAddress = null)
    {
        $query = $this->interactions()->where('type', $type);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('ip_address', $ipAddress);
        }

        return $query->exists();
    }
}
