<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:5120'],
            'remove_file' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'title',
            'content' => 'content',
            'amount' => 'amount',
            'file' => 'attachment',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.max' => 'Title cannot be longer than 255 characters.',
            'content.required' => 'Content is required.',
            'content.max' => 'Content cannot be longer than 5000 characters.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount cannot be negative.',
            'amount.max' => 'Amount cannot exceed 999,999.99.',
            'file.mimes' => 'File must be in format: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG.',
            'file.max' => 'File cannot be larger than 5MB.',
        ];
    }
}
