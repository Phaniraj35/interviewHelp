<?php

namespace App\Http\Requests;

use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;

class QuestionStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return  [
            'question' => 'required|string|unique:questions',
            'answer' => 'required|string',
            'difficulty' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['user_id' => $this->user()->id]);
    }
}
