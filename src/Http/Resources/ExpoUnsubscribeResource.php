<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpoUnsubscribeResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'deleted' => $this->resource['deleted'],
        ];
    }
}
