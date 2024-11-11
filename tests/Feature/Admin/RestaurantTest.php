<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // indexアクション（店舗一覧ページ）
    //未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_index()
    {
        $response = $this->get('/admin/restaurants/index');
        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_regular_user_cannot_access_admin_restaurants_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/restaurants/index');
        $response->assertStatus(403);
    }

    // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_index()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin');
        $response = $this->get('/admin/restaurants/index');
        $response->assertStatus(200);
    }

    // showアクション（店舗詳細ページ）
    // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get("/admin/restaurants/show/{$restaurant->id}");
        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_regular_user_cannot_access_admin_restaurants_show()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->get("/admin/restaurants/show/{$restaurant->id}");
        $response->assertStatus(403);
    }

    // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_show()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin');

        $restaurant = Restaurant::factory()->create();
        $response = $this->get("/admin/restaurants/show/{$restaurant->id}");
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
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/restaurants/create');
        $response->assertStatus(403);
    }

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_create()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin');
        $response = $this->get('/admin/restaurants/create');
        $response->assertStatus(200);
    }

    // storeアクション（店舗登録機能）
    // 未ログインのユーザーは店舗を登録できない
    public function test_guest_cannot_store_restaurant()
    {
        $response = $this->post('/admin/restaurants/store', [
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

        $response->assertRedirect(route('admin.restaurants.index'));
    }

    // ログイン済みの一般ユーザーは店舗を登録できない
    public function test_regular_user_cannot_store_restaurant()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 一般ユーザーが店舗登録を試みる
        $response = $this->post('/admin/restaurants/store', [
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

        $response->assertStatus(403);
    }

    // ログイン済みの管理者は店舗を登録できる
    public function test_admin_can_store_restaurant()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin');

        // 店舗登録データ
        $restaurantData = [
            'name' => 'Test',
            'description' => 'test',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => 'test',
            'opening_time' => '09:00',
            'closing_time' => '18:00',
            'seating_capacity' => 50,
        ];

        $response = $this->post('/admin/restaurants/store', $restaurantData);

        // データベースに新しい店舗が登録されたかを確認
        $this->assertDatabaseHas('restaurants', [
            'name' => 'Test',
            'description' => 'test',
        ]);

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
        $this->actingAs($user);

        $response = $this->get('/admin/restaurants/edit');
        $response->assertRedirect('/admin/login');
        $response->assertStatus(403);
    }

    // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_edit()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin');
        $response = $this->get('/admin/restaurants/edit');
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

        $response->assertStatus(403);
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
        ];

        $response = $this->patch(route('admin.restaurants.update', $restaurant->id), $new_restaurant);

        $this->assertDatabaseHas('restaurants', $new_restaurant);

        $response->assertRedirect(route('admin.restaurants.show', $restaurant->id))->with('flash_message', '店舗を編集しました。');
        $response->assertStatus(302);
    }

    // destroyアクション（店舗削除機能）
    // 未ログインのユーザーは店舗を削除できない
    public function test_guest_cannot_destroy_restaurant()
    {
        // テスト用の店舗を作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete("/admin/restaurants/{$restaurant->id}");

        $response->assertRedirect(route('admin.restaurants.index'));
    }

    // ログイン済みの一般ユーザーは店舗を削除できない
    public function test_regular_user_cannot_destroy_restaurant()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // テスト用の店舗を作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete("/admin/restaurants/{$restaurant->id}");

        $response->assertStatus(403);
    }

    // ログイン済みの管理者は店舗を削除できる
    public function test_admin_can_destroy_restaurant()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'admin');

        // テスト用の店舗を作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete("/admin/restaurants/{$restaurant->id}");

        // データベースから店舗が削除されているかを確認
        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);

        $response->assertRedirect(route('admin.restaurants.index'))->with('flash_message', '店舗を削除しました。');
        $response->assertStatus(302);
    }
}
