<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'brand'        => ['nullable', 'string', 'max:255'],
            'price'        => ['required', 'integer', 'min:0'],
            'condition'    => ['required', 'in:good,clean,fair,bad'],
            'description'  => ['required', 'string'],
            'image'        => ['required', 'image'],
            'categories'   => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
        ];
    }

    public function messages()
    {
        return [
            'image.required'       => '商品画像を選択してください',
            'image.image'          => '画像ファイルを選択してください',
            'name.required'        => '商品名を入力してください',
            'categories.required'  => 'カテゴリーを選択してください',
            'condition.required'   => '商品の状態を選択してください',
            'description.required' => '商品説明を入力してください',
            'price.required'       => '価格を入力してください',
            'price.integer'        => '価格は数値で入力してください',
        ];
    }
}
