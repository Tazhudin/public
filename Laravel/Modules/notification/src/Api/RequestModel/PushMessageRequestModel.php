<?php

namespace Notification\Api\RequestModel;

class PushMessageRequestModel
{
    /**
     * @param array<string, string> $data
     * @return void
     */
    public function __construct(
        public string $message,
        public string $title,
        public string $image = '',
        public array $data = [],
        private string $hash = '',
    ) {
    }

    public function hash(): string
    {
        return $this->hash ?: md5(sprintf('%s,%s,%s', $this->message, $this->title, json_encode($this->data)));
    }
}
