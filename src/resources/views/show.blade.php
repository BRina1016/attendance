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
    <h1 class="content__heading">{{ $user->name }}</h1>
    <div class="month-navigation">
        <a href="{{ route('user.show', ['user' => $user->id, 'year' => $previousMonth->year, 'month' => $previousMonth->month]) }}" class="list__arrow">&lt;</a>
        <span>{{ $month }}月</span>
        <a href="{{ route('user.show', ['user' => $user->id, 'year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="list__arrow">&gt;</a>
    </div>

    <h2 class="show__heading">{{ \Carbon\Carbon::create($year, $month)->format('n') }}月の勤務情報</h2>
    <div class="show__inner">
        <table>
        <thead>
            <tr class="show-list">
                <th class="show-list__item">日付</th>
                <th class="show-list__item">勤務開始</th>
                <th class="show-list__item">勤務終了</th>
                <th class="show-list__item">休憩時間</th>
                <th class="show-list__item">勤務時間</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($stamps as $stamp)
        <tr class="show-list">
            <td class="show-list__item">{{ \Carbon\Carbon::parse($stamp->clock_in)->format('Y-m-d') }}</td>
            <td class="show-list__item">{{ \Carbon\Carbon::parse($stamp->clock_in)->format('H:i:s') }}</td>
            <td class="show-list__item">{{ $stamp->clock_out ? \Carbon\Carbon::parse($stamp->clock_out)->format('H:i:s') : 'N/A' }}</td>
            <td class="show-list__item">
                @php
                    $totalRestSeconds = 0;
                    foreach ($stamp->rests as $rest) {
                        $totalRestSeconds += strtotime($rest->rest_time) - strtotime('TODAY');
                    }
                @endphp
                {{ gmdate('H:i:s', $totalRestSeconds) }}
            </td>
            <td class="show-list__item">{{ $stamp->work_time }}</td>
        </tr>
        @endforeach
        </tbody>
        </table>
        <div class="pagination-links">
            {{ $stamps->links('vendor.pagination.custom') }}
        </div>
    </div>
@endsection
