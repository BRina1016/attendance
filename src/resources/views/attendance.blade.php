@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
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
<div class="attendance__inner">
    <div class="attendance__date">
        <a href="{{ route('attendance.search', ['date' => $prevDate]) }}" class="attendance__arrow">&lt;</a>
        <span>{{ $currentDate }}</span>
        <a href="{{ route('attendance.search', ['date' => $nextDate]) }}" class="attendance__arrow">&gt;</a>
    </div>
    <div class="attendance-table">
        <table>
            <thead>
                <tr class="attendance-list">
                    <th class="attendance-list__item">名前</th>
                    <th class="attendance-list__item">勤務開始</th>
                    <th class="attendance-list__item">勤務終了</th>
                    <th class="attendance-list__item">休憩時間</th>
                    <th class="attendance-list__item">勤務時間</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stamps as $stamp)
                <tr class="attendance-list">
                    <td class="attendance-list__item">{{ $stamp->user->name }}</td>
                    <td class="attendance-list__item">{{ \Carbon\Carbon::parse($stamp->clock_in)->format('H:i') }}</td>
                    <td class="attendance-list__item">{{ \Carbon\Carbon::parse($stamp->clock_out)->format('H:i') }}</td>
                    <td class="attendance-list__item">
                        @php
                            $totalRestSeconds = 0;
                            foreach ($stamp->rests as $rest) {
                                $totalRestSeconds += strtotime($rest->rest_time) - strtotime('TODAY');
                            }
                        @endphp
                        {{ gmdate('H:i', $totalRestSeconds) }}
                    </td>
                    <td class="attendance-list__item">{{ $stamp->work_time }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
