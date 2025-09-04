<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Requests;

use RonasIT\Support\Http\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use NotificationChannels\ExpoPushNotifications\Http\Resources\ExpoNotValidResource;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BaseExpoRequest extends BaseRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new UnprocessableEntityHttpException(json_encode([
            'status' => 'failed',
            'error' => $validator->errors(),
        ]));
    }
}
