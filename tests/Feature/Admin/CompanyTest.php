<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // indexアクション（会社概要ページ）
    // 未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_cannot_access_admin_company_index()
    {
        $response = $this->get('/admin/restaurants');

        $response->assertRedirect('/admin/login');
    }


    // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない


    // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる


    // editアクション（会社概要編集ページ）
    // 未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない


    // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない


    // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる


    // updateアクション（会社概要更新機能）
    // 未ログインのユーザーは会社概要を更新できない


    // ログイン済みの一般ユーザーは会社概要を更新できない


    // ログイン済みの管理者は会社概要を更新できる


}
