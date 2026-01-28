@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="container purchase">
  <div class="purchase-grid">

    {{-- 左：商品情報 + 支払い方法 + 配送先 --}}
    <div class="purchase-left">

      <div class="purchase-item-card">
        <div class="thumb">
          <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
               onerror="this.onerror=null;this.src='{{ asset('img/no-image.png') }}';">
          @if($item->is_sold)
            <span class="badge-sold">Sold</span>
          @endif
        </div>
        
        <div class="meta">
          <p class="name">{{ $item->name }}</p>
          <p class="price">¥{{ number_format($item->price) }}</p>
        </div>
      </div>
      
      @if($item->is_sold)
        <p class="warn">この商品は売り切れです。</p>
      @endif
      
      <form method="POST" action="{{ route('purchase.store', $item->id) }}">
        @csrf
        
        <div class="block">
          <h2 class="block-title">支払い方法</h2>
          
          <select class="select" name="payment_method" id="paymentSelect" required @disabled($item->is_sold)>
            <option value="" selected disabled hidden>選択してください</option>
            @foreach($paymentMethods as $key => $label)
              <option value="{{ $key }}" @selected(old('payment_method') === $key)>{{ $label }}</option>
            @endforeach
          </select>
          
          @error('payment_method')
            <p class="error">{{ $message }}</p>
          @enderror
        </div>
        
        <div class="block">
          <div class="block-head">
            <h2 class="block-title">配送先</h2>
            <a class="link" href="{{ route('purchase.address.edit', $item->id) }}">変更する</a>
          </div>
          
          <p class="shipping-line">〒{{ $shipping['postal_code'] ?? '' }}</p>
          <p class="shipping-line">{{ $shipping['address'] ?? '' }}</p>
          @if(!empty($shipping['building'] ?? ''))
            <p class="shipping-line">{{ $shipping['building'] }}</p>
          @endif
        </div>
        
        <div class="purchase-right">
          <div class="summary-box">
            <div class="summary-row">
              <span>商品代金</span>
              <span>¥{{ number_format($item->price) }}</span>
            </div>
          </div>
          
          <div class="summary-box">
            <div class="summary-row">
              <span>支払い方法</span>
              <span id="payText">コンビニ払い</span>
            </div>
          </div>
          
          <div class="actions">
            @if(!$item->is_sold)
              <button class="btn-primary" type="submit">購入する</button>
            @else
              <button class="btn-primary" type="button" disabled>購入する</button>
            @endif
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const select = document.getElementById('paymentSelect');
  const payText = document.getElementById('payText');

  function syncPayText(){
    if (!select || !payText) return;

    if (select.value === '') {
      payText.textContent = 'コンビニ払い';
      return;
    }

    const label = select.options[select.selectedIndex].text;
    payText.textContent = label;
  }

  if (select) {
    select.addEventListener('change', syncPayText);
    syncPayText();
  }
</script>

@endsection
