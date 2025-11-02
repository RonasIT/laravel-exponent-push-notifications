<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Resources;

class ExpoSubscribeResource extends BaseJsonResource
{
    public function toArray($request): array
    {
        return [
            'expo_token' => $this->resource['expo_token'],
            'status' => $this->resource['status'],
        ];
    }
}
