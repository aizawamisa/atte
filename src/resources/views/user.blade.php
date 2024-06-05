@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endsection

@section('content')
    <div class="header__wrap">
        <p class="header__text">ユーザー一覧</p>
        <p class="header__text-right">{{ $displayDate->format('Y-m-d') }} 現在</p>
    </div>
    <div class="table__wrap">
        <table class="attendance__table">
            <tr class="table__row">
                <th class="table__header">No.</th>
                <th class="table__header">ID</th>
                <th class="table__header">名前</th>
                <th class="table__header">Email</th>
                <th class="table__header">勤務状態</th>
            </tr>
            @php
                $pageNumber = ($users->currentPage() - 1) * $users->perPage() + 1;
            @endphp
            
            @foreach ($users as $user)
                <tr class="table__row">
                    <td class="table__item">{{ $pageNumber }}</td>
                    <td class="table__item">{{ $user->id }}</td>
                    <td class="table__item">{{ $user->name }}</td>
                    <td class="table__item">{{ $user->email }}</td>
                    @if ($user->status == 1)
                        <td class="table__item">勤務中</td>
                    @elseif($user->status == 2)
                        <td class="table__item">休憩中</td>
                    @elseif($user->status == 3)
                        <td class="table__item">退勤</td>
                    @else
                        <td class="table__item">その他</td>
                    @endif
                </tr>
                @php
                    $pageNumber++;
                @endphp
            @endforeach
        </table>
    </div>
    {{ $users->links('vendor/pagination/paginate') }}
@endsection