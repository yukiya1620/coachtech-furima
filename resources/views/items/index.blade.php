@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endsection

@section('content')

@php
  $query = request()->query();
@endphp

<div class="items-page">
    <x-tabs :tabs="[
      ['label' => 'おすすめ', 
       'href' => url()->current() . '?' . http_build_query(array_merge($query, ['tab' => 'recommend'])), 
       'active' => $tab === 'recommend'],
      ['label' => 'マイリスト', 
       'href' => url()->current() . '?' . http_build_query(array_merge($query, ['tab' => 'mylist'])),
       'active' => $tab === 'mylist'],
    ]" />

  <div class="item-grid">
    @forelse($items as $item)
      <x-item-card :item="$item" :showPrice="true" />
    @empty
      @if($tab === 'mylist' && auth()->guest())
        {{-- 未認証ユーザーのマイリストは何も表示されない --}}
      @else
        <p class="items-empty">
          {{ $tab === 'mylist' ? 'マイリストに商品がありません' : '商品がありません' }}
        </p>
      @endif
    @endforelse
  </div>
</div>
@endsection
