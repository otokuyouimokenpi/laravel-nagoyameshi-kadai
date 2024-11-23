<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationTest extends TestCase
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

    // indexアクション（予約一覧ページ）
    // 未ログインのユーザーは会員側の予約一覧ページにアクセスできない
    public function test_guest_cannot_access_reservations_index()
    {
        $response = $this->get(route('reservations.index'));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側の予約一覧ページにアクセスできない
    public function test_free_user_cannot_access_reservations_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reservations.index'));

        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側の予約一覧ページにアクセスできる
    public function test_paid_user_can_access_reservations_index()
    {
        $user = User::factory()->create();

        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $response = $this->actingAs($user)->get(route('reservations.index'));

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の予約一覧ページにアクセスできない
    public function test_admin_cannot_access_reservations_index()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('reservations.index'));

        $response->assertRedirect(route('admin.home'));
    }


    // createアクション（予約ページ）
    // 未ログインのユーザーは会員側の予約ページにアクセスできない
    public function test_guest_cannot_access_reservations_create()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reservations.create', $restaurant));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側の予約ページにアクセスできない
    public function test_free_user_cannot_access_reservations_create()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', $restaurant));

        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側の予約ページにアクセスできる
    public function test_paid_user_can_access_reservations_create()
    {
        $user = User::factory()->create();

        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', $restaurant));

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の予約ページにアクセスできない
    public function test_admin_cannot_access_reservations_create()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reservations.create', $restaurant));

        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（予約機能）
    // 未ログインのユーザーは予約できない
    public function test_guest_cannot_store_reservations()
    {
        $restaurant = Restaurant::factory()->create();

        $reservation_data = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];

        $response = $this->post(route('restaurants.reservations.store', $restaurant), $reservation_data);

        $this->assertDatabaseMissing('reservations', ['reserved_datetime' => '2024-01-01 00:00', 'number_of_people' => 10]);

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は予約できない
    public function test_free_user_cannot_store_reservations()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $reservation_data = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];

        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservation_data);

        $this->assertDatabaseMissing('reservations', ['reserved_datetime' => '2024-01-01 00:00', 'number_of_people' => 10]);

        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は予約できる
    public function test_paid_user_can_store_reservations()
    {
        $user = User::factory()->create();

        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();

        $reservation_data = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];

        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservation_data);

        $this->assertDatabaseHas('reservations', ['reserved_datetime' => '2024-01-01 00:00', 'number_of_people' => 10]);

        $response->assertRedirect(route('reservations.index'));
    }

    // ログイン済みの管理者は予約できない
    public function test_admin_cannot_store_reservations()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $reservation_data = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('restaurants.reservations.store', $restaurant), $reservation_data);

        $this->assertDatabaseMissing('reservations', ['reserved_datetime' => '2024-01-01 00:00', 'number_of_people' => 10]);

        $response->assertRedirect(route('admin.home'));
    }


    // destroyアクション（予約キャンセル機能）
    public function test_guest_cannot_destroy_reservations()
   {
        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は予約をキャンセルできない
    public function test_free_user_cannot_destroy_reservations()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);

        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は他人の予約をキャンセルできない
    public function test_paid_user_cannot_destroy_others_reservations_()
    {
        $user = User::factory()->create();

        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $other_user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $other_user->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);

        $response->assertRedirect(route('reservations.index'));
    }

    // ログイン済みの有料会員は自身の予約をキャンセルできる
    public function test_paid_user_can_destroy_own_reservations()
    {
        $user = User::factory()->create();

        $user->newSubscription('premium_plan', 'price_1QNWqpRrZ6MH2neTyFK3pIP0')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);

        $response->assertRedirect(route('reservations.index'));
    }

    // ログイン済みの管理者は予約をキャンセルできない
    public function test_admin_cannot_destroy_reservations()
    {
        // 既存の admin@example.com を削除
        Admin::where('email', 'admin@example.com')->delete();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($admin, 'admin')->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);

        $response->assertRedirect(route('admin.home'));
    }
}
