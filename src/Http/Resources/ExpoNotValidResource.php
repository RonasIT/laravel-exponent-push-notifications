<?php

namespace NotificationChannels\ExpoPushNotifications\Http\Resources;

class ExpoNotValidResource extends BaseExpoResource
{
    public function toArray($request): array
    {
        return array_merge(parent::toArray($request), [
            'error' => $this->error,
        ]);
    }
}
