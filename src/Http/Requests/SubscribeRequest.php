<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Requests;

class SubscribeRequest extends BaseExpoRequest
{
    public function rules(): array
    {
        return [
            'expo_token' => 'required|string',
        ];
    }
}
