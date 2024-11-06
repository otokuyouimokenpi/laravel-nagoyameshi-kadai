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
        $query = User::query();

        if (!empty($keyword)) {
            $query->where(function($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('kana', 'LIKE', "%{$keyword}%");
            });
        }

        $users = $query->paginate(10);

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
