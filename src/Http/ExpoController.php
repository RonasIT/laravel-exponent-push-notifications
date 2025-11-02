<?php

namespace NotificationChannels\ExpoPushNotifications\Http;

use Illuminate\Routing\Controller;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\Http\Requests\SubscribeRequest;
use NotificationChannels\ExpoPushNotifications\Http\Resources\ExpoSubscribeResource;
use NotificationChannels\ExpoPushNotifications\Http\Requests\UnsubscribeRequest;
use NotificationChannels\ExpoPushNotifications\Http\Resources\ExpoUnsubscribeResource;

class ExpoController extends Controller
{
    public function __construct(
        private readonly ExpoChannel $expoChannel,
    ) {
    }

    public function subscribe(SubscribeRequest $request): ExpoSubscribeResource
    {
        $data = $request->validated();

        $interest = $this
            ->expoChannel
            ->interestName($request->user());

        $this
            ->expoChannel
            ->expo
            ->subscribe($interest, $data['expo_token']);

        $data['status'] = 'succeeded';

        return ExpoSubscribeResource::make($data);
    }

    public function unsubscribe(UnsubscribeRequest $request): ExpoUnsubscribeResource
    {
        $interest = $this
            ->expoChannel
            ->interestName($request->user());

        $deleted = $this
            ->expoChannel
            ->expo
            ->unsubscribe($interest, $request->validated('expo_token'));

        return ExpoUnsubscribeResource::make(['deleted' => $deleted]);
    }
}
