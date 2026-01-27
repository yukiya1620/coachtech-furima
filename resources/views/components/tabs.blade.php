@props([
  'tabs' => [],      // [['label' => 'おすすめ', 'href' => route(...), 'active' => true], ...]
])

<div class="tabs">
  @foreach($tabs as $tab)
    <a href="{{ $tab['href'] }}"
       class="tab {{ !empty($tab['active']) ? 'is-active' : '' }}">
      {{ $tab['label'] }}
    </a>
  @endforeach
</div>
