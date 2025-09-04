<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpoSubscribeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'status' => $this->status,
            'expo_token' => $this->expo_token,
        ];
    }
}
