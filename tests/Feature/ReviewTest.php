<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Review;
use App\Models\Restaurant;

class ReviewTest extends TestCase
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

    // indexアクション（レビュー一覧ページ）
    // 未ログインのユーザーは会員側のレビュー一覧ページにアクセスできない
    public function test_guest_cannot_access_reviews_index()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.index', $restaurant));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_free_user_can_access_reviews_index()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));

        $response->assertStatus(200);
    }

    // ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_paid_user_can_access_reviews_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
    public function test_admin_cannot_access_reviews_index()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.index', $restaurant));

        $response->assertRedirect(route('admin.home'));
    }


    // createアクション（レビュー投稿ページ）
    // 未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
    public function test_guest_cannot_access_reviews_create()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.create', $restaurant));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側のレビュー投稿ページにアクセスできない
    public function test_free_user_cannot_access_reviews_create()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));

        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側のレビュー投稿ページにアクセスできる
    public function test_paid_user_can_access_reviews_create()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のレビュー投稿ページにアクセスできない
    public function test_admin_cannot_access_reviews_create()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.create', $restaurant));

        $response->assertRedirect(route('admin.home'));
    }


    // storeアクション（レビュー投稿機能）
    // 未ログインのユーザーはレビューを投稿できない
    public function test_guest_cannot_store_reviews()
    {
        $restaurant = Restaurant::factory()->create();

        $review_data = [
            'score' => 1,
            'content' => 'テスト'
        ];

        $response = $this->post(route('restaurants.reviews.store', $restaurant), $review_data);

        $this->assertDatabaseMissing('reviews', $review_data);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビューを投稿できない
    public function test_free_user_cannot_store_reviews()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $review_data = [
            'score' => 1,
            'content' => 'テスト'
        ];

        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant), $review_data);

        $this->assertDatabaseMissing('reviews', $review_data);
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はレビューを投稿できる
    public function test_paid_user_can_store_reviews()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();

        $review_data = [
            'score' => 1,
            'content' => 'テスト'
        ];

        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant), $review_data);

        $this->assertDatabaseHas('reviews', $review_data);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // ログイン済みの管理者はレビューを投稿できない
    public function test_admin_cannot_store_reviews()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $review_data = [
            'score' => 1,
            'content' => 'テスト'
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('restaurants.reviews.store', $restaurant), $review_data);

        $this->assertDatabaseMissing('reviews', $review_data);
        $response->assertRedirect(route('admin.home'));
    }


    // editアクション（レビュー編集ページ）
    // 未ログインのユーザーは会員側のレビュー編集ページにアクセスできない
    public function test_guest_cannot_access_reviews_edit()
    {
        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側のレビュー編集ページにアクセスできない
    public function test_free_user_cannot_access_reviews_edit()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));

        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側の他人のレビュー編集ページにアクセスできない
    public function test_paid_user_cannot_access_others_reviews_edit()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');
        $other_user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other_user->id
        ]);

        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));

        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // ログイン済みの有料会員は会員側の自身のレビュー編集ページにアクセスできる
    public function test_paid_user_can_access_own_reviews_edit()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のレビュー編集ページにアクセスできない
    public function test_admin_cannot_access_reviews_edit()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.edit', [$restaurant, $review]));

        $response->assertRedirect(route('admin.home'));
    }


    // updateアクション（レビュー更新機能）
    // 未ログインのユーザーはレビューを更新できない
    public function test_guest_cannot_update_reviews()
    {
        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $old_review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $new_review_data = [
            'score' => 2,
            'content' => 'テスト更新'
        ];

        $response = $this->patch(route('restaurants.reviews.update', [$restaurant, $old_review]), $new_review_data);

        $this->assertDatabaseMissing('reviews', $new_review_data);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビューを更新できない
    public function test_free_user_cannot_update_reviews()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $old_review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $new_review_data = [
            'score' => 2,
            'content' => 'テスト更新'
        ];

       $response = $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $old_review]), $new_review_data);

        $this->assertDatabaseMissing('reviews', $new_review_data);
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は他人のレビューを更新できない
    public function test_paid_user_cannot_update_others_reviews()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');
        $other_user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $old_review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other_user->id
        ]);

        $new_review_data = [
            'score' => 2,
            'content' => 'テスト更新'
        ];

        $response = $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $old_review]), $new_review_data);

        $this->assertDatabaseMissing('reviews', $new_review_data);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // ログイン済みの有料会員は自身のレビューを更新できる
    public function test_paid_user_can_update_own_reviews()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();

        $old_review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $new_review_data = [
            'score' => 2,
            'content' => 'テスト更新'
        ];

        $response = $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $old_review]), $new_review_data);

        $this->assertDatabaseHas('reviews', $new_review_data);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // ログイン済みの管理者はレビューを更新できない
    public function test_admin_cannot_update_reviews()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $old_review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $new_review_data = [
            'score' => 2,
            'content' => 'テスト更新'
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('restaurants.reviews.update', [$restaurant, $old_review]), $new_review_data);

        $this->assertDatabaseMissing('reviews', $new_review_data);
        $response->assertRedirect(route('admin.home'));
    }


    // destroyアクション（レビュー削除機能）
    // 未ログインのユーザーはレビューを削除できない
    public function test_guest_cannot_destroy_reviews()
    {
        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビューを削除できない
    public function test_free_user_cannot_destroy_reviews()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は他人のレビューを削除できない
    public function test_paid_user_cannot_destroy_others_reviews()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');
        $other_user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other_user->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // ログイン済みの有料会員は自身のレビューを削除できる
    public function test_paid_user_can_destroy_own_reviews()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // ログイン済みの管理者はレビューを削除できない
    public function test_admin_cannot_destroy_reviews()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($admin, 'admin')->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('admin.home'));
    }

}
