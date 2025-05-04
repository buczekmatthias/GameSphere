<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user() === null;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'name' => 'required|string|max:255|not_in:Guest,guest,test,Test',
			'username' => ['required', 'string', 'max:75', 'unique:'.User::class.',username', 'not_in:Guest,guest,test,Test'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email'],
			'password' => ['required', 'confirmed', Rules\Password::defaults()],
		];
	}
}
