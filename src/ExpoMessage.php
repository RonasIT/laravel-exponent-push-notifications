<?php

namespace NotificationChannels\ExpoPushNotifications;

use NotificationChannels\ExpoPushNotifications\Enums\InterruptionLevelEnum;
use NotificationChannels\ExpoPushNotifications\Exceptions\CouldNotCreateMessage;

class ExpoMessage
{
    static function create(string $body = ''): static
    {
        return new static($body);
    }

    public function __construct(
        protected string $body = '',
        protected ?string $title = null,
        protected ?string $sound = 'default',
        protected int $badge = 0,
        protected int $ttl = 0,
        protected string $channelId = '',
        protected string $jsonData = '{}',
        protected string $priority = 'default',
        protected ?InterruptionLevelEnum $interruptionLevel = null,
    ) {
    }

    public function title(string $value): self
    {
        $this->title = $value;

        return $this;
    }

    public function body(string $value): self
    {
        $this->body = $value;

        return $this;
    }

    public function setInterruptionLevel(InterruptionLevelEnum $value): self
    {
        $this->interruptionLevel = $value;

        return $this;
    }

    public function enableSound(): self
    {
        $this->sound = 'default';

        return $this;
    }

    public function disableSound(): self
    {
        $this->sound = null;

        return $this;
    }

    public function badge(int $value): self
    {
        $this->badge = $value;

        return $this;
    }

    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function setChannelId(string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    public function setJsonData(array|string $data): self
    {
        if (is_array($data)) {
            $data = json_encode($data);
        } elseif (is_string($data)) {
            @json_decode($data);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new CouldNotCreateMessage('Invalid json format passed to the setJsonData().');
            }
        }

        $this->jsonData = $data;

        return $this;
    }

    public function priority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function toArray(): array
    {
        $message = [
            'title' => $this->title,
            'body' => $this->body,
            'sound' => $this->sound,
            'badge' => $this->badge,
            'ttl' => $this->ttl,
            'data' => $this->jsonData,
            'priority' => $this->priority,
        ];

        if (!empty($this->channelId)) {
            $message['channelId'] = $this->channelId;
        }

        if (!empty($this->interruptionLevel)) {
            $message['interruptionLevel'] = $this->interruptionLevel->value;
        }

        return $message;
    }
}
