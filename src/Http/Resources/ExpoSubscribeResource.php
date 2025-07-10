<?php

namespace NotificationChannels\ExpoPusRehNotifications\Http\Resources;

use NotificationChannels\ExpoPushNotifications\Http\Resources\BaseExpoResource;

class ExpoSubscribeResource extends BaseExpoResource
{
    public function toArray($request): array
    {
        return array_merge(parent::toArray($request), [
            'expo_token' => $this->expo_token,
        ]);
    }
}
