<?php

namespace NotificationChannels\ExpoPushNotifications\Test;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RonasIT\Media\Models\Media;
use RonasIT\Media\Tests\Models\User;
use RonasIT\Media\Tests\Support\ModelTestState;

class ExpoStaticTest
{
    public function setUp(): void
    {
        parent::setUp();

        self::$user ??= User::find(2);
        self::$file ??= UploadedFile::fake()->image('file.png', 600, 600);
        self::$mediaTestState ??= new ModelTestState(Media::class);

        Storage::fake();
    }
}
