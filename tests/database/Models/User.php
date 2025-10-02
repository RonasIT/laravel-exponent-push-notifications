<?php

namespace NotificationChannels\ExpoPushNotifications\Test\database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RonasIT\Media\Models\Media;
use RonasIT\Support\Traits\ModelTrait;

class User extends Authenticatable
{
    use Notifiable;
    use ModelTrait;
    use HasFactory;

    protected $fillable = [
        'email',
    ];
}
