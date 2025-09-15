<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Illuminate\Foundation\Http\FormRequest;

class BaseExpoRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new UnprocessableEntityHttpException(json_encode([
            'status' => 'failed',
            'error' => $validator->errors(),
        ]));
    }
}
