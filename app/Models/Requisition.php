<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    const STATUS = [
        'nothing' => 0,
        'waiting' => 1,
        'done' => 2,
    ];

    const PAYMENT_SISTEM = [
        'nothing' => 0,
        'Payeer' => 1,
        'Bitcoin' => 2,
        'Qiwi' => 3,
        'AdvCash' => 4,
        'PayPal' => 5,
    ];

    /**
     * @property-description Статус
     * @return array
     */
    public static function getStatusList() {
        return [
            self::STATUS['nothing'] => 'Неопределенный',
            self::STATUS['waiting'] => 'В ожидании',
            self::STATUS['done'] => 'Проведено',
        ];
    }

    /**
     * @property-description Список систем оплаты
     * @return array
     */
    public static function getPaymentSistemList() {
        return [
            self::PAYMENT_SISTEM['Payeer'] => 'Payeer',
            self::PAYMENT_SISTEM['Bitcoin'] => 'Bitcoin',
            self::PAYMENT_SISTEM['Qiwi'] => 'Qiwi',
            self::PAYMENT_SISTEM['AdvCash'] => 'AdvCash',
            self::PAYMENT_SISTEM['PayPal'] => 'PayPal',
        ];
    }

    /**
     * @property-description Создание и инициализация заявки
     * @param integer $paymentSystem
     * @param User $user
     * @return Requisition
     */
    public static function createModel($paymentSystem, $user) {
        $requisition = new self;
        $requisition->user1_id = $user->id;
        $requisition->user_id = $user->user_id;
        $requisition->wallet = '';
        $requisition->payment_system = Requisition::PAYMENT_SISTEM['Payeer'];
        $requisition->amount_to_output = 0;
        $requisition->status = Requisition::STATUS['nothing'];

        return $requisition;
    }
}
