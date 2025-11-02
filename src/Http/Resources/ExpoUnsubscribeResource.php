<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Resources;

class ExpoUnsubscribeResource extends BaseJsonResource
{
    public function toArray($request): array
    {
        return [
            'deleted' => $this->resource['deleted'],
        ];
    }
}
