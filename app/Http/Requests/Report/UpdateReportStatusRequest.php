<?php

namespace App\Http\Requests\Report;

use App\Enum\ReportStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReportStatusRequest extends FormRequest
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
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'status' => ['string', 'required', 'in:'.implode(",", array_column(ReportStatus::cases(), 'value'))]
		];
	}
}
