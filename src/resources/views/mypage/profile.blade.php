@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-page">
  <h1 class="profile-title">プロフィール設定</h1>

  <form class="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf

    <div class="profile-avatar-row">
      <div class="profile-avatar">
        <img
          id="avatarPreview"
          src="{{ !empty($user->profile_image) ? asset('storage/'.$user->profile_image) : '' }}"
          alt="avatar"
          style="{{ empty($user->profile_image) ? 'display:none;' : '' }}"
        >
      </div>

      <input id="profile_image" class="profile-file" type="file" name="profile_image" accept="image/*">
      <label for="profile_image" class="profile-file-btn">画像を選択する</label>
    </div>

    <div class="profile-field">
      <label class="profile-label" for="name">ユーザー名</label>
      <input class="profile-input" id="name" type="text" name="name" value="{{ old('name', $user->name) }}">
    </div>

    <div class="profile-field">
      <label class="profile-label" for="postcode">郵便番号</label>
      <input class="profile-input" id="postcode" type="text" name="postcode" value="{{ old('postcode', $user->postcode) }}">
    </div>

    <div class="profile-field">
      <label class="profile-label" for="address">住所</label>
      <input class="profile-input" id="address" type="text" name="address" value="{{ old('address', $user->address) }}">
    </div>

    <div class="profile-field">
      <label class="profile-label" for="building">建物名</label>
      <input class="profile-input" id="building" type="text" name="building" value="{{ old('building', $user->building) }}">
    </div>

    <button class="profile-submit" type="submit">更新する</button>
  </form>
</div>

@endsection
<script>
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('profile_image');
  const preview = document.getElementById('avatarPreview');

  if (!input || !preview) return;

  input.addEventListener('change', (e) => {
    const file = e.target.files && e.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
      input.value = '';
      alert('画像ファイルを選択してください');
      return;
    }

    const reader = new FileReader();
    reader.onload = (ev) => {
      preview.src = ev.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  });
});
</script>
