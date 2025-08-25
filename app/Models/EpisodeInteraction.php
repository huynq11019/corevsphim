<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpisodeInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'episode_id',
        'user_id',
        'user_ip',
        'type'
    ];

    // Relationships
    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUser($query, $userId = null, $userIp = null)
    {
        return $query->where(function ($q) use ($userId, $userIp) {
            if ($userId) {
                $q->where('user_id', $userId);
            }
            if ($userIp) {
                $q->orWhere('user_ip', $userIp);
            }
        });
    }
}
