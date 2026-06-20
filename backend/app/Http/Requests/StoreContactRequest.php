<?php

namespace App\Http\Requests;

use App\Support\PhoneValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalized = PhoneValidator::normalize($this->input('phone'));

        if ($normalized !== null) {
            $this->merge(['phone' => $normalized['formatted']]);
        }

        if (! $this->filled('message')) {
            $this->merge(['message' => '']);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:5000'],
            'source_section' => ['nullable', 'string', 'max:50'],
            'source_label' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $phone = $this->input('phone');

            if (! PhoneValidator::isValid($phone)) {
                $validator->errors()->add(
                    'phone',
                    'Укажите корректный номер телефона в формате +7 XXX XXX-XX-XX.',
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите, как к вам обращаться.',
            'phone.required' => 'Укажите номер телефона для связи.',
        ];
    }
}
