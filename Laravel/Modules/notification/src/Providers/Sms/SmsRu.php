<?php

namespace Notification\Providers\Sms;

use Illuminate\Support\Facades\Http;
use Library\Helper\SomeExceptionWithContext;
use Notification\Providers\NotificationProviderResponse;

readonly class SmsRu implements SmsProvider
{
    public function __construct(
        private string $endpoint,
        private string $apiId,
    ) {
    }

    /**
     * @param array<string> $phoneNumbers
     * @throws \Throwable
     */
    public function send(array $phoneNumbers, string $message): NotificationProviderResponse
    {
        $response = Http::baseUrl($this->endpoint)
            ->get('/sms/send', [
                'api_id' => $this->apiId,
                'to' => implode(',', $phoneNumbers),
                'msg' => $message,
                'json' => 1
            ]);

        if (!$response->ok()) {
            throw new SomeExceptionWithContext($response->body(), [
                'response' => $response->json()
            ]);
        }

        return new NotificationProviderResponse($response->collect());
    }
}
