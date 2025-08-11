<?php

namespace Notification\Persist;

use Illuminate\Support\Collection;
use Notification\Model\PushToken;
use Illuminate\Support\Facades\DB;

class EloquentPushTokenRepository implements PushTokenRepository
{
    public function save(PushToken $pushToken): void
    {
        DB::transaction(function () use ($pushToken) {
            PushToken::where('token', $pushToken->token)->delete();
            PushToken::where('device_id', $pushToken->device_id)->delete();

            PushToken::upsert(
                [
                    'token' => $pushToken->token,
                    'user_id' => $pushToken->user_id,
                    'device_id' => $pushToken->device_id,
                ],
                ['user_id', 'device_id'],
                ['token']
            );
        });
    }

    public function getCustomerPushTokens(string $customerId): Collection
    {
        return PushToken::where('user_id', $customerId)->get();
    }
}
