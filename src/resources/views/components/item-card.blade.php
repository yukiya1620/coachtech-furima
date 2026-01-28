@props([
  'item',
  'showPrice' => true,
  'showSold' => true,
  'imageClass' => 'item-thumb',
  'soldClass' => 'badge-sold',
  'infoClass' => 'item-meta',
])

@php
  use Illuminate\Support\Facades\Storage;

  $id = data_get($item, 'id');
  $name = data_get($item, 'name');
  $imagePath = data_get($item, 'image_path');
  $price = data_get($item, 'price');
  $isSold = data_get($item, 'is_sold');

  $normalized = $imagePath ? ltrim($imagePath, '/') : null;
  if ($normalized && str_starts_with($normalized, 'storage/')) {
    $normalized = substr($normalized, strlen('storage/'));
  }

  $fallback = asset('img/no-image.png');
  $src = $normalized ? asset('storage/' . $normalized) : $fallback;
@endphp

<a href="{{ route('items.show', $id) }}" class="item-card">
  <div class="{{ $imageClass }}">
    <img
      src="{{ $src }}"
      alt="{{ $name }}"
      onerror="this.onerror=null;this.src='{{ $fallback }}';"
    />

    @if($showSold && $isSold)
      <span class="{{ $soldClass }}">SOLD</span>
    @endif
  </div>

  <div class="{{ $infoClass }}">
    <p class="item-name">{{ $name }}</p>

    @if($showPrice)
      <p class="item-price">¥{{ number_format($price) }}</p>
    @endif
  </div>
</a>
