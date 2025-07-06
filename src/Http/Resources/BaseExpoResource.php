<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseExpoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'status' => $this->status,
        ];
    }
}
