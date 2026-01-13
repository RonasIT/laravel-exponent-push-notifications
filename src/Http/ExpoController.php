<?php

namespace NotificationChannels\ExpoPushNotifications\Http;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\Http\Requests\SubscribeRequest;
use NotificationChannels\ExpoPushNotifications\Http\Requests\UnsubscribeRequest;

class ExpoController extends Controller
{
    public function __construct(
        private readonly ExpoChannel $expoChannel,
    ) {
    }

    public function subscribe(SubscribeRequest $request): Response
    {
        $interest = $this
            ->expoChannel
            ->interestName($request->user());

        $this
            ->expoChannel
            ->expo
            ->subscribe($interest, $request->validated('expo_token'));

        return response()->noContent();
    }

    public function unsubscribe(UnsubscribeRequest $request): Response
    {
        $interest = $this
            ->expoChannel
            ->interestName($request->user());

        $this
            ->expoChannel
            ->expo
            ->unsubscribe($interest, $request->validated('expo_token'));

        return response()->noContent();
    }
}
