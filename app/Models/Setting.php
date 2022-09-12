<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    const TYPE = [
        'input' => 1,
        'checkbox' => 2,
        'textarea' => 3,
    ];

    const SECTION = [
        'step2' => 1,
        'step3' => 2,
        'step3_1' => 3,
        'step3_2' => 4,
        'step3_3' => 5,
        'step3_4' => 6,
    ];

    const SETTINGS = [
        1 => ['name' => 'channel1ForRequiredSubscribe', 'section' => self::SECTION['step2'], 'description' => 'Ссылка для канала 1 для обязательной подписки', 'type' => self::TYPE['input'],  'defaultValue' => ''],
        2 => ['name' => 'channel2ForRequiredSubscribe', 'section' => self::SECTION['step2'], 'description' => 'Ссылка для канала 2 для обязательной подписки', 'type' => self::TYPE['input'],  'defaultValue' => ''],
        3 => ['name' => 'priceInvitedFriend', 'section' => self::SECTION['step3'], 'description' => 'Цена за друга приглашенного друга', 'type' => self::TYPE['input'],  'defaultValue' => 0.0],
        4 => ['name' => 'priceGroupSubscribe',  'section' => self::SECTION['step3'], 'description' => 'Цена за подписку на группу', 'type' => self::TYPE['input'],  'defaultValue' => 0.0],
        5 => ['name' => 'priceChannelSubscribe', 'section' => self::SECTION['step3'], 'description' => 'Цена за подписку на канал', 'type' => self::TYPE['input'],  'defaultValue' => 0.0],
        6 => ['name' => 'priceBonuce', 'section' => self::SECTION['step3'], 'description' => 'Цена за бонус', 'type' => self::TYPE['input'],  'defaultValue' => 0.0],
        7 => ['name' => 'priceTask', 'section' => self::SECTION['step3'], 'description' => 'Цена за задание', 'type' => self::TYPE['input'],  'defaultValue' => 0.0],
        8 => ['name' => 'supportAkk', 'section' => self::SECTION['step3'], 'description' => 'Акк тг тех.поддержки', 'type' => self::TYPE['input'],  'defaultValue' => ''],
        9 => ['name' => 'priceReferal', 'section' => self::SECTION['step3_1'], 'description' => 'Цена за реферала', 'type' => self::TYPE['input'],  'defaultValue' => 0.0],
        10 => ['name' => 'payoutLink', 'section' => self::SECTION['step3_3'], 'description' => 'Ссылка для перехода при нажатии на "Выплаты"', 'type' => self::TYPE['input'],  'defaultValue' => ''],
        11 => ['name' => 'orderAdvAkk', 'section' => self::SECTION['step3_4'], 'description' => 'Акк для перехода при нажатии "Заказать рекламу"', 'type' => self::TYPE['input'],  'defaultValue' => ''],
        12 => ['name' => 'txtByBtnInfo', 'section' => self::SECTION['step3_4'], 'description' => 'Текст при нажатии кнопки "Информация"', 'type' => self::TYPE['textarea'],  'defaultValue' => ''],
    ];

    /**
     * настройки
     * @return array
     */
    public static function getSettings()
    {
        $result = [];
        $settings = self::orderBy('number')->get();
        if ($settings->isEmpty()) {
            foreach (self::SETTINGS as $number => $item) {
                $model = new self;
                $model->name = $item['name'];
                $model->description = $item['description'];
                $model->value = $item['defaultValue'];
                $model->number = $number;
                if ($model->save()) {
                    $result[$model->number] = $model;
                }
            }
        } else {
            foreach (self::SETTINGS as $number => $item) {
                $isSet = false;
                foreach ($settings as $setting) {
                    if ($setting->name == $item['name']) {
                        if ($setting->number != $number) {
                            $setting->number = $number;
                            $setting->save();
                        }
                        $isSet = true;
                        $result[$number] = $setting;
                        break;
                    }
                }
                if (!$isSet) {
                    $model = new self;
                    $model->name = $item['name'];
                    $model->description = $item['description'];
                    $model->value = $item['defaultValue'];
                    $model->number = $number;
                    if ($model->save()) {
                        $result[$model->number] = $model;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        $retrieved = function ($model) {
            if (is_numeric($model->value)) {
                if (is_int($model->value + 0)) {
                    $model->value = intval($model->value);
                } elseif (is_float($model->value + 0)) {
                    $model->value = floatval($model->value);
                }
            }
        };

        $updating = function ($model) {
            if (strpos($model->value, ',') !== false) {
                $explode = explode(',', $model->value);
                if ((count($explode) == 2) && is_numeric($explode[0]) && is_numeric($explode[1])) {
                    $model->value = implode('.', $explode);
                }
            }
        };

        static::retrieved($retrieved);
        static::creating($updating);
        static::updating($updating);

    }

    /**
     * настройки
     * @param string $name
     * @return integer | false
     */
    public static function getTypeByName($name)
    {
        $type = false;
        foreach (self::SETTINGS as $setting) {
            if ($setting['name'] == $name) {
                $type = $setting['type'];
                break;
            }
        }
        return $type;
    }

    /**
     * описание по ид
     * @param integer $id
     * @return string
     */
    public static function getDescriptionById($id)
    {
        $description = '';
        $settingModel = self::find($id);
        if (!is_null($settingModel)) {
            foreach (self::SETTINGS as $setting) {
                if ($settingModel->name == $setting['name']) {
                    $description = $setting['description'];
                    break;
                }
            }
        }
        return $description;
    }

    /**
     * ид по name
     * @param string $name
     * @return integer
     */
    public static function getIdByName($name)
    {
        $id = 0;
        $settingModel = self::where('name', $name)->first();
        if (!is_null($settingModel)) {
            $id = $settingModel->id;
        }
        return $id;
    }

    /**
     * значение по name
     * @param string $name
     * @return string | false
     */
    public static function getValByName($name)
    {
        $val = false;
        $settingModel = self::where('name', $name)->first();
        if (!is_null($settingModel)) {
            $val = $settingModel->value;
        }
        return $val;
    }

    /**
     * тип по ид
     * @param integer $id
     * @return integer
     */
    public static function getTypeById($id)
    {
        $type = 0;
        $settingModel = self::find($id);
        if (!is_null($settingModel)) {
            foreach (self::SETTINGS as $setting) {
                if ($settingModel->name == $setting['name']) {
                    $type = $setting['type'];
                    break;
                }
            }
        }
        return $type;
    }

    /**
     * @description Форматирование дробного числа без лишних нулей
     * @param double $number
     * @param integer $countCharAfterComma
     * @property integer $showEndZero
     * @return string
     */
    public static function fFormat($number, $countCharAfterComma = 2, $showEndZero = true) {
        $number = rtrim($number, '0');
        $lastChar = substr($number, -1);
        if (in_array($lastChar, ['.'])) {
            $number = rtrim($number, '.');
        }

        if ($showEndZero) {
            $numArr = explode('.', $number);
            if (count($numArr) > 1) {
                if (strlen($numArr[1]) < $countCharAfterComma) {
                    $number = sprintf("%01.2F", $number);
                }
            } else {
                $number = sprintf("%01.2F", $number);
            }
        }

        return $number;
    }

    /**
     * @description Получение ссылки в ТГ
     * @param string $settingField
     * @return string
     */
    public static function getUrl($settingField) {
        if (strpos($settingField, '@') !== false) {
            $settingField = 'https://t.me/' . ltrim($settingField, '@');
        }

        return $settingField;
    }
}
