<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';

    protected $fillable = [
        'image',
        'about_dancing_en',
        'dancing_en',
        'about_dancing_hy',
        'dancing_hy',
        'about_dancing_ge',
        'dancing_ge',
        'about_dancing_ru',
        'dancing_ru',
    ];

    /**
     * Get or create the single banner record
     */
    public static function getBanner()
    {
        return static::firstOrCreate([]);
    }
}






