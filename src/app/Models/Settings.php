<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'org_link',
        'progress',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'progress' => 'integer',
        'rating' => 'float',
        'reviews_count' => 'integer',
    ];

    protected $attributes = [
        'progress' => 0,
        'rating' => 0.0,
        'reviews_count' => 0,
    ];


    public static function getSettings()
    {
        $settings = self::first();       
        return $settings;
    }

    public static function updateReviewLink(?string $url)
    {
        $settings = self::getSettings();
        $settings->update(['org_link' => $url]);
        return $settings;
    }

    public static function updateProgress(array $data)
    {
        $settings = self::getSettings();

        if(is_array($data)) 
        {
            if(isset($data['total_reviews']))
                $settings->reviews_count = $data['total_reviews'];

            if(isset($data['total_rating']))
                $settings->rating = $data['total_rating'];

            if(isset($data['progress_percent']))
                $settings->progress = $data['progress_percent'];

            $settings->save();
        }

        return $settings;
    }

    public static function getReviewLink(): ?string
    {
        return self::getSettings()->org_link;
    }

    public static function getProgress(): int
    {
        return self::getSettings()->progress ?? 0;
    }
}