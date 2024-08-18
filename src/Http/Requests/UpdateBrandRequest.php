<?php

namespace JobMetric\Brand\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Translation\Rules\TranslationFieldExistRule;
use JobMetric\Brand\Models\Brand;

class UpdateBrandRequest extends FormRequest
{
    public int|null $brand_id = null;

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
        if (is_null($this->brand_id)) {
            $brand_id = $this->route()->parameter('brand')?->id;
        } else {
            $brand_id = $this->brand_id;
        }

        return [
            'status' => 'sometimes|boolean|nullable',
            'ordering' => 'sometimes|integer|nullable',

            'translation' => 'sometimes|array',
            'translation.name' => [
                'sometimes',
                'string',
                new TranslationFieldExistRule(Brand::class, 'name', object_id: $brand_id),
            ],
            'translation.description' => 'sometimes|string|nullable',
            'translation.meta_title' => 'sometimes|string|nullable',
            'translation.meta_description' => 'sometimes|string|nullable',
            'translation.meta_keywords' => 'sometimes|string|nullable',
        ];
    }

    /**
     * Set brand id for validation
     *
     * @param int $brand_id
     * @return static
     */
    public function setBrandId(int $brand_id): static
    {
        $this->brand_id = $brand_id;

        return $this;
    }
}
