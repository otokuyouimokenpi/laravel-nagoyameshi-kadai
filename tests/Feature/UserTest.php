<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    use RefreshDatabase;

    // indexアクション（会員情報ページ）
    // 未ログインのユーザーは会員側の会員情報ページにアクセスできない
    public function test_guest_cannot_access_user_index()
    {
        $response = $this->get('/user');

        $response->assertRedirect('/login');
    }

    // ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
    public function test_regular_user_can_access_user_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/user');

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会員情報ページにアクセスできない
    public function test_admin_cannot_access_user_index()
    {
    // 既存の admin@example.com を削除
    Admin::where('email', 'admin@example.com')->delete();

    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $response = $this->actingAs($admin, 'admin')->get('/user');

    $response->assertRedirect('/admin/home');
    }

    // editアクション（会員情報編集ページ）
    // 未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
    public function test_guest_cannot_access_user_edit()
    {
        $response = $this->get('/user/{$user->id}/edit');

        $response->assertRedirect('/login');
    }

    // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
    public function test_regular_user_cannot_access_other_user_edit()
    {
        $user = User::factory()->create();

        // 他のユーザーを作成
        $otheruser = User::factory()->create();

        $response = $this->actingAs($user)->get("/user/{$otheruser->id}/edit");

        $response->assertRedirect('/user');
    }

    // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
    public function test_regular_user_can_access_own_user_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/user/{$user->id}/edit");

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_cannot_access_user_edit()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get("/user/{$user->id}/edit");

        $response->assertRedirect('/admin/home');
    }


    // updateアクション（会員情報更新機能）
    // 未ログインのユーザーは会員情報を更新できない
    public function test_guest_cannot_update_user_edit()
    {
        $old_user_date = User::factory()->create();

        $new_user_data = [
            'name' => '更新する名前',
            'kana' => 'コウシンスルナマエ',
            'email' => 'user@example.com',
            'postal_code' => '2222222',
            'address' => '更新する住所',
            'phone_number' => '12345678910',
            'birthday' => '11111111',
            'occupation' => '更新する職業',
        ];

        $response = $this->patch(route('user.update', $old_user_date), $new_user_data);

        $this->assertDatabaseMissing('users', $new_user_data);

        $response->assertRedirect('/login');
    }

    // ログイン済みの一般ユーザーは他人の会員情報を更新できない
    public function test_regular_user_cannot_update_other_user_edit()
    {
        $user = User::factory()->create();

        // 他のユーザーを作成
        $otheruser = User::factory()->create();

        $old_user_date = User::factory()->create();

        $new_user_data = [
            'name' => '更新する名前',
            'kana' => 'コウシンスルナマエ',
            'email' => 'user@example.com',
            'postal_code' => '2222222',
            'address' => '更新する住所',
            'phone_number' => '12345678910',
            'birthday' => '11111111',
            'occupation' => '更新する職業',
        ];

        $response = $this->actingAs($user)->patch(route('user.update', $old_user_date), $new_user_data);

        $this->assertDatabaseMissing('users', $new_user_data);

        $response->assertRedirect('/user');
    }

    // ログイン済みの一般ユーザーは自身の会員情報を更新できる
    public function test_regular_user_can_update_own_user_edit()
    {
        $user = User::factory()->create();

        $old_user_date = User::factory()->create();

        $new_user_data = [
            'name' => '更新する名前',
            'kana' => 'コウシンスルナマエ',
            'email' => 'user@example.com',
            'postal_code' => '2222222',
            'address' => '更新する住所',
            'phone_number' => '12345678910',
            'birthday' => '11111111',
            'occupation' => '更新する職業',
        ];

        $response = $this->actingAs($user)->patch(route('user.update', $old_user_date), $new_user_data);

        $this->assertDatabaseMissing('users', $new_user_data);

        $response->assertRedirect('/user');
    }


    // ログイン済みの管理者は会員情報を更新できない
    public function test_admin_cannot_update_user_edit()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_user_date = User::factory()->create();

        $new_user_data = [
            'name' => '更新する名前',
            'kana' => 'コウシンスルナマエ',
            'email' => 'user@example.com',
            'postal_code' => '2222222',
            'address' => '更新する住所',
            'phone_number' => '12345678910',
            'birthday' => '11111111',
            'occupation' => '更新する職業',
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('user.update', $old_user_date), $new_user_data);

        $response->assertRedirect('/admin/home');
    }
}
