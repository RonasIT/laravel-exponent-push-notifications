<?php

namespace NotificationChannels\ExpoPushNotifications\Enum;

enum ExpoRouterActionEnum: string
{
    case Subscribe = 'subscribe';
    case Unsubscribe = 'unsubscribe';
}
