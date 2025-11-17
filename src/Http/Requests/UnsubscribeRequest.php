<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Requests;

use RonasIT\Support\Http\BaseRequest;

class UnsubscribeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'expo_token' => 'sometimes|string',
        ];
    }
}
