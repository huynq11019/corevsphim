<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShortsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:episodes,slug,' . $this->id,
            'movie_id' => 'required|exists:movies,id',
            'link' => 'required|string',
            'hashtags' => 'nullable|string|max:500',
            'duration_seconds' => 'nullable|integer|min:1|max:300',
            'status' => 'required|in:active,inactive,pending',
            'content' => 'nullable|string|max:1000',
            'view' => 'nullable|integer|min:0',
            'likes' => 'nullable|integer|min:0',
            'dislikes' => 'nullable|integer|min:0',
            'shares' => 'nullable|integer|min:0'
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'tiêu đề',
            'slug' => 'đường dẫn',
            'movie_id' => 'phim',
            'link' => 'link video',
            'hashtags' => 'hashtags',
            'duration_seconds' => 'thời lượng',
            'status' => 'trạng thái',
            'content' => 'mô tả',
            'view' => 'lượt xem',
            'likes' => 'likes',
            'dislikes' => 'dislikes',
            'shares' => 'shares'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Tiêu đề là bắt buộc.',
            'name.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'slug.unique' => 'Đường dẫn này đã tồn tại.',
            'movie_id.required' => 'Vui lòng chọn phim.',
            'movie_id.exists' => 'Phim được chọn không tồn tại.',
            'link.required' => 'Link video là bắt buộc.',
            'hashtags.max' => 'Hashtags không được vượt quá 500 ký tự.',
            'duration_seconds.integer' => 'Thời lượng phải là số nguyên.',
            'duration_seconds.min' => 'Thời lượng tối thiểu là 1 giây.',
            'duration_seconds.max' => 'Thời lượng tối đa là 300 giây (5 phút).',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'content.max' => 'Mô tả không được vượt quá 1000 ký tự.'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Auto-generate slug if not provided
        if (empty($this->slug) && !empty($this->name)) {
            $this->merge([
                'slug' => \Str::slug($this->name)
            ]);
        }

        // Ensure is_short is true for shorts
        $this->merge([
            'is_short' => true,
            'server' => $this->server ?? 'shorts',
            'type' => $this->type ?? 'embed'
        ]);

        // Process hashtags
        if (!empty($this->hashtags)) {
            // Convert comma-separated string to proper format
            $hashtags = explode(',', $this->hashtags);
            $hashtags = array_map('trim', $hashtags);
            $hashtags = array_filter($hashtags);

            $this->merge([
                'hashtags' => implode(',', $hashtags)
            ]);
        }
    }
}
