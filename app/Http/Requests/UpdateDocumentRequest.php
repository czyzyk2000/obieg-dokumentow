<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateDocumentRequest
 * 
 * Validation rules for updating existing documents.
 */
class UpdateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:5120'], // 5MB
            'remove_file' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'tytuł',
            'content' => 'opis',
            'amount' => 'kwota',
            'file' => 'załącznik',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tytuł jest wymagany.',
            'title.max' => 'Tytuł nie może być dłuższy niż 255 znaków.',
            'content.required' => 'Opis jest wymagany.',
            'content.max' => 'Opis nie może być dłuższy niż 5000 znaków.',
            'amount.required' => 'Kwota jest wymagana.',
            'amount.numeric' => 'Kwota musi być liczbą.',
            'amount.min' => 'Kwota nie może być ujemna.',
            'amount.max' => 'Kwota nie może przekraczać 999,999.99 PLN.',
            'file.mimes' => 'Plik musi być w formacie: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG.',
            'file.max' => 'Plik nie może być większy niż 5MB.',
        ];
    }
}
