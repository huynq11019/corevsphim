<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_id',
        'user_id',
        'ip_address',
        'type'
    ];

    public $timestamps = false;
    protected $dates = ['created_at'];

    // Relationships
    public function short()
    {
        return $this->belongsTo(Short::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Boot method to set created_at
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($interaction) {
            $interaction->created_at = now();
        });
    }
}
