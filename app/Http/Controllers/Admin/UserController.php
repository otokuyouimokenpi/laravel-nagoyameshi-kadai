<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // 会員一覧ページ
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $users = User::query()
            ->when($keyword, function($query, $keyword) {
                return $query->where('name', 'like', "%{$keyword}%")
                             ->orWhere('kana', 'like', "%{$keyword}%");
            })
            ->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
            'keyword' => $keyword,
            'total' => $users->total(),
        ]);
    }

    //会員詳細ページ
    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user,
        ]);
    }
}
