@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_show.css') }}">
@endsection

@section('content')
<div class="item-show container">
  <div class="item-show__inner">

    {{-- Left: Image --}}
    <div class="item-show__image">
      <img
        src="{{ asset('storage/items/' . basename($item->image_path)) }}"
        alt="{{ $item->name }}"
        onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}';"
      />

    </div>

    {{-- Right: Info --}}
    <div class="item-show__info">
      <div class="item-show__section">
        <h1 class="item-show__name">{{ $item->name }}</h1>

       {{-- brand --}}
       @if(!empty($item->brand))
         <p class="item-show__brand">{{ $item->brand }}</p>
       @endif
       <p class="item-show__price">¥{{ number_format($item->price) }} <span class="item-show__tax">(税込)</span></p>

       {{-- like / comment counts --}}
       <div class="item-show__counts">
          @auth
            <form class="icon-button" action="{{ route('likes.toggle', $item->id) }}" method="POST">
              @csrf
                <button type="submit" class="icon-button__inner" aria-label="いいね">
                  <img
                    class="icon-img"
                    src="{{ asset($liked ? 'img/heart-pink.png' : 'img/heart-default.png') }}"
                    alt=""
                    aria-hidden="true"
                  >
                  <span class="icon-count">{{ $item->likes_count }}</span>
                </button>
            </form>
          @endauth
          
          @guest
            <div class="icon-button is-disabled" title="ログインが必要です">
              <img
                class="icon-img"
                src="{{ asset('img/heart-default.png') }}"
                alt=""
                aria-hidden="true"
              >
              <span class="icon-count">{{ $item->likes_count }}</span>
            </div>
          @endguest
          
          {{-- コメント数（表示だけ） --}}
          <div class="icon-button">
            <img
              class="icon-img"
              src="{{ asset('img/comment.png') }}"
              alt=""
              aria-hidden="true"
            >
            <span class="icon-count">{{ $item->comments_count }}</span>
          </div>
        </div>
    
      <div class="item-show__actions">
        
        @if($item->is_sold)
          <span class="item-show__sold">SOLD</span>
        @else
          <a href="{{ route('purchase.create', $item->id) }}" class="btn-buy">
            購入手続きへ
          </a>
        @endif
      </div>

      {{-- 商品説明 --}}
      @if($item->description)
        <div class="item-show__section item-show__description">
          <h2 class="item-show__heading">商品説明</h2>
          <p>{{ $item->description }}</p>
        </div>
      @endif

      {{-- 商品情報（カテゴリ・状態） --}}
      <div class="item-show__meta">
        <h2>商品情報</h2>

        <dl class="item-show__meta-list">
          <dt>カテゴリ</dt>
          <dd>
            @if($item->categories->isNotEmpty())
              <div class="item-show__categories">
                @foreach($item->categories as $category)
                  <span class="item-show__category">{{ $category->name }}</span>
                @endforeach
              </div>
            @else
              <span class="item-show__muted">未設定</span>
            @endif
          </dd>

          <dt>商品の状態</dt>
          <dd>
            {{ config('conditions')[$item->condition] ?? '不明' }}
          </dd>
        </dl>
      </div>

      {{-- コメント一覧 --}}
      <div class="item-show__section item-show__comments">
        <h2 class="item-show__heading">コメント（{{ $item->comments_count }}件）</h2>

        @if($item->comments->isEmpty())
          <p class="item-show__muted">まだコメントはありません。</p>
        @else
          <ul class="item-show__comment-list">
            @foreach($item->comments as $comment)
              <li class="item-show__comment">

                @php
                  $fallback = asset('img/default-user.png');

                  $path = data_get($comment->user, 'profile_image');
                  $normalized = $path ? ltrim($path, '/') : null;

                  if ($normalized && str_starts_with($normalized, 'storage/')) {
                    $normalized = substr($normalized, strlen('storage/'));
                  }

                  $avatar = $normalized ? \Illuminate\Support\Facades\Storage::url($normalized) : $fallback
                @endphp

                <div class="item-show__comment-head">
                  <img
                    class="item-show__comment-avatar"
                    src="{{ $avatar }}"
                    alt=""
                    onerror="this.onerror=null;this.src='{{ $fallback }}';"
                  />
                  <span class="item-show__comment-user">
                    {{ $comment->user->name ?? '名無し' }}
                  </span>
                </div>
                
                <p class="item-show__comment-body">
                  {{ $comment->content }}
                </p>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
      
      {{-- コメント投稿 --}}
      @if(auth()->check())
        <div class="item-show__section item-show__comment-form">
          <p class="comment-label">商品へのコメント</p>
          
          <form method="POST" action="{{ route('comments.store', $item->id) }}">
            @csrf
            
            <textarea
              name="content"
              class="comment-textarea"
              rows="3"
            >{{ old('content') }}</textarea>
            
            @error('content')
              <p class="error">{{ $message }}</p>
            @enderror
            
            <button type="submit" class="btn-comment">コメントを送信する</button>
          </form>
        </div>
      @else
        <p class="comment-login-required">
          コメントするにはログインしてください
        </p>
      @endif
    </div>
  </div>
</div>
@endsection
