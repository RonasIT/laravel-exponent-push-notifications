<?php

namespace NotificationChannels\ExpoPushNotifications\Http;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\Http\Requests\SubscribeRequest;
use NotificationChannels\ExpoPusRehNotifications\Http\Resources\ExpoSubscribeResource;
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
        $this->expoChannel
            ->expo
            ->subscribe($this
                ->expoChannel
                ->interestName(Auth::user()), $request->validated('expo_token')
            );

        return ExpoSubscribeResource::make($request->onlyValidated());
    }

    public function unsubscribe(UnsubscribeRequest $request): ExpoUnsubscribeResource
    {
        $interest = $this
            ->expoChannel
            ->interestName(Auth::user());

        $token = $request->get('expo_token') ?: null;

        $deleted = $this
            ->expoChannel
            ->expo
            ->unsubscribe($interest, $token);

        return ExpoUnsubscribeResource::make($deleted);
    }
}
