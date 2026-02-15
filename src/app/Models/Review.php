<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_name',
        'rating',
        'review_id',
        'text',
        'review_created_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'review_created_at' => 'datetime',
    ];

    public function setRatingAttribute($value)
    {
        $this->attributes['rating'] = min(5, max(1, (int) $value));
    }

    public function scopeHighRating($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeLowRating($query)
    {
        return $query->where('rating', '<=', 2);
    }

    public function getExcerptAttribute($length = 100)
    {
        return strlen($this->text) > $length 
            ? substr($this->text, 0, $length) . '...' 
            : $this->text;
    }
}