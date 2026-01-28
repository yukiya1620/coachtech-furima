@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage">

  {{-- プロフィール上部（常に表示） --}}
  <div class="mypage-profile">
    <div class="mypage-profile__left">
      <div class="mypage-avatar">
        @if(!empty($user->profile_image))
          <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_image) }}" alt="avatar">
        @else
          <img src="{{ asset('img/no-image.png') }}" alt="avatar">
        @endif
      </div>
      <div class="mypage-username">{{ $user->name }}</div>
    </div>

    <a href="{{ route('profile.edit') }}" class="mypage-profile__edit">
      プロフィールを編集
    </a>
  </div>

  {{-- タブ（2つだけ） --}}
  <x-tabs :tabs="[
    ['label' => '出品した商品', 'href' => route('mypage.index', ['page' => 'sell']), 'active' => $mode === 'sell'],
    ['label' => '購入した商品', 'href' => route('mypage.index', ['page' => 'buy']), 'active' => $mode === 'buy'],
  ]" />

  <div class="mypage-divider"></div>

  {{-- 中身 --}}
  <section class="mypage-section">
    @if ($mode === 'buy')

      @if(count($items) === 0)
        <p class="mypage-empty">購入した商品はありません</p>
      @else
        <div class="mypage-items">
          @foreach($items as $item)
            <x-item-card
              :item="$item"
              :showPrice="false"
              :showSold="true"
              imageClass="mypage-item__thumb"
              infoClass="mypage-item__info"
            />
          @endforeach
        </div>
      @endif

    @else

      @if(count($items) === 0)
        <p class="mypage-empty">出品した商品はありません</p>
      @else
        <div class="mypage-items">
          @foreach($items as $item)
            <a class="mypage-item" href="{{ route('items.show', $item->id) }}">
              <div class="mypage-item__thumb">
                <img
                  src="{{ $item->image_url }}"
                  alt="{{ $item->name }}"
                  onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}';"/>
                                  
                @if($item->is_sold)
                  <span class="badge-sold">SOLD</span>
                @endif
              </div>
              <p class="mypage-item__name">{{ $item->name }}</p>
            </a>
          @endforeach
        </div>
      @endif
    @endif
  </section>

</div>
@endsection
