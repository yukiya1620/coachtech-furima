@extends('layouts.app')

@php($variant = 'simple')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify verify verify--simple-header">
  <div class="verify-card">

    <p class="verify-text">
      登録していただいたメールアドレスに認証メールを送付しました。<br>
      メール認証を完了してください。
    </p>

    @if (session('status') === 'verification-link-sent')
      <p class="verify-success">認証メールを再送しました。</p>
    @endif

    <a class="verify-btn" href="http://localhost:8025" target="_blank" rel="noopener">
      認証はこちらから
    </a>

    <form method="POST" action="{{ route('verification.send') }}" class="verify-resend">
      @csrf
      <button type="submit" class="verify-link">認証メールを再送する</button>
    </form>

  </div>
</div>
@endsection
