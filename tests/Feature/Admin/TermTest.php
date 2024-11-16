<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
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


    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない


    // ログイン済みの管理者は管理者側の利用規約ページにアクセスできる


    // editアクション（利用規約編集ページ）
    // 未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない


    // ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない


    // ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる


    // updateアクション（利用規約更新機能）
    // 未ログインのユーザーは利用規約を更新できない


    // ログイン済みの一般ユーザーは利用規約を更新できない


    // ログイン済みの管理者は利用規約を更新できる

}
