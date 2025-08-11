<?php

namespace Notification\Test;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Library\Tests\TestCase;
use Notification\Api\RequestModel\PushTokenRequestModel;
use Notification\Api\UpdatePushTokenUseCase;
use Notification\Model\PushToken;

//Надо сделать, чтобы определенный пуш токен был только у одного клиента
//Надо сделать, чтобы определенный deviceId был только у одного клиента
//Надо сделать, чтобы у клиента с одного устройства был только 1 токен, чтобы старые токены не скапливались

class UpdatePushTokenUseCaseTest extends TestCase
{
    use DatabaseTransactions;

    private UpdatePushTokenUseCase $updatePushTokenUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->updatePushTokenUseCase = app(UpdatePushTokenUseCase::class);
    }

    public function testUpdatePushToken(): void
    {
        $pushTokenRequestModel = new PushTokenRequestModel(
            token: '12345654321',
            customerId: '1',
            deviceId: null
        );

        $this->updatePushTokenUseCase->update($pushTokenRequestModel);

        self::assertTrue(PushToken::where([
            'token' => $pushTokenRequestModel->token,
            'user_id' => $pushTokenRequestModel->customerId,
            'device_id' => $pushTokenRequestModel->deviceId
        ])->exists());
    }

    public function testUpdatePushTokenWithDeviceId(): void
    {
        $pushTokenRequestModel = new PushTokenRequestModel(
            token: '12345654321',
            customerId: '1',
            deviceId: 'id123'
        );

        $this->updatePushTokenUseCase->update($pushTokenRequestModel);

        self::assertTrue(PushToken::where([
            'token' => $pushTokenRequestModel->token,
            'user_id' => $pushTokenRequestModel->customerId,
            'device_id' => $pushTokenRequestModel->deviceId
        ])->exists());
    }

    public function testUpdatePushTokenWhenHasDuplicateByCustomerIdAndDeviceId(): void
    {
        $pushTokenRequestModelFirst = new PushTokenRequestModel(
            token: '12345654321',
            customerId: '1',
            deviceId: 'id123'
        );
        $pushTokenRequestModelSecond = new PushTokenRequestModel(
            token: '987654321',
            customerId: '1',
            deviceId: 'id123'
        );

        $this->updatePushTokenUseCase->update($pushTokenRequestModelFirst);
        $this->updatePushTokenUseCase->update($pushTokenRequestModelSecond);

        self::assertTrue(PushToken::where([
            'token' => $pushTokenRequestModelSecond->token,
            'user_id' => $pushTokenRequestModelSecond->customerId,
            'device_id' => $pushTokenRequestModelSecond->deviceId
        ])->exists());
    }

    public function testUpdatePushTokenWhenHasDuplicateByToken(): void
    {
        $pushTokenRequestModelFirst = new PushTokenRequestModel(
            token: '12345654321',
            customerId: '1',
            deviceId: 'id123'
        );
        $pushTokenRequestModelSecond = new PushTokenRequestModel(
            token: '12345654321',
            customerId: '2',
            deviceId: 'id123'
        );

        $this->updatePushTokenUseCase->update($pushTokenRequestModelFirst);
        $this->updatePushTokenUseCase->update($pushTokenRequestModelSecond);

        self::assertTrue(PushToken::where([
            'token' => $pushTokenRequestModelSecond->token,
            'user_id' => $pushTokenRequestModelSecond->customerId,
            'device_id' => $pushTokenRequestModelSecond->deviceId
        ])->exists());
    }
}
