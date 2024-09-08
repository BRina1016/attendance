@extends('layouts.app')

@section('css')
<form method="POST" action="/login" novalidate>
<link rel="stylesheet" href="{{ asset('css/login.css')}}">
@endsection

@section('content')
<div class="login">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="login__inner">
                <h2 class="login__heading content__heading">ログイン</h2>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <div class="row mb-3 login__group">
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="メールアドレス">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 login__group">
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="パスワード">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary login__btn btn">
                                    {{ __('ログイン') }}
                                </button>
                            </div>
                        </div>
                        <div class="login__bottom">
                            <p class="login__text">アカウントをお持ちでない方はこちらから<br><a class="login__text-link" href="/register">会員登録</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
