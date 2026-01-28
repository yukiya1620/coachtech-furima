@extends('layouts.app', ['headerVariant' => 'simple'])

@section('css')
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
  <div class="auth">
    <h1 class="auth-title">会員登録</h1>

    <form method="POST" action="{{ route('register') }}" novalidate>
      @csrf
      
      <x-form-errors />
      <div class="form-group">
        <label class="form-label">ユーザー名</label>
        <input class="form-input" type="text" name="name" value="{{ old('name') }}">
      </div>

      <div class="form-group">
        <label class="form-label">メールアドレス</label>
        <input class="form-input" type="email" name="email" value="{{ old('email') }}">
      </div>

      <div class="form-group">
        <label class="form-label">パスワード</label>
        <input class="form-input" type="password" name="password">
      </div>

      <div class="form-group">
        <label class="form-label">確認用パスワード</label>
        <input class="form-input" type="password" name="password_confirmation">
      </div>

      <button class="btn" type="submit">登録する</button>
    </form>

    <p class="auth-link">
      <a href="{{ route('login') }}">ログインはこちら</a>
    </p>
  </div>
@endsection
