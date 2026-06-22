<?php

namespace App\Http\Requests\Auth;

use App\Models\PasswordConfiguration;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $config = PasswordConfiguration::getConfig();

        return [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => $this->passwordRules($config),
        ];
    }

    private function passwordRules(PasswordConfiguration $config): array
    {
        $rules = [
            'required',
            'string',
            'confirmed',
            // all time minimum 8,  maximum count from config 
            'min:8',
            "max:{$config->max_length}",
        ];

        // Word count — always apply
        if ($config->min_words > 0) {
            $rules[] = function ($attribute, $value, $fail) use ($config) {
                $spaced    = preg_replace('/([a-z])([A-Z])/', '$1 $2', $value);
                $wordCount = str_word_count($spaced);
                if ($wordCount < $config->min_words) {
                    $fail("Password must contain at least {$config->min_words} words.");
                }
            };
        }

        // Adaptive complexity rules
        $rules[] = function ($attribute, $value, $fail) use ($config) {

            $length = strlen($value);

            // $config->min_length+ character → without complexity check
            if ($length >= $config->min_length) {
                return;
            }

            // 8–($config->min_length - 1) character → with complexity check
            if ($config->require_uppercase && !preg_match('/[A-Z]/', $value)) {
                $fail('Password must contain at least one uppercase letter (A–Z).');
                return;
            }

            if ($config->require_lowercase && !preg_match('/[a-z]/', $value)) {
                $fail('Password must contain at least one lowercase letter (a–z).');
                return;
            }

            if ($config->require_number && !preg_match('/[0-9]/', $value)) {
                $fail('Password must contain at least one number (0–9).');
                return;
            }

            if ($config->require_special_char && !preg_match('/[\W_]/', $value)) {
                $fail('Password must contain at least one special character (!@#$%^&*...).');
                return;
            }
        };

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Name is required.',
            'email.required'     => 'Email is required.',
            'email.unique'       => 'This email is already registered.',
            'password.required'  => 'Password is required.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.max'       => 'Password is too long.',
        ];
    }
}