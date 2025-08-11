<?php

namespace Api\Models\DeliveryArea;

use Api\Exceptions\IncorrectDeliveryTypeParamsException;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Library\ValueObject\DeliveryTime;
use PriceAndStock\Infrastructure\Repository\Db\Model\Stock;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @property string $stock_id
 */
class DeliveryArea extends Model
{
    use HasUuids;
    use SoftDeletes;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public const string DEMO_DELIVERY_AREA_CODE = '75cfaf40-caa8-40bc-a42a-a888b74c7cdf';

    public $timestamps = false;
    protected $table = 'delivery_area';
    protected $casts = [
        'work_time' => 'collection',
        'coordinates' => 'collection'
    ];

    protected static function booted()
    {
        static::addGlobalScope(static fn(Builder $builder) => $builder->whereNull('deleted_at'));
    }
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    public function isWorkingTime(): bool
    {
        $todaySchedule = $this->getTodaySchedule();

        if (!$todaySchedule) {
            return true;
        }

        [$time_from, $time_to] = explode('-', $todaySchedule);
        [$fromHour, $fromMinutes] = explode(':', $time_from);
        [$toHour, $toMinutes] = explode(':', $time_to);

        $obWorkTimeFrom = (new DateTimeImmutable())->setTime($fromHour, $fromMinutes)->format('H:i');
        $obWorkTimeTo = (new DateTimeImmutable())->setTime($toHour, $toMinutes)->format('H:i');
        $obCurrentTime = (new DateTimeImmutable())->format('H:i');

        return $obCurrentTime >= $obWorkTimeFrom && $obCurrentTime < $obWorkTimeTo;
    }

    public function getTodaySchedule(): ?string
    {
        $weekMap = ['ВС', 'ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ'];
        $weekday = $weekMap[Carbon::now()->dayOfWeek];

        if ($this->work_time->has($weekday)) {
            return $this->work_time[$weekday];
        } else {
            return null;
        }
    }

    public function deliveries(): BelongsToMany
    {
        return $this->belongsToMany(
            DeliveryType::class,
            'delivery_area_delivery_type',
            'delivery_area_id',
            'delivery_type_id'
        )->where('is_active', 1);
    }


    /**
     * @throws IncorrectDeliveryTypeParamsException
     */
    public function getDeliveryPrice(string $deliveryType, int $productsPrice): int
    {
        $query = DeliveryType::where('is_active', 1);
        $query = $query->whereHas('deliveryArea', function ($query) {
            return $query->where('id', $this->id);
        });
        $anyDeliveryTypes = $query->get();
        $currentDeliveryType = $query->where('delivery_type', $deliveryType)->first();

        $arResultDeliveryPriceInfo[] = [
            'from' => 0,
            'deliveryPrice' => 0
        ];
        if (empty($anyDeliveryTypes->first())) {
            if ($deliveryType == DeliveryType::EXPRESS) {
                return $productsPrice < DeliveryType::EXPRESS_DEFAULT_MIN_SUM_FOR_FREE_DELIVERY ?
                    DeliveryType::EXPRESS_DEFAULT_PRICE : 0;
            }
        }

        if (empty($currentDeliveryType)) {
            $availableTypes = $anyDeliveryTypes->pluck('delivery_type')->toArray();
            throw new IncorrectDeliveryTypeParamsException(
                new HttpException(
                    statusCode: 424,
                    message: "Тип доставки {$deliveryType} недоступен для текущей зоны доставки.",
                    code: 1001
                ),
                ['availableDeliveryTypes' => $availableTypes ?: [DeliveryType::EXPRESS]]
            );
        }

        foreach ($currentDeliveryType->price as $priceGradation) {
            if (
                isset($lastGradation) &&
                $productsPrice < $priceGradation['fromOrderAmount'] &&
                $productsPrice > $lastGradation['fromOrderAmount']
            ) {
                return $lastGradation['deliveryPrice'];
            }
            $lastGradation = $priceGradation;
        }

        return $arResultDeliveryPriceInfo[0]['deliveryPrice'];
    }

    /**
     * @throws IncorrectDeliveryTypeParamsException
     */
    public function getDeliveryTime(string $deliveryDateTime): DeliveryTime
    {
        [$deliveryDay, $deliveryTime] = explode(" ", $deliveryDateTime);
        [$deliveryTimeFrom, $deliveryTimeTo] = explode("-", $deliveryTime);

        $delivery = Arr::first($this->deliveries, function (DeliveryType $delivery) {
            return $delivery->delivery_type == DeliveryType::DELAYED;
        });

        if ($delivery == null) {
            throw new IncorrectDeliveryTypeParamsException(
                new HttpException(
                    statusCode: 424,
                    message: 'Отложенная доставка недоступна.',
                    code: 1001
                ),
                ['availableDeliveryTypes' => [DeliveryType::EXPRESS]]
            );
        }

        $slotDate = Arr::first($delivery->delivery_time, function ($slotDate) use ($deliveryDay) {
            return $slotDate['date'] === $deliveryDay;
        });

        if ($slotDate == null) {
            throw new IncorrectDeliveryTypeParamsException(
                new HttpException(
                    statusCode: 424,
                    message: "На дату: {$deliveryDay} доставка недоступна.",
                    code: 1002
                ),
                []
            );
        }

        $slot = Arr::first($slotDate["slots"], function ($slot) use ($deliveryTimeFrom, $deliveryTimeTo) {
            return $slot['isAvailable'] && $slot['startTime'] == $deliveryTimeFrom
                && $slot['endTime'] == $deliveryTimeTo;
        });

        if ($slot == null) {
            throw new IncorrectDeliveryTypeParamsException(
                new HttpException(
                    statusCode: 424,
                    message: "На время: {$deliveryTime} доставка недоступна.",
                    code: 1003
                ),
                []
            );
        };

        return new DeliveryTime(
            CarbonImmutable::parse("$deliveryDay {$slot['startTime']}"),
            CarbonImmutable::parse("$deliveryDay {$slot['endTime']}")
        );
    }

    public function hasDelayDelivery(): bool
    {
        return $this->deliveries()->where('delivery_type', DeliveryType::DELAYED)->exists();
    }

    public function approximateDeliveryTime()
    {
        return $this->delivery_time ?? 30;
    }
}
