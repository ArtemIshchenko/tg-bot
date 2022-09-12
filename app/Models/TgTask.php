<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgTask extends Model
{
    use HasFactory;

    const STATUS = [
        'disabled' => 0,
        'enabled' => 1,
    ];

    public static function getStatusList() {
        return [
            self::STATUS['disabled'] => 'Неактивна',
            self::STATUS['enabled'] => 'Активна',
        ];
    }
}
