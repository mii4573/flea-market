@extends('layouts.app')

@push('css')
    {{-- マイページのスタイルを再利用 --}}
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}?v={{ time() }}">

@endpush

@section('content')
<div class="edit-container">
    <h2 style="text-align: center; margin-bottom: 30px;">プロフィール設定</h2>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 画像アップロード部分 --}}
        <div class="image-upload-section">
            <div class="profile-image">
                <img src="{{ (isset($profile) && $profile->image_path) ? asset('storage/' . $profile->image_path) : asset('images/default-user.png') }}" id="preview" alt="">
            </div>
            <label class="btn-file-select">
                画像を選択する
                <input type="file" name="image" class="file-input" onchange="previewImage(this);">
            </label>
        </div>

        <div class="form-group">
            <label>ユーザー名</label>
            <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $profile->display_name ?? $user->name) }}">
            @error('display_name')
              <span style="color: #ff4d4d;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="post_code" class="form-control" value="{{ old('post_code', $profile->post_code) }}">
            @error('post_code')
              <span style="color: #ff4d4d;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $profile->address) }}">
            @error('address')
              <span style="color: #ff4d4d;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building" class="form-control" value="{{ old('building', $profile->building) }}">
        </div>

        <button type="submit" class="btn-submit">更新する</button>
    </form>
</div>

<script>
// 選択した画像を即座にプレビューするJavaScript
function previewImage(obj) {
    var fileReader = new FileReader();
    fileReader.onload = (function() {
        document.getElementById('preview').src = fileReader.result;
    });
    fileReader.readAsDataURL(obj.files[0]);
}
</script>
@endsection