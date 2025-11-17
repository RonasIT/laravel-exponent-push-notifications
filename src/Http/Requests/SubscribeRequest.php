<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Requests;

use RonasIT\Support\Http\BaseRequest;

class SubscribeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'expo_token' => 'required|string',
        ];
    }
}
