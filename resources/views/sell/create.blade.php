@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-page">
  <div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>

    <form class="sell-form" method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
      @csrf

      {{-- 商品画像 --}}
      <div class="sell-block">
        <h2 class="sell-block-title">商品画像</h2>

        <label class="image-upload">
          <input id="imageInput" class="image-input" type="file" name="image" accept="image/*">

          {{-- プレビュー枠 --}}
          <img id="imagePreview" class="image-preview" alt="選択した画像のプレビュー" style="display:none;">

          <span id="imageButton" class="image-button">画像を選択する</span>
        </label>

        @error('image')
          <p class="form-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 商品の詳細 --}}
      <div class="sell-section-title">商品の詳細</div>
      <div class="sell-divider"></div>

      {{-- カテゴリー --}}
      <div class="sell-block">
        <h2 class="sell-block-title">カテゴリー</h2>

        <div class="category-tags">
          @foreach($categories as $category)
            <label class="category-tag">
              <input
                type="checkbox"
                name="categories[]"
                value="{{ $category->id }}"
                {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
              >
              <span>{{ $category->name }}</span>
            </label>
          @endforeach
        </div>

        @error('categories')
          <p class="form-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 商品の状態 --}}
      <div class="sell-block">
        <h2 class="sell-block-title">商品の状態</h2>

        <select class="sell-select" name="condition">
          <option value="" disabled hidden {{ old('condition') ? '' : 'selected' }}>
            選択してください
          </option>
          @foreach(config('conditions') as $key => $label)
            <option value="{{ $key }}" @selected(old('condition') === $key)>
              {{ $label }}
            </option>
          @endforeach
        </select>

        @error('condition')
          <p class="form-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 商品名と説明 --}}
      <div class="sell-section-subtitle">商品名と説明</div>

      {{-- 商品名 --}}
      <div class="sell-block">
        <h2 class="sell-block-title">商品名</h2>
        <input
          class="sell-input"
          type="text"
          name="name"
          value="{{ old('name') }}"
          placeholder="商品名を入力"
        >

        @error('name')
          <p class="form-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- ブランド名（任意） --}}
      <div class="sell-block">
        <h2 class="sell-block-title">ブランド名</h2>
        <input
          class="sell-input"
          type="text"
          name="brand"
          value="{{ old('brand') }}"
          placeholder="ブランド名（任意）"
        >
      </div>

      {{-- 商品説明 --}}
      <div class="sell-block">
        <h2 class="sell-block-title">商品の説明</h2>
        <textarea
          class="sell-textarea"
          name="description"
          rows="5"
          placeholder="商品の説明を入力"
        >{{ old('description') }}</textarea>

        @error('description')
          <p class="form-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- 販売価格 --}}
      <div class="sell-block">
        <h2 class="sell-block-title">販売価格</h2>
        <input
          class="sell-input"
          type="number"
          name="price"
          value="{{ old('price') }}"
          placeholder="¥"
        >

        @error('price')
          <p class="form-error">{{ $message }}</p>
        @enderror
      </div>

      <button class="sell-submit" type="submit">出品する</button>

      <script>
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const button = document.getElementById('imageButton');
        
        input.addEventListener('change', () => {
          const file = input.files?.[0];
          if (!file) return;
          
          const url = URL.createObjectURL(file);
          preview.src = url;
          preview.style.display = 'block';
          button.style.display = 'none';
        });
      </script>
    </form>
  </div>
</div>
@endsection
