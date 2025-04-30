<?php

namespace NotificationChannels\ExpoPushNotifications\Enums;

enum InterruptionLevelEnum: string
{
    case Active = 'active';
    case Critical = 'critical';
    case Passive = 'passive';
    case TimeSensitive = 'time-sensitive';
}
