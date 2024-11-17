<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Term;

class TermTest extends TestCase
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

    // indexアクション（利用規約ページ）
    // 未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_guest_cannot_access_admin_terms_index()
    {
        $response = $this->get('/admin/terms');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_regular_user_cannot_access_admin_terms_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/terms');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_admin_can_access_admin_terms_index()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $term = Term::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/terms');

        $response->assertStatus(200);
    }

    // editアクション（利用規約編集ページ）
    // 未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_guest_cannot_access_admin_terms_edit()
    {
        $response = $this->get('/admin/terms/{$terms->id}/edit');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_regular_user_cannot_access_admin_terms_edit()
    {
        $user = User::factory()->create();

        $term = Term::factory()->create();

        $response = $this->actingAs($user)->get('/admin/terms/{$terms->id}/edit');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
    public function test_admin_can_access_admin_terms_edit()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $term = Term::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.terms.edit', $term));

        $response->assertStatus(200);
    }

    // updateアクション（利用規約更新機能）
    // 未ログインのユーザーは利用規約を更新できない
    public function test_guest_cannot_update_terms()
    {
        $old_term = Term::factory()->create();

        $new_term_data = [
            'content' => 'テスト更新',
        ];

        $response = $this->patch(route('admin.terms.update', $old_term), $new_term_data);

        $this->assertDatabaseMissing('terms', $new_term_data);

        $response->assertRedirect(route('admin.login'));

    }

    // ログイン済みの一般ユーザーは利用規約を更新できない
    public function test_regular_user_cannot_update_terms()
    {
        $user = User::factory()->create();

        $old_term = Term::factory()->create();

        $new_term_data = [
            'content' => 'テスト更新',
        ];

        $response = $this->actingAs($user)->patch(route('admin.terms.update', $old_term), $new_term_data);

        $this->assertDatabaseMissing('terms', $new_term_data);

        $response->assertRedirect('/admin/login');

    }

    // ログイン済みの管理者は利用規約を更新できる
    public function test_admin_can_update_terms()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_term = Term::factory()->create();

        $new_term_data = [
            'content' => 'テスト更新',
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.terms.update', $old_term), $new_term_data);

        $this->assertDatabaseHas('terms', $new_term_data);

        $response->assertRedirect(route('admin.terms.index', $old_term));
    }

}
