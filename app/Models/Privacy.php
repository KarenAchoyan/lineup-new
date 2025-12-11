<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privacy extends Model
{
    use HasFactory;

    protected $table = 'privacy';

    protected $fillable = [
        'content_en',
        'content_hy',
        'content_ge',
        'content_ru',
    ];

    /**
     * Get privacy content based on locale
     */
    public function getContent($locale = 'en')
    {
        return $this->{"content_{$locale}"} ?? $this->content_en ?? '';
    }

    /**
     * Get or create the single privacy record
     */
    public static function getPrivacy()
    {
        return static::firstOrCreate([]);
    }
}
