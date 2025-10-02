<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class BaseExpoRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        $response = response()->json([
            'status' => 'failed',
            'errors' => $validator->errors(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
