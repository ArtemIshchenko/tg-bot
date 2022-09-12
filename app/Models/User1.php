<?php

namespace App\Models;

use App\Helpers\generate\Generate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class User1 extends Model
{
    use HasFactory;

    const MAX_HASH_SIZE = 9;
    const STATUS = [
        'disabled' => 0,
        'enabled' => 1,
        'blocked' => 2,
        'deleted' => 3,
    ];
    const NUM_MAX = 10;
    const COUNT_ANSWER = 3;

    public static function getHash()
    {
        $code  = '';
        for($i=0;$i<100000;$i++) {
            $code = Generate::generateMixCode(self::MAX_HASH_SIZE);
            $model = self::where('hash', $code)->first();
            if(is_null($model) || empty($model)) {
                break;
            }
        }
        return $code;
    }

    /**
     * @property-description Создание капча вопроса
     * @param User1 $user
     * @param bool $isValid
     * @return array
     */
    public static function createCaptchaQuestion($user, $isValid) {
        $result = [];
        $firstNum = rand(0, self::NUM_MAX);
        $secondNum = rand(1, self::NUM_MAX);
        $answer = $firstNum + $secondNum;

        $capchaAnswer = rand(1, self::COUNT_ANSWER);

        if ($isValid) {
            $user->is_capcha_checked = 1;
        } else {
            $message = "Для проверки, что вы не робот, решите пример: \n $firstNum+$secondNum=";
            $buttons = [];
            $btns = [];
            for ($i = 1; $i <= self::COUNT_ANSWER; $i++) {
                if ($i == $capchaAnswer) {
                    $btns[] = ['text' => $answer, 'callback_data' => "answer{$i}_valid"];
                } else {
                    do {
                        $r = rand(1, self::NUM_MAX * 2);
                    } while ($r == $answer);
                    $btns[] = ['text' => $r, 'callback_data' => "answer{$i}_invalid"];
                }
            }

            $buttons[] = $btns;
            $result = [
                'message' => $message,
                'buttons' => $buttons,
            ];
        }

        $user->save();

        return $result;
    }

    /**
     * @property-description Основная информация по пользователю
     * @param User $user
     * @return array
     */
    public static function getInfo($user) {
        $userLabel = "User id: {$user->user_id}
                      Псевдоним: {$user->username}
                      ФИО:" . self::getFio($user);
        return [
            'id' => $user->id,
            'userLabel' => $userLabel,
            'joinGroupCount' => $user->join_group_count,
            'subscribeCount' => $user->subscribe_count,
            'bonusCount' => $user->bonus_count,
            'referralsEarned' => $user->referrals_earned,
            'expectedToPay' => $user->expected_to_pay,
            'outputAmount' => $user->output_amount,
            'earned' => $user->earned,
            'balance' => $user->balance,
        ];
    }

    /**
     * @property-description Никнейм
     * @param User1 $user
     * @return string
     */
    public static function getNickname($user) {
        $nickname = '';
        if (!empty($user->username)) {
            $nickname = $user->username;
        } else {
            if (!empty($user->first_name)) {
                $nickname .= $user->first_name;
            }
            if (!empty($user->last_name)) {
                if (!empty($nickname)) {
                    $nickname .= ' ';
                }
                $nickname .= $user->last_name;
            }
        }
        return $nickname;
    }

    /**
     * @property-description ФИО
     * @param User1 $user
     * @return string
     */
    public static function getFio($user) {
        $fio = '';
        if (!empty($user->first_name)) {
            $fio .= $user->first_name;
        }
        if (!empty($user->last_name)) {
            if (!empty($fio)) {
                $fio .= ' ';
            }
            $fio .= $user->last_name;
        }

        return $fio;
    }

    /**
     * @property-description Партнерские ссылки
     * @param User1 $user
     * @param string $botName
     * @return string
     */
    public static function getPartnerLinks($user, $botName) {
        $bot = ltrim($botName, '@');
        return "https://t.me/$bot?start={$user->user_id}";
    }

    /**
     * @property-description Статус
     * @return array
     */
    public static function getStatusList() {
        return [
            self::STATUS['disabled'] => 'Неактивный',
            self::STATUS['enabled'] => 'Активный',
            self::STATUS['blocked'] => 'Заблокированный',
            self::STATUS['deleted'] => 'Удаленный',
        ];
    }
}
