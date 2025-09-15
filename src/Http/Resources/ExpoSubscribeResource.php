<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpoSubscribeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'status' => $this->resource->status,
            'expo_token' => $this->resource->expo_token,
        ];
    }
}
