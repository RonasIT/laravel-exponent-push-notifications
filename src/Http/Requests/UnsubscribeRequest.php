<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Requests;

class UnsubscribeRequest extends BaseExpoRequest
{
    public function rules(): array
    {
        return [
            'expo_token' => 'filled|string',
        ];
    }
}
