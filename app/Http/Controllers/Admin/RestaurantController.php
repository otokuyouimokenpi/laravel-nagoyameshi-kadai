<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class RestaurantController extends Controller
{
    // 店舗一覧ページ
    public function index(Request $request)
    {
        // 検索ボックスに入力されたキーワードを取得
        $keyword = $request->input('keyword');

        // restaurantsテーブルからデータを取得
        $query = Restaurant::query();

        // キーワードが存在する場合は店舗名で部分一致検索を行う
        $restaurants = Restaurant::query()
            ->when($keyword, function($query, $keyword) {
                return $query->where('name', 'like', "%{$keyword}%");
        })
        ->paginate(15);

        return view('admin.restaurants.index', [
            'restaurants' => $restaurants,
            'keyword' => $keyword,
            'total' => $restaurants->total(),
        ]);
    }

    // 店舗詳細ページ
    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact('restaurant'));
    }

    // 店舗登録ページ
    public function create(Request $request)
    {
        $categories = Category::all();

        return view('admin.restaurants.create', compact('categories'));
    }

    // 店舗登録機能
    public function store(Request $request)
    {
        // バリデーションを設定
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|numeric|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

    // フォームの入力内容をもとに、テーブルにデータを追加する
        $restaurant = new Restaurant();
        $restaurant->name = $request->input('name');
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');

    if ($request->hasFile('image')) {
        $image_path = $request->file('image')->store('public/restaurants');
        $restaurant->image_name = basename($image_path);
    } else {
        $restaurant->image = '';
    }

    $restaurant->save();

    $category_ids = array_filter($request->input('category_ids'));
    $restaurant->categories()->sync($category_ids);

    return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
    }

    // 店舗編集ページ
    public function edit(Restaurant $restaurant)
    {

        $categories = Category::all();

        // 設定されたカテゴリのIDを配列化する
        $category_ids = $restaurant->categories->pluck('id')->toArray();

        return view('admin.restaurants.edit', compact('restaurant', 'categories', 'category_ids'));
    }

    // 店舗更新機能
    public function update(Request $request, $id)
    {
        // バリデーションを設定
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|numeric|digits:7',
            'address' => 'required',
            'opening_time' => 'required|date_format:H:i|before:closing_time',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        // IDで既存のレストランを取得
        $restaurant = Restaurant::findOrFail($id);

        $restaurant->name = $request->input('name');
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');

        if($request->hasFile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        }

        $restaurant->update();

        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);


    return redirect()->route('admin.restaurants.show', $restaurant)->with('flash_message', '店舗を編集しました。');
    }

    // 店舗削除機能
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

    return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
}
