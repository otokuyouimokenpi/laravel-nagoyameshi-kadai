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

    // 予約との1対多リレーションシップ
    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    // ユーザーとの多対多リレーションシップ
    public function favorite_users() {
        return $this->belongsToMany(User::class);
    }

    public function ratingSortable($query, $direction) {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }

    public function popularSortable($query, $direction) {
        return $query->withCount('reservations')->orderBy('reservations_count', $direction);
    }
}
