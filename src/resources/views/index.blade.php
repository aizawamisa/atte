@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <style>
        .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    <div class="header__wrap">
        <p class="header__text">
            {{ \Auth::user()->name }}さんお疲れ様です！
        </p>
    </div>

    <form class="form__wrap" method="post">
        @csrf
        <div class="form__item">
            <button class="form__item-button @if($status != 0) disabled @endif" type="submit" formaction="{{ route('startWork') }}">勤務開始</button>
        </div>
        <div class="form__item">
            <button class="form__item-button @if($status != 1) disabled @endif" type="submit" formaction="{{ route('endWork') }}">勤務終了</button>
        </div>
        <div class="form__item">
            <button class="form__item-button @if($status != 1) disabled @endif" type="submit" formaction="{{ route('startRest') }}">休憩開始</button>
        </div>
        <div class="form__item">
            <button class="form__item-button @if($status != 2) disabled @endif" type="submit" formaction="{{ route('endRest') }}">休憩終了</button>
        </div>
    </form>
@endsection
