@php
  $variant = $variant ?? 'default';
@endphp

<header class="header header--{{ $variant }}">
  <div class="header__inner">
    <a class="header__logo" href="{{ route('items.index') }}">
      <img src="{{ asset('img/header-logo.png') }}" alt="COACHTECH">
    </a>

    @if($variant === 'default')
      <form action="{{ route('items.index') }}" method="GET" class="header__search">
        <input
          class="header__searchInput"
          type="text"
          name="keyword"
          value="{{ request('keyword') }}"
          placeholder="なにをお探しですか？"
        >

        <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">

        <button type="submit" class="u-visually-hidden">検索</button>
      </form>

      <nav class="header__nav">
        @guest
          <a class="header__link" href="{{ route('login') }}">ログイン</a>
          <a class="header__link" href="{{ route('login') }}">マイページ</a>
          <a class="header__btn" href="{{ route('login') }}">出品</a>
        @endguest

        @auth
          <form class="header__logout" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="header__link header__link--button" type="submit">
              ログアウト
            </button>
          </form>

          <a class="header__link" href="{{ route('mypage.index') }}">マイページ</a>
          <a class="header__btn" href="{{ route('sell.create') }}">出品</a>
        @endauth
      </nav>

    @elseif($variant === 'auth')
      <nav class="header__nav">
        @guest
          <a class="header__link" href="{{ route('login') }}">ログイン</a>
          <a class="header__link" href="{{ route('login') }}">マイページ</a>
          <a class="header__btn" href="{{ route('login') }}">出品</a>
        @endguest

        @auth
          <form class="header__logout" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="header__link header__link--button" type="submit">
              ログアウト
            </button>
          </form>

          <a class="header__link" href="{{ route('mypage.index') }}">マイページ</a>
          <a class="header__btn" href="{{ route('sell.create') }}">出品</a>
        @endauth
      </nav>

    @elseif($variant === 'simple')
      {{-- ロゴだけ(何も表示しない) --}}

    @endif
  </div>
</header>
