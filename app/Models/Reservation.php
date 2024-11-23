<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // 店舗とのリレーションシップ
    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }

     // ユーザーとのリレーションシップ
    public function user() {
        return $this->belongsTo(User::class);
    }
}
