<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class RestaurantController extends Controller
{
    // indexアクション（店舗一覧ページ）
    public function index(Request $request)
    {
        // 検索ボックスに入力されたキーワードを取得
        $keyword = $request->input('keyword');

        // 選択されたカテゴリのIDを取得
        $category_id = $request->input('category_id');

        // 選択された予算を取得
        $price = $request->input('price');

        // 並べ替えのセレクトボックスに表示するテキストをキー、カラム名と並び順を値に指定した配列
        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc',
            '評価が高い順' => 'rating desc',
            '予約数が多い順' => 'popular desc'
        ];

        $sort_query = [];
        $sorted = "created_at desc";

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        // 変数$keywordが存在する場合
        if ($keyword) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")
                ->orWhere('address', 'like', "%{$keyword}%")
                ->orWhereHas('categories', function ($query) use ($keyword) {
                    $query->where('categories.name', 'like', "%{$keyword}%");
                })
                ->sortable($sort_query)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

         // 変数$category_idが存在する場合
        } elseif ($category_id) {
            $restaurants = Restaurant::whereHas('categories', function ($query) use ($category_id) {
                $query->where('categories.id', $category_id);
            })->sortable($sort_query)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

         // 変数$priceが存在する場合
        } elseif ($price) {
            $restaurants = Restaurant::where('lowest_price', '<=', $price)
            ->sortable($sort_query)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

         // それ以外の場合
        } else {
            $restaurants = Restaurant::sortable($sort_query)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        }

        $categories = Category::all();

        $total = $restaurants->total();

        return view('restaurants.index', compact(
            'keyword',
            'category_id',
            'price',
            'sorts',
            'sorted',
            'restaurants',
            'categories',
            'total'
        ));
    }

    // showアクション（店舗詳細ページ）
    public function show(Restaurant $restaurant)
    {
        return view('restaurants.show', compact('restaurant'));
    }
}
