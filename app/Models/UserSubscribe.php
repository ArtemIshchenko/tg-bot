<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserSubscribe extends Model
{
    use HasFactory;

    const TYPE = [
        'nothing' => 0,
        'channel' => 1,
        'group' => 2,
    ];

    /**
     * @property-description Добавление подписки пользователя
     * @param string $userId
     * @param int $subscribeId
     * @param int $type
     * @return bool
     */
    public static function add($userId, $subscribeId, $type) {
        $result = false;
        $model = self::where('user_id', $userId)
                    ->where('subscribe_id', $subscribeId)
                    ->where('type', $type)
                    ->first();
        if (is_null($model)) {
            $model = new self;
            $model->user_id = $userId;
            $model->subscribe_id = $subscribeId;
            $model->type = $type;

            $validator = Validator::make([
                'user_id' => $model->user_id,
                'subscribe_id' => $model->subscribe_id,
                'type' => $model->type,
            ], [
                'user_id' => Rule::unique('user_subscribes')
                    ->where('subscribe_id', $subscribeId)
                    ->where('type', $type),
                'type' => 'integer',
            ]);
            if (!$validator->fails() && $model->save()) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @property-description Проверка достижения лимита подписок
     * @param int $subscribeId
     * @param int $type
     * @param int $limit
     * @return bool
     */
    public static function overLimit($subscribeId, $type, $limit)
    {
        $subscribsCount = self::where('subscribe_id', $subscribeId)
                            ->where('type', $type)
                            ->count();

        return $subscribsCount >= $limit;
    }

    /**
     * @property-description Количество подписок
     * @param int $subscribeId
     * @param int $type
     * @return int
     */
    public static function subscribeCount($subscribeId, $type)
    {
        return self::where('subscribe_id', $subscribeId)
            ->where('type', $type)
            ->count();
    }

    /**
     * @property-description Проверка подписки
     * @param string $userId
     * @param int $subscribeId
     * @param int $type
     * @return bool
     */
    public static function checkSubscribe($userId, $subscribeId, $type)
    {
        return self::where('user_id', $userId)
            ->where('subscribe_id', $subscribeId)
            ->where('type', $type)
            ->exists();
    }
}
