<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '正しい形式のメールアドレスを入力してください。',
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上である必要があります。',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withErrors([
                'email' => 'こちらのメールアドレスは登録されていません。',
            ])->withInput();
        }
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->intended($this->redirectTo);
        }

        return back()->withErrors([
            'email' => '認証に失敗しました。',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}