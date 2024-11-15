<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    /**
     * カテゴリとの多対多リレーションシップ
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_restaurant');
    }
}
