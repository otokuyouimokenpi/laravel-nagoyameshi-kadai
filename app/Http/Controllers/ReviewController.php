<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Review;

class ReviewController extends Controller
{
    // indexアクション（レビュー一覧ページ）
    public function index(Restaurant $restaurant)
    {
        if (Auth::user()->subscribed('premium_plan')) {
            $reviews = Review::where('restaurant_id', $restaurant->id)->orderBy('created_at', 'desc')->paginate(5);
        } else {
            $reviews = Review::where('restaurant_id', $restaurant->id)->orderBy('created_at', 'desc')->take(3)->get();
        }

        return view('reviews.index', compact('restaurant', 'reviews'));
    }

    // createアクション（レビュー投稿ページ）
    public function create(Restaurant $restaurant)
    {
        return view('reviews.create', compact('restaurant'));
    }

    // storeアクション（レビュー投稿機能）
    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required',
        ]);

        $review = new Review();
        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = $restaurant->id;
        $review->user_id = $request->user()->id;
        $review->save();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを投稿しました。');
    }

    // editアクション（レビュー編集ページ）
    public function edit(Restaurant $restaurant, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        return view('reviews.edit', compact('restaurant', 'review'));
    }

    // updateアクション（レビュー更新機能）
    public function update(Request $request, Restaurant $restaurant, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required',
        ]);

        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->save();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを編集しました。');
    }

    // destroyアクション（レビュー削除機能）
    public function destroy(Restaurant $restaurant, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        $review->delete();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを削除しました。');
    }
}
