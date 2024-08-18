<?php

namespace JobMetric\Brand\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Translation\Rules\TranslationFieldExistRule;
use JobMetric\Brand\Models\Brand;

class StoreBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'boolean|nullable',
            'ordering' => 'integer|nullable',

            'translation' => 'required|array',
            'translation.name' => [
                'string',
                new TranslationFieldExistRule(Brand::class, 'name'),
            ],
            'translation.description' => 'string|nullable',
            'translation.meta_title' => 'string|nullable',
            'translation.meta_description' => 'string|nullable',
            'translation.meta_keywords' => 'string|nullable',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->status ?? true,
            'ordering' => $this->ordering ?? 0
        ]);
    }
}
