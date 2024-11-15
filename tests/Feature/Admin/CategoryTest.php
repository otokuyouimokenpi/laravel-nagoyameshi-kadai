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

class CategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // indexアクション（カテゴリ一覧ページ）
    // 未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_categories_index()
    {

        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));

    }

    // ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_regular_user_cannot_access_admin_categories_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_admin_can_access_admin_categories_index()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }

    // storeアクション（カテゴリ登録機能）
    // 未ログインのユーザーはカテゴリを登録できない
    public function test_guest_cannot_store_categories()
    {

        $category_data = [
            'name' => 'テスト',
        ];

        $response = $this->post(route('admin.categories.store'), $category_data);

        $response->assertRedirect(route('admin.login'));

    }

    // ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_regular_user_cannot_store_categories()
    {
        $user = User::factory()->create();

        $category_data = [
            'name' => 'テスト',
        ];

        $this->actingAs($user);

        $response = $this->post(route('admin.categories.store'), $category_data);

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを登録できる
    public function test_admin_can_store_categories()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $category_data = [
            'name' => 'テスト',
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), $category_data);

        $response->assertRedirect(route('admin.categories.index'));
    }

    // updateアクション（カテゴリ更新機能）
    // 未ログインのユーザーはカテゴリを更新できない
    public function test_guest_cannot_update_categories()
    {
        $category = Category::factory()->create();

        $response = $this->patch("/admin/categories/{$category->id}", [
            'name' => 'Test',
        ]);

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_regular_user_cannot_update_categories()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $this->actingAs($user);

        $response = $this->patch("/admin/categories/{$category->id}", [
            'name' => 'Test',
        ]);

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを更新できる
    public function test_admin_can_update_categories()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'admin');

        $category = Category::factory()->create();

        $new_category_data = [
            'name' => '新しいカテゴリ',
        ];

        $response = $this->patch(route('admin.categories.update', $category->id), $new_category_data);

        $this->assertDatabaseHas('categories', $new_category_data);

        $response->assertRedirect(route('admin.categories.index'));
    }

    // destroyアクション（カテゴリ削除機能）
    // 未ログインのユーザーはカテゴリを削除できない
    public function test_guest_cannot_destroy_categories()
    {
        $category = Category::factory()->create();

        $response = $this->delete('/admin/categories/{$category->id}');

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_regular_user_cannot_destroy_categories()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $this->actingAs($user);

        $response = $this->delete('/admin/categories/{$category->id}');

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを削除できる
    public function test_admin_can_destroy_categories()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete("/admin/categories/{$category->id}");

        $response->assertRedirect(route('admin.categories.index'));
    }

}
