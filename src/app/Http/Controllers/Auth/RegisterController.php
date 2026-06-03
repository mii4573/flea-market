<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 「新規ユーザーが登録された（だから認証メールを送って）」と通知する
        event(new Registered($user));

        Auth::login($user);

        // プロフィール画面ではなく、まずは「メール確認誘導画面」へリダイレクト
        return redirect()->route('verification.notice');
    }
}
