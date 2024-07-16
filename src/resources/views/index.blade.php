@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
<script src="{{ asset('js/Stamp.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('link')
<nav class="nav">
    <ul class="nav__inner">
        <li><a href="/">ホーム</a></li>
        <li><a href="/attendance">日付一覧</a></li>
        @guest
            <!-- Show Login/Register Links -->
        @else
            <!-- Show Logout Link -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                    {{ __('ログアウト') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        @endguest
    </ul>
</nav>
@endsection

@section('content')
<div class="stamp">
    <div class="stamp__inner">
        <h2 class="stamp__heading content__heading">さんお疲れ様です！</h2>
        <button id="clock_in" class="btn__on stamp-form__btn">勤務開始</button>
        <button id="clock_out" class="btn__off stamp-form__btn" disabled>勤務終了</button>
        <button id="rest_start" class="btn__off stamp-form__btn" disabled>休憩開始</button>
        <button id="rest_end" class="btn__off stamp-form__btn" disabled>休憩終了</button>
    </div>
</div>
@endsection
