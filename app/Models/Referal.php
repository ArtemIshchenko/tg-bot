<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Referal extends Model
{
    use HasFactory;

    /**
     * @property-description Рефералы
     * @param string $user_id
     * @return string
     */
    public static function getList($user_id) {
        $res = '';
        $referalsIds = DB::table('referals')->where('user_id', $user_id)->orderBy('created_at')->pluck('referal_id');

        $referals = User1::whereIn('user_id', $referalsIds)->get();
        foreach ($referals as $referal) {
            $nickname = self::getNickname($referal);
            $res .= "$nickname - Бонус за реферала получен\n";
//            if ($referal->balance < 0.5) {
//                $res .= "$nickname - Реферал не заработал еще 0.50&#8381;\n";
//            }
        }

        return $res;
    }

    /**
     * @property-description Никнейм
     * @param Referal $referal
     * @return string
     */
    public static function getNickname($referal) {
        $nickname = '';
        if (!empty($referal->username)) {
            $nickname = $referal->username;
        } else {
            if (!empty($referal->first_name)) {
                $nickname .= $referal->first_name;
            }
            if (!empty($referal->last_name)) {
                if (!empty($nickname)) {
                    $nickname .= ' ';
                }
                $nickname .= $referal->last_name;
            }
        }
        return $nickname;
    }

    /**
     * @property-description Начисление бонуса за реферала
     * @param User $referal
     * @param float $priceReferal
     * @return string
     */
    public static function payoutBonus($referal, $priceReferal) {
//        if (($referal->is_referer_bonus_payout == 0) && ($referal->balance >= 0.5)) {
        if ($referal->is_referer_bonus_payout == 0) {
            $referer = User1::where('user_id', $referal->referer_id)->first();
            if (!is_null($referer)) {
                $referer->bonus_count += 1;
                $referer->referrals_earned += $priceReferal;
                $referer->earned += $priceReferal;
                $referer->balance += $priceReferal;
                if ($referer->save()) {
                    $referal->is_referer_bonus_payout = 1;
                    $referal->save();
                }
            }
        }
    }
}
