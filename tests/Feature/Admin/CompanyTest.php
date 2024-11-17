<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;

class CompanyTest extends TestCase
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

    // indexアクション（会社概要ページ）
    // 未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_cannot_access_admin_company_index()
    {
        $response = $this->get('/admin/company');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    public function test_regular_user_cannot_access_admin_company_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/company');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
    public function test_admin_can_access_admin_company_index()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $company = Company::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/company');

        $response->assertStatus(200);
    }

    // editアクション（会社概要編集ページ）
    // 未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_guest_cannot_access_admin_company_edit()
    {
        $response = $this->get('/admin/company/{$company->id}/edit');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_regular_user_cannot_access_admin_company_edit()
    {
        $user = User::factory()->create();

        $company = Company::factory()->create();

        $response = $this->actingAs($user)->get('/admin/company/{$company->id}/edit');

        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
    public function test_admin_can_access_admin_company_edit()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $company = Company::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company));

        $response->assertStatus(200);
    }

    // updateアクション（会社概要更新機能）
    // 未ログインのユーザーは会社概要を更新できない
    public function test_guest_cannot_update_admin_company()
    {
        $company_data = Company::factory()->create();

        $new_company_data = [
            'name' => '更新',
            'postal_code' => '1111111',
            'address' => '更新',
            'representative' => '更新',
            'establishment_date' => '更新',
            'capital' => '更新',
            'business' => '更新',
            'number_of_employees' => '更新',
        ];

        $response = $this->patch(route('admin.company.update', $company_data), $new_company_data);

        $this->assertDatabaseMissing('companies', $new_company_data);

        $response->assertRedirect('/admin/login');

    }

    // ログイン済みの一般ユーザーは会社概要を更新できない
    public function test_regular_user_cannot_update_admin_company()
    {
        $user = User::factory()->create();

        $company_data = Company::factory()->create();

        $new_company_data = [
            'name' => '更新',
            'postal_code' => '1111111',
            'address' => '更新',
            'representative' => '更新',
            'establishment_date' => '更新',
            'capital' => '更新',
            'business' => '更新',
            'number_of_employees' => '更新',
        ];

        $response = $this->actingAs($user)->patch(route('admin.company.update', $company_data), $new_company_data);

        $this->assertDatabaseMissing('companies', $new_company_data);

        $response->assertRedirect('/admin/login');

    }

    // ログイン済みの管理者は会社概要を更新できる
    public function test_admin_can_update_admin_company()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_company = Company::factory()->create();

        $new_company_data = [
            'name' => 'テスト更新',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新',
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.company.update', $old_company), $new_company_data);

        $this->assertDatabaseHas('companies', $new_company_data);

        $response->assertRedirect(route('admin.company.index', $old_company));
    }

}
