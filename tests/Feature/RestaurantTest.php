<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Restaurant;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // indexアクション（店舗一覧ページ）
    // 未ログインのユーザーは会員側の店舗一覧ページにアクセスできる
    public function test_guest_can_access_restaurant_index()
    {
        $response = $this->get('/restaurants');

        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側の店舗一覧ページにアクセスできる
    public function test_regular_user_can_access_restaurant_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/restaurants');

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の店舗一覧ページにアクセスできない
    public function test_admin_cannot_access_restaurant_index()
    {
    // 既存の admin@example.com を削除
    Admin::where('email', 'admin@example.com')->delete();

    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $response = $this->actingAs($admin, 'admin')->get('/restaurants');

    $response->assertRedirect('/admin/home');
    }

    // indexアクション（店舗一覧ページ）
    // 未ログインのユーザーは会員側の店舗詳細ページにアクセスできる
    public function test_guest_can_access_restaurant_show()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get("/restaurants/{$restaurant->id}");

        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側の店舗詳細ページにアクセスできる
    public function test_regular_user_can_access_restaurant_show()
    {
        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/restaurants/{$restaurant->id}");

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の店舗詳細ページにアクセスできない
    public function test_admin_cannot_access_restaurant_show()
    {
    // 既存の admin@example.com を削除
    Admin::where('email', 'admin@example.com')->delete();

    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $restaurant = Restaurant::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get("/restaurants/{$restaurant->id}");

    $response->assertRedirect('/admin/home');
    }

}
