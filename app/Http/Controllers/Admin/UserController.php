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
        $total = $query->count();

        return view('admin.user.index', compact('user', 'keyword', 'total'));
    }

    //会員詳細ページ
    public function show(User $user)
    {
        return view('admin.user.show',compact('user'));
    }
}
