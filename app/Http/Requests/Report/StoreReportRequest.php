<?php

namespace App\Http\Requests\Report;

use App\Services\ReportsServices;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReportRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return Auth::check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$exists = [
			'comment' => 'comments,slug',
			'discussion' => 'discussions,slug',
			'game' => 'games,slug',
			'user' => 'users,username',
			'review' => 'reviews,slug'
		];

		return [
			'reason' => ['string', 'required', 'in:'.implode(',', ReportsServices::getReportReasons())],
			'id' => ['string_or_uuid', 'required', "exists:".$exists[$this->post('type')]],
			'type' => ['string', 'in:'.implode(',', array_keys($exists))]
		];
	}
}
