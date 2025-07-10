<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Requests;

use RonasIT\Support\Http\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use NotificationChannels\ExpoPushNotifications\Http\Resources\ExpoNotValidResource;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BaseExpoRequest extends BaseRequest
{
    protected function failedValidation(Validator $validator): ExpoNotValidResource
    {
        throw new UnprocessableEntityHttpException(
            (new ExpoNotValidResource([
                'status' => 'failed',
                'error' => $validator->errors(),
            ]))->response(),
        );
    }
}
