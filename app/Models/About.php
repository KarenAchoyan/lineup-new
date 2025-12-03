<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $table = 'about';

    protected $fillable = [
        'about_en',
        'about_hy',
        'about_ge',
        'about_ru',
        'image',
    ];

    /**
     * Get about content based on locale
     */
    public function getContent($locale = 'en')
    {
        return $this->{"about_{$locale}"} ?? $this->about_en ?? '';
    }

    /**
     * Get or create the single about record
     */
    public static function getAbout()
    {
        return static::firstOrCreate([]);
    }
}
