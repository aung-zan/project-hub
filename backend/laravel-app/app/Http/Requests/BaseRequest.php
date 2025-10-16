<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\MessageBag;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();

        $response = $this->customizedResponse($errors);

        throw new HttpResponseException(response()->json($response, 422));
    }

    /**
     * Customized the response array.
     *
     * @param \Illuminate\Support\MessageBag $errors
     * @return array
     */
    private function customizedResponse(MessageBag $errors): array
    {
        $errorMessages = [];

        foreach ($errors->toArray() as $field => $message) {
            $errorMessages[$field] = $message;
        }

        return [
            'success' => false,
            'error' => 'VALIDATION_FALIED',
            'message' => $errorMessages,
        ];
    }
}
