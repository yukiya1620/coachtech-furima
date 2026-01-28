@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="container purchase-address">

  <h1 class="page-title">住所の変更</h1>

  <form method="POST" action="{{ route('purchase.address.update', $item->id) }}">
    @csrf

    <div class="block">
      <label class="label">郵便番号</label>
      <input
        type="text"
        name="postal_code"
        value="{{ old('postal_code', $shipping['postal_code'] ?? '') }}"
        class="input"
      >
      @error('postal_code')
        <p class="error">{{ $message }}</p>
      @enderror
    </div>

    <div class="block">
      <label class="label">住所</label>
      <input
        type="text"
        name="address"
        value="{{ old('address', $shipping['address'] ?? '') }}"
        class="input"
      >
      @error('address')
        <p class="error">{{ $message }}</p>
      @enderror
    </div>

    <div class="block">
      <label class="label">建物名</label>
      <input
        type="text"
        name="building"
        value="{{ old('building', $shipping['building'] ?? '') }}"
        class="input"
      >
    </div>

    <button class="btn-primary" type="submit">
      更新する
    </button>
  </form>

</div>
@endsection
