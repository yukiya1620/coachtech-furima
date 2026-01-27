<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('mypage.profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'name.required' => 'ユーザー名を入力してください。',
            'profile_image.image' => '画像ファイルを選択してください。',
            'profile_image.mimes' => '画像はjpg/jpeg/png形式でアップロードしてください。',
            'profile_image.max' => '画像サイズは2MB以内にしてください。',
        ]);

        if ($request->hasFile('profile_image')) {

            if (!empty($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profiles', 'public');

            $validated['profile_image'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profile.edit')->with('status', 'プロフィールを更新しました');
    }
}
