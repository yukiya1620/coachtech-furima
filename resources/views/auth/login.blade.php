@extends('layouts.app', ['headerVariant' => 'simple'])

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="auth">
  <h1 class="auth-title">ログイン</h1>

  {{-- エラーメッセージ --}}
  <x-form-errors />

  <form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    <div class="form-group">
      <label class="form-label">メールアドレス</label>
      <input
        class="form-input"
        type="email"
        name="email"
        value="{{ old('email') }}"
      >
    </div>

    <div class="form-group">
      <label class="form-label">パスワード</label>
      <input
        class="form-input"
        type="password"
        name="password"
      >
    </div>

    <button class="btn" type="submit">ログインする</button>
  </form>

  <p class="auth-link">
    <a href="{{ route('register') }}">会員登録はこちら</a>
  </p>
</div>
@endsection
