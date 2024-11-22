<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Restaurant extends Model
{
    use HasFactory, Sortable;

    /**
     * カテゴリとの多対多リレーションシップ
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_restaurant');
    }

    /**
     ＊店舗定休日との多対多リレーションシップ
     */
    public function regular_holidays()
    {
        return $this->belongsToMany(RegularHoliday::class, 'regular_holiday_restaurant');
    }

    // レビューとの1対多リレーションシップ
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function ratingSortable($query, $direction) {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }
}
