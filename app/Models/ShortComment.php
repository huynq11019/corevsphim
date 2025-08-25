<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_id',
        'user_id',
        'content',
        'parent_id',
        'likes',
        'dislikes',
        'is_anonymous',
        'ip_address'
    ];

    protected $casts = [
        'is_anonymous' => 'boolean'
    ];

    // Relationships
    public function short()
    {
        return $this->belongsTo(Short::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ShortComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ShortComment::class, 'parent_id')->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeParentComments($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    // Methods
    public function incrementLikes()
    {
        $this->increment('likes');
    }

    public function incrementDislikes()
    {
        $this->increment('dislikes');
    }
}
