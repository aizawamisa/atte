@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
    <form class="header__wrap" action="{{ route('per/date') }}" method="post">
        @csrf
        <button class="date__change-button" name="prevDate"><</button>
        <input type="hidden" name="displayDate" value="{{ $displayDate }}">
        <p class="header__text">{{ $displayDate }}</p>
        <button class="date__change-button" name="nextDate">></button>
    </form>

    <div class="table__wrap">
        <table class="attendance__table">
            <tr class="table__row">
                <th class="table__header">名前</th>
                <th class="table__header">勤務開始</th>
                <th class="table__header">勤務終了</th>
                <th class="table__header">休憩時間</th>
                <th class="table__header">勤務時間</th>
            </tr>
            @foreach ($attendances as $attendance)
                <tr class="table__row">
                    <td class="table__item">{{ $attendance->name }}</td>
                    <td class="table__item">{{ $attendance->start_work->format('Y-m-d H:i:s') }}</td>
                    <td class="table__item">{{ $attendance->end_work->format('Y-m-d H:i:s') }}</td>
                    <td class="table__item">{{ $attendance->total_rest }}分</td>
                    <td class="table__item">{{ $attendance->total_work }}分</td>
                </tr>
            @endforeach
        </table>
    </div>

    {{ $attendances->links() }} 
@endsection
