<?php

namespace NotificationChannels\ExpoPushNotifications\Test\Support;

use RonasIT\Support\Testing\ModelTestState as ModelTestStateBase;

class ModelTestState extends ModelTestStateBase
{
    protected function getFixturePath(string $fixtureName): string
    {
        $path = parent::getFixturePath($fixtureName);

        return str_replace('vendor/orchestra/testbench-core/laravel/', '', $path);
    }
}
