@props(['variant' => 'default'])

<header class="header">
  <div class="header__inner">
    <a class="header__logo" href="{{ url('/') }}">COACHTECH</a>

    @if($variant === 'default')
      <form class="header__search" method="GET" action="{{ url('/') }}">
        <input
          class="header__searchInput"
          type="text"
          name="keyword"
          placeholder="なにをお探しですか？"
          value="{{ request('keyword') }}"
        >
      </form>

      <nav class="header__nav">
        @guest
          <a class="header__link" href="{{ route('login') }}">ログイン</a>
        @endguest

        @auth
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="header__linkButton" type="submit">ログアウト</button>
          </form>
        @endauth

        <a class="header__link" href="{{ url('/mypage') }}">マイページ</a>
        <a class="header__sellBtn" href="{{ url('/sell') }}">出品</a>
      </nav>
    @endif
  </div>
</header>
