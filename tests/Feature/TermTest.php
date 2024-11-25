<?php

namespace Tests\Feature;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Term;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
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

    // 未ログインのユーザーは会員側の利用規約ページにアクセスできる
    public function test_guest_can_access_terms_index()
    {
        $term = Term::factory()->create();

        $response = $this->get(route('terms.index'));

        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側の利用規約ページにアクセスできる
    public function test_regular_user_can_access_terms_index()
    {
        $user = User::factory()->create();

        $term = Term::factory()->create();

        $response = $this->actingAs($user)->get(route('terms.index'));

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の利用規約ページにアクセスできない
    public function test_admin_cannot_access_terms_index()
    {
    // 既存の admin@example.com を削除
    Admin::where('email', 'admin@example.com')->delete();

    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $term = Term::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('terms.index'));

    $response->assertRedirect(route('admin.home'));
    }
}