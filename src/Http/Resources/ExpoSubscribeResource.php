<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpoSubscribeResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'expo_token' => $this->resource['expo_token'],
            'status' => $this->resource['status'],
        ];
    }
}
