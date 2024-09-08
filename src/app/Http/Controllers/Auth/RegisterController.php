<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;


class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'regex:/^[ぁ-んァ-ヶ一-龥々ー]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Z])(?=.*\d).+$/'],
        ], [
            'name.required' => '名前を入力してください。',
            'name.regex' => '名前は全角文字で入力してください。',
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '正しい形式のメールアドレスを入力してください。',
            'email.unique' => 'そのメールアドレスはすでに使用されています。',
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上である必要があります。',
            'password.confirmed' => 'パスワードが一致しません。',
            'password.regex' => 'パスワードは1つ以上の大文字と数字を含む必要があります。',
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        auth()->login($user);

        return redirect()->intended('/');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function registered(Request $request, $user)
    {
        $user->sendEmailVerificationNotification();
        return Redirect::route('login')->with('status', '登録が完了しました。メールをご確認ください。');
    }
}