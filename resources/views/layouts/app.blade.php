<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'COACHTECHフリマ')</title>

  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
</head>
<body>
  @include('partials.header', ['variant' => $headerVariant ?? $variant ?? 'default'])

  <main class="main">
    @yield('content')
  </main>
</body>
</html>
