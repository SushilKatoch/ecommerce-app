<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class productCategoriesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'              => 'required|max:191|unique:product_categories',
            'slug'              => 'nullable|max:191|unique:product_categories',
            'images'            => 'nullable',
            'description'           => 'nullable',
            'bannerImage'              => 'nullable|',
            'bannerImageMobile'       => 'nullable',
            'isActive'  => 'nullable|max:50',
            'parentId'    => 'nullable|max:191',
            'parentName'              => 'nullable',
            'reorder'             => 'nullable|numeric',
            'seoData'            => 'nullable',
        ];
    }
    public function messages(): array
    {
        return [
            'name' => 'Please Enter Name',
            'name.unique' => 'Name  Already Exist',
        ];
    }
}
