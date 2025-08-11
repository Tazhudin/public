<?php

namespace Feedback\Api;

use Event;
use Feedback\Api\RequestModel\OrderCancelationRequestModel;
use Feedback\Api\RequestModel\OrderEvaluationRequestModel;
use Feedback\Api\ResponseModel\FaqResponseModel;
use Feedback\Api\ResponseModel\OrderCancelationVariantResponseModel;
use Feedback\Api\ResponseModel\OrderEvaluationVariantResponseModel;
use Feedback\Infrastructure\Models\Faq;
use Feedback\Infrastructure\Models\OrderCancelationVariant;
use Feedback\Infrastructure\Models\OrderCancellation;
use Feedback\Infrastructure\Models\OrderEvaluation;
use Feedback\Infrastructure\Models\OrderEvaluationVariant;
use Feedback\Infrastructure\OrderEvaluateEvent;

class FeedbackAPI
{
    /**
     * Получение списка вопрос-ответ
     * @return array<FaqResponseModel>
     */
    public function getFaqs(): array
    {
        return Faq::get()
            ->mapInto(FaqResponseModel::class)
            ->all();
    }

    /**
     * Получение списка вариантов оценок заказа
     * @return array<OrderEvaluationVariantResponseModel>
     */
    public function getEvaluationVariants(): array
    {
        return OrderEvaluationVariant::groupBy('evaluation')
            ->selectRaw('JSON_ARRAY_ELEMENTS_TEXT(evaluations) AS evaluation, JSON_AGG(comment) AS comments')
            ->get()
            ->mapInto(OrderEvaluationVariantResponseModel::class)
            ->all();
    }

    /**
     * Добавление клиентской оценки заказа
     */
    public function addEvaluation(OrderEvaluationRequestModel $obDtoModel): bool
    {
        $arEvaluation = (array) $obDtoModel;
        $obEvaluation = new OrderEvaluation($arEvaluation);

        if (!$obEvaluation->save()) {
            return false;
        }

        Event::dispatch(new OrderEvaluateEvent($obEvaluation));

        return true;
    }

    /**
     * Проверка оценки заказа
     */
    public function checkEvaluation(string $orderId): bool
    {
        return OrderEvaluation::where('order_id', $orderId)->exists();
    }

    /**
     * Получение списка вариантов отмены заказа
     * @return array<OrderCancelationVariantResponseModel>
     */
    public function getCancelationVariants(): array
    {
        return OrderCancelationVariant::pluck('reason')
            ->mapInto(OrderCancelationVariantResponseModel::class)
            ->all();
    }

    /**
     * Добавление клиентской отмены заказа
     */
    public function addCancelation(OrderCancelationRequestModel $dto): void
    {
        OrderCancellation::create((array) $dto);
    }
}
