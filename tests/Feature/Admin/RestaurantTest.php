<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;

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
    //未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_index()
    {
        $response = $this->get('/admin/restaurants');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_regular_user_cannot_access_admin_restaurants_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/restaurants');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_index()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants');

        $response->assertStatus(200);
    }

    // showアクション（店舗詳細ページ）
    // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get("/admin/restaurants/{$restaurant->id}");

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_regular_user_cannot_access_admin_restaurants_show()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get("/admin/restaurants/{$restaurant->id}");

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_show()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show', $restaurant));

        $response->assertStatus(200);
    }

    // createアクション（店舗登録ページ）
    // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_create()
    {
        $response = $this->get('/admin/restaurants/create');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_regular_user_cannot_access_admin_restaurants_create()
    {
        $response = $this->get('/admin/restaurants/create');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_create()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/create');

        $response->assertStatus(200);
    }

    // storeアクション（店舗登録機能）
    // 未ログインのユーザーは店舗を登録できない
    public function test_guest_cannot_store_restaurant()
    {
        $restaurant = Restaurant::factory()->create()->make()->toArray();

        $response = $this->post('/admin/restaurants', $restaurant);

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは店舗を登録できない
    public function test_regular_user_cannot_store_restaurant()
    {
        $restaurant = Restaurant::factory()->create()->make()->toArray();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/restaurants', $restaurant);

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は店舗を登録できる
    public function test_admin_can_store_restaurant()
    {
        $restaurant = Restaurant::factory()->make()->toArray();

        $admin = User::factory()->create(['is_admin' => true]);

        // 店舗にカテゴリを設定
        $categories = Category::factory()->count(3)->create();

        $category_ids = $categories->pluck('id')->toArray();

        $restaurant['category_ids'] = $category_ids;

        // 店舗に定休日を設定
        $regular_holiday = RegularHoliday::factory()->count(3)->create();

        $regular_holiday_ids = $regular_holiday->pluck('id')->toArray();

        $restaurant['regular_holiday_ids'] = $regular_holiday_ids;

        $response = $this->actingAs($admin, 'admin')->post('/admin/restaurants', $restaurant);

        $response->assertRedirect(route('admin.restaurants.index'))->with('flash_message', '店舗を登録しました。');
    }

    // editアクション（店舗編集ページ）
    // 未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_edit()
    {
        $response = $this->get('/admin/restaurants/edit');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_regular_user_cannot_access_admin_restaurants_edit()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get('/admin/restaurants/{$restaurant->id}/edit');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_edit()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit', $restaurant));

        $response->assertStatus(200);
    }

    // updateアクション（店舗更新機能）
    // 未ログインのユーザーは店舗を更新できない
    public function test_guest_cannot_update_restaurant()
    {
        // テスト用の店舗を作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->patch("/admin/restaurants/{$restaurant->id}", [
            'name' => 'Test',
            'description' => 'test',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => 'test',
            'opening_time' => '09:00',
            'closing_time' => '18:00',
            'seating_capacity' => 50,
        ]);

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは店舗を更新できない
    public function test_regular_user_cannot_update_restaurant()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // テスト用の店舗を作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->patch("/admin/restaurants/{$restaurant->id}", [
            'name' => 'Test',
            'description' => 'test',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => 'test',
            'opening_time' => '09:00',
            'closing_time' => '18:00',
            'seating_capacity' => 50,
        ]);

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は店舗を更新できる
    public function test_admin_can_update_restaurant()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'admin');

        // テスト用の店舗を作成
        $restaurant = Restaurant::factory()->create();

        // 作成した店舗をIDで取得
        $restaurant = Restaurant::find($restaurant->id);

        // 店舗にカテゴリを設定
        $categories = Category::factory()->count(3)->create();

        $category_ids = $categories->pluck('id')->toArray();

        $restaurant['category_ids'] = $category_ids;

        // 店舗に定休日を設定
        $regular_holiday = RegularHoliday::factory()->count(3)->create();

        $regular_holiday_ids = $regular_holiday->pluck('id')->toArray();

        $restaurant['regular_holiday_ids'] = $regular_holiday_ids;

        // 店舗更新データ
        $new_restaurant = [
            'name' => 'Test',
            'description' => 'test',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => 'test',
            'opening_time' => '09:00',
            'closing_time' => '18:00',
            'seating_capacity' => 50,
            'category_ids' => $category_ids,
            'regular_holiday_ids' => $regular_holiday_ids,
        ];

        $response = $this->patch(route('admin.restaurants.update', $restaurant->id), $new_restaurant);

        $response->assertRedirect(route('admin.restaurants.show', $restaurant->id))->with('flash_message', '店舗を編集しました。');

        $response->assertStatus(302);
    }

    // destroyアクション（店舗削除機能）
    // 未ログインのユーザーは店舗を削除できない
    public function test_guest_cannot_destroy_restaurant()
    {
        // テスト用の店舗を作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete('/admin/restaurants/{$restaurant->id}');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは店舗を削除できない
    public function test_regular_user_cannot_destroy_restaurant()
    {
        $user = User::factory()->create();

        // テスト用の店舗を作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->delete('/admin/restaurants/{$restaurant->id}');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は店舗を削除できる
    public function test_admin_can_destroy_restaurant()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // テスト用の店舗を作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete("/admin/restaurants/{$restaurant->id}");

        // データベースから店舗が削除されているかを確認
        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);

        $response->assertRedirect(route('admin.restaurants.index'))->with('flash_message', '店舗を削除しました。');

        $response->assertStatus(302);
    }
}
