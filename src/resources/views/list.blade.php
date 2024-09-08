@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css')}}">
@endsection

@section('link')
<nav class="nav">
    <ul class="nav__inner">
        <li><a href="/">ホーム</a></li>
        <li><a href="/attendance">日付一覧</a></li>
        <li><a href="/list">ユーザー一覧</a></li>
        @guest

        @else

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
    <div class="container list__container">
        <h1 class="content__heading">ユーザー一覧</h1>
        <ul class="list__inner">
            @foreach ($users as $user)
                <li class="list-user">
                    <a href="{{ route('user.show', $user->id) }}">{{ $user->name }}</a>
                </li>
            @endforeach
        </ul>
        <div class="pagination-links">
            {{ $users->links('vendor.pagination.custom') }}
        </div>
    </div>
@endsection
