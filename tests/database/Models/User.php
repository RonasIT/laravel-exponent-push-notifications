<?php

namespace NotificationChannels\ExpoPushNotifications\Test\database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RonasIT\Support\Traits\ModelTrait;

class User extends Authenticatable
{
    protected $table = 'users';

    use Notifiable;
    use ModelTrait;
    use HasFactory;

    protected $fillable = [
        'email',
    ];
}
