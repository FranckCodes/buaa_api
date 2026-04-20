<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'author_id'   => ['required', 'string', 'exists:users,id'],
            'content'     => ['required', 'string'],
            'post_tag_id' => ['required', 'integer', 'exists:post_tags,id'],
        ];
    }
}
