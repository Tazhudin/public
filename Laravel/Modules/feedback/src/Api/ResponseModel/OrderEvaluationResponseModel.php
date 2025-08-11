<?php

namespace Feedback\Api\ResponseModel;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $user_id
 */
final class OrderEvaluationResponseModel
{
    public function __construct(Model $object)
    {
        $this->cast($object);
    }

    public function cast(Model $object)
    {
        foreach ($object->toArray() as $key => $value) {
            $this->$key = $value;
        }
    }
}
