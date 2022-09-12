<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Helpers\TgBot;
use App\Models\Referal;
use App\Models\Requisition;
use App\Models\Setting;
use App\Models\User1;
use App\Models\TgChannel;
use App\Models\TgGroup;
use App\Models\TgTask;
use App\Models\UserSubscribe;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TelegramBotController extends Controller
{

    const API_URL = 'https://api.telegram.org';
    const TOKEN = '889673981:AAEr8c6SXUan0-apuglTuFVt-M37QbBSvlM';
    const BOT = '@arhosaBot';

    protected $bot;
    protected $channel1;
    protected $channel2;
    protected $priceInvitedFriend;
    protected $priceGroupSubscribe;
    protected $priceChannelSubscribe;
    protected $priceBonuce;
    protected $priceTask;
    protected $priceReferal;
    protected $supportAkk;
    protected $orderAdvAkk;
    protected $payoutLink;
    protected $txtByBtnInfo;


    public function index() {
        try {
            $this->_init();
            $this->_handler();
        } catch (\Exception $e) {
            Log::error('error', ['message' => $e->getMessage(), 'code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile(), 'trace' => $e->getTraceAsString()]);
        }
    }

    protected function _init() {
        $this->bot = (new TgBot)
                        ->setApiUrl(self::API_URL)
                        ->setToken(self::TOKEN);
        $this->channel1 = Setting::getValByName('channel1ForRequiredSubscribe');
        $this->channel2 = Setting::getValByName('channel2ForRequiredSubscribe');
        $this->priceInvitedFriend = Setting::getValByName('priceInvitedFriend');
        $this->priceGroupSubscribe = Setting::getValByName('priceGroupSubscribe');
        $this->priceChannelSubscribe = Setting::getValByName('priceChannelSubscribe');
        $this->priceBonuce = Setting::getValByName('priceBonuce');
        $this->priceTask = Setting::getValByName('priceTask');
        $this->priceReferal = Setting::getValByName('priceReferal');
        $this->supportAkk = Setting::getValByName('supportAkk');
        $this->orderAdvAkk = Setting::getUrl(Setting::getValByName('orderAdvAkk'));
        $this->payoutLink = Setting::getValByName('payoutLink');
        $this->txtByBtnInfo = Setting::getValByName('txtByBtnInfo');

    }

    protected function _handler()
    {
        $content = file_get_contents("php://input");
        $update = json_decode($content, true);
        Log::debug($update);
        if (!$update) {
            exit;
        }

        try {
            if (isset($update["message"])) {
                $message = $update['message'];
                $userData = $message['from'];
                $messageId = $message['message_id'];
                $userId = $userData['id'];
                $text = isset($message['text']) ? $message['text'] : '';
            } elseif (isset($update['callback_query'])) {
                $userData = $update['callback_query']['from'];
                $message = $update['callback_query']['message'];
                $userId = $userData['id'];
                $text = '';
            }

            /**
             * Если новый пользоватаель
             * Регаем и начинаем стартовое общение
             */
            $userId = intval($userId);
            $user = User1::where('user_id', $userId)->first();
            if (is_null($user)) {
                if (!$this->_registration($userData, $text)) {
                    exit;
                }

                $user = User1::where('user_id', $userId)->first();
                $user->is_start_button = 1;
                $user->save();

                $this->_start($userId);
                exit;
            } elseif ($user->is_capcha_checked < 1) {
                if (isset($update['callback_query']['data'])) {
                    $answerStr = $update['callback_query']['data'];
                    $answerArr = explode('_', $answerStr);
                    $isValid = $answerArr[1] == 'valid' ? true : false;
                    $this->_start($userId, $isValid);
                    $user = User1::where('user_id', $userId)->first();
                    if ($user->is_capcha_checked > 0) {
                        $this->_subscribe($userId);
                    }
                }
                exit;
            } elseif ($user->is_subscribe_checked < 1) {
                if (isset($update['callback_query']['data'])) {
                    $subscribeQuery = false;
                    if ($update['callback_query']['data'] == 'subscribe-check') {
                        $subscribeQuery = true;
                    }
                    $this->_subscribe($userId, $subscribeQuery);
                    $user = User1::where('user_id', $userId)->first();
                    if ($user->is_subscribe_checked > 0) {
                        $this->_step3($userId, '', '', 'text');
                    }
                }
                exit;
            } else {
                $callbackData = '';
                if (isset($update['callback_query']['data'])) {
                    $callbackData = $update['callback_query']['data'];
                }
                $callbackQueryId = '';
                if (isset($update['callback_query']['id'])) {
                    $callbackQueryId = $update['callback_query']['id'];
                }
                $this->_step3($userId, $callbackData, $callbackQueryId, $text);
                exit;
            }
        } catch (\Exception $e) {
            Log::error('message: ' . $e->getMessage());
        }
    }

    /**
     * Регистрация пользователя
     * @param $user
     * @param $text
     * @return bool
     */
    protected function _registration($user, $text)
    {
        try {

            $model = new User1;
            $model->user_id = $user['id'];
            $refererArr = explode(' ', $text);
            $model->referer_id = isset($refererArr[1]) ? $refererArr[1] : '';
            $model->hash = User1::getHash();
            $model->lang = $user['language_code'];
            $model->first_name = isset($user['first_name']) ? $user['first_name'] : '';
            $model->last_name = isset($user['last_name']) ? $user['last_name'] : '';
            $model->username = isset($user['username']) ? $user['username'] : '';
            $model->status = User1::STATUS['enabled'];
            $model->is_start_button = 0;
            $model->is_capcha_checked = 0;
            $model->is_subscribe_checked = 0;
            $model->subscribe_count = 0;
            $model->join_group_count = 0;
            $model->bonus_count = 0;
            $model->referrals_earned = 0.00;
            $model->expected_to_pay = 0.00;
            $model->output_amount = 0.00;
            $model->earned = 0.00;
            $model->balance = 0.00;

            $validator = Validator::make([
                'user_id' => $model->user_id,
                'lang' => $model->lang,
                'first_name' => $model->first_name,
                'last_name' => $model->last_name,
                'username' => $model->username,

            ], [
                'user_id' => 'required|unique:user1s',
                'lang' => 'string|alpha|max:3',
                'first_name' => 'string',
                'last_name' => 'string',
                'username' => 'string',
            ]);
            if (!$validator->fails() && $model->save()) {
                if (!empty($model->referer_id)) {
                    $referer = User1::where('user_id', $model->referer_id)->first();
                    if (!is_null($referer)) {
                        $referal = new Referal;
                        $referal->user1_id = $referer->id;
                        $referal->user_id = $referer->user_id;
                        $referal->referal_id = $model->user_id;
                        $referal->first_name = $referer->first_name;
                        $referal->last_name = $referer->last_name;
                        $referal->username = $referer->username;
                        $referal->bonus_is_received = 1;
                        if ($referal->save()) {
                            Referal::payoutBonus($model, $this->priceReferal);
                        }
                    }
                }
            } else {
                Log::error('save error');
                return false;
            }
            return true;

        } catch (\Exception $e) {
            Log::error('Registration exception: ' . $e->getLine() . $e->getTraceAsString());
        }
    }

    protected function _start($userId, $isValid = false)
    {
        $user = User1::where('user_id', $userId)->first();
        if (!is_null($user)) {

            $question = User1::createCaptchaQuestion($user, $isValid);
            if (!empty($question)) {
                $message = $question['message'];
                $buttons = $question['buttons'];

                $this->bot->sendMessage($userId, $message, $buttons);
            }
        }
    }

    protected function _subscribe($userId, $subscribeQuery = false)
    {
        if ($subscribeQuery) {
            $user = User1::where('user_id', $userId)->first();
            if (!is_null($user)) {
                $message = '';
                $ch1IsMis = false;
                $ch2IsMis = false;
                try {
                    $info1 = $this->bot->getChatMember($this->channel1, $user->user_id);
                    if (!isset($info1->ok) || ($info1->ok !== true) || (isset($info1->result, $info1->result->status) && (!in_array($info1->result->status, ['member', 'creator'])))) {
                        $ch1IsMis = true;
                    }
                } catch (\Exception $e) {
                    $ch1IsMis = true;
                }
                try {
                    $info2 = $this->bot->getChatMember($this->channel2, $user->user_id);
                    if (!isset($info2->ok) || ($info2->ok !== true) || (isset($info2->result, $info2->result->status) && (!in_array($info2->result->status, ['member', 'creator'])))) {
                        $ch2IsMis = true;
                    }
                } catch (\Exception $e) {
                    $ch2IsMis = true;
                }
                if ($ch1IsMis && $ch2IsMis) {
                    $message = 'Вы не подписались на каналы ' . $this->channel1 . ' и ' . $this->channel2;
                } elseif ($ch1IsMis) {
                    $message = 'Вы не подписались на канал ' . $this->channel1;
                } elseif ($ch2IsMis) {
                    $message = 'Вы не подписались на канал ' . $this->channel2;
                } else {
                    $user = User1::where('user_id', $userId)->first();
                    if (!is_null($user)) {
                        $user->is_subscribe_checked = 1;
                        $user->subscribe_count += 2;
                        $user->save();
                    }
                    return true;
                }
                $message .= "\nПодпишитесь и нажмите проверить";
            }
        } else {
            $message = "Для верификации профиля подпишитесь на два канала и нажмите проверить \n" . $this->channel1 . "\t" . $this->channel2;
        }
        $buttons = [[
            ['text' => 'Проверить', 'callback_data' => 'subscribe-check'],
        ]];

        $this->bot->sendMessage($userId, $message, $buttons);
        return false;
    }

    protected function _step3($userId, $callbackData = '', $callbackQueryId = '', $text = '')
    {
        $isInlineKeyboard = true;
        $message = '';
        $buttons = [];

        $user = User1::where('user_id', $userId)->first();
        $requisition = Requisition::where('user_id', $userId)->where('status', Requisition::STATUS['nothing'])->first();
        if (!is_null($user)) {
            if (empty($callbackData)) {
                if (!empty($text)) {
                    if (mb_strpos($text, 'Заработать') !== false) {
                        $channelCount = TgChannel::where('status', TgChannel::STATUS['enabled'])->count();
                        $groupCount = TgGroup::where('status', TgGroup::STATUS['enabled'])->count();
                        $taskCount = TgTask::where('status', TgTask::STATUS['enabled'])->count();
                        $priceInvitedFriend = Setting::fFormat($this->priceInvitedFriend);
                        $message = "\xF0\x9F\x9A\x80 Как Вы хотите заработать?" . "\n\n" .
                            "\xF0\x9F\x92\xB0 Доступно:\n" .
                            "\xF0\x9F\x93\xA2 Подписаться на канал: $channelCount\n" .
                            "\xF0\x9F\x91\xA4 Вступить в группу: $groupCount\n" .
                            "\xF0\x9F\x93\x9D Расширенных заданий: $taskCount\n" .
                            "\xF0\x9F\x91\xA6 Пригласить друга: $priceInvitedFriend&#8381;";
                        $buttons = [
                            [
                                ['text' => "\xF0\x9F\x93\xA2 Подписаться на канал", 'callback_data' => 'subscribe-channel'],
                                ['text' => "\xF0\x9F\x91\xA4 Вступить в группу", 'callback_data' => 'join-group'],
                            ],
                            [
                                ['text' => "\xF0\x9F\x93\x9D Расширенное задание", 'callback_data' => 'get-task'],
                                ['text' => "\xF0\x9F\x91\xA6 Пригласить друга", 'callback_data' => 'invite-friend'],
                            ],
                        ];
                    } elseif (mb_strpos($text, 'Личный кабинет') !== false) {
                        $userInfo = User1::getInfo($user);
                        $message = "\xF0\x9F\x93\xB1 Ваш кабинет:\n\n" .
                            "\xF0\x9F\x94\x91 Мой id: {$userInfo['id']}\n" .
                            "\xF0\x9F\x91\xA5 Сделано подписок (группа): {$userInfo['joinGroupCount']}\n" .
                            "\xF0\x9F\x91\xA5 Сделано подписок (канал): {$userInfo['subscribeCount']}\n" .
                            "\xF0\x9F\x8E\x81 Получено бонусов: {$userInfo['bonusCount']}\n" .
                            "--------------------------------------------------------\n" .
                            "\xF0\x9F\x91\xA4 Заработано рефералов: {$userInfo['referralsEarned']}&#8381;\n" .
                            "\xE2\x8F\xB3 Ожидается к выплате: {$userInfo['expectedToPay']}&#8381;\n" .
                            "\xF0\x9F\x92\xB3 Выведено всего: {$userInfo['outputAmount']}&#8381;\n" .
                            "\xF0\x9F\x92\xB8 Заработано всего: {$userInfo['earned']}&#8381;\n" .
                            "--------------------------------------------------------\n" .
                            "\xF0\x9F\x92\xB0 Основной баланс: {$userInfo['balance']}&#8381;\n";
                        $buttons = [
                            [
                                ['text' => "Вывести", 'callback_data' => 'withdraw_money'],
                            ],
                        ];
                    } elseif (mb_strpos($text, 'Мои рефералы') !== false) {
                        $refererNickname = '';
                        $referer = User1::where('user_id', $user->referer_id)->first();
                        if (!is_null($referer)) {
                            $refererNickname = User1::getNickname($referer);
                        }
                        $referals = Referal::getList($user->user_id);
                        $partnerLinks = User1::getPartnerLinks($user, self::BOT);
                        $priceReferal = Setting::fFormat($this->priceReferal);
                        $message = "\xF0\x9F\x9A\xB8 Реферальная система: \xF0\x9F\x9A\xB8 ----------\n" .
                            "\xE2\x9C\x85 Платим {$priceReferal}&#8381; на баланс в боте за реферала\n" .
                            "\xE2\x9A\xA0 Условие акции:\n" .
                            "\xE2\x9D\x97 {$priceReferal}&#8381; начисляется после того как заработает - 0.50&#8381; в боте\n" .
                            "----------------------------------------------------------\n" .
                            "\xF0\x9F\x91\xA4 Вас привел: {$refererNickname}\n" .
                            "\xF0\x9F\x91\xA4 Ваши рефералы:\n" .
                            $referals .
                            "----------------------------------------------------------\n" .
                            "\xF0\x9F\x93\x8E Ваши партнерские ссылки:\n" .
                            $partnerLinks . "\n" .
                            "\xE2\x9D\x97 Приводи друзей - зарабатывайте вместе \xF0\x9F\x91\x8D \xF0\x9F\x92\xB0";
                        $buttons = [
                            [
                                ['text' => "Вывести", 'callback_data' => 'withdraw_money'],
                            ],
                        ];
                    } elseif (mb_strpos($text, 'Информация') !== false) {
                        $message = $this->txtByBtnInfo;
                    } elseif (mb_strpos($text, 'Выплаты') !== false) {
                        $message = "Перейдите по ссылке ниже: \xE2\xAC\x87";
                        $buttons = [
                            [
                                ['text' => "Выплаты", 'callback_data' => 'payouts', 'url' => $this->payoutLink],
                            ],
                        ];
                    } elseif (mb_strpos($text, 'Заказать рекламу/Продвигать свои ТГ проекты') !== false) {
                        $message = "Перейдите по ссылке ниже: \xE2\xAC\x87";
                        $buttons = [
                            [
                                ['text' => "Заказ рекламы/Продвижение своих ТГ проектов", 'callback_data' => 'payouts', 'url' => $this->orderAdvAkk],
                            ],
                        ];
                    } elseif (!is_null($requisition) && ($requisition->status == Requisition::STATUS['nothing']) && empty($requisition->wallet)) {
                        $validator = Validator::make(['text' => $text], [
                            'text' => [
                                'string',
                                'alpha_num',
                                'max:128',
                            ],
                        ]);

                        if ($validator->fails()) {
                            $message = "Ошибка! Введены недопустимые символы";
                        } else {
                            $requisition->wallet = $text;
                            $requisition->save();
                            $message = "\xF0\x9F\x92\xB0 На Вашем балансе {$user->balance}&#8381;\n" .
                                "Укажите сумму для вывода:";
                        }
                    } elseif (!is_null($requisition) && ($requisition->status == Requisition::STATUS['nothing']) && !empty($requisition->wallet)) {
                        if (is_numeric($text)) {
                            $amount = floatval($text);
                            if ($amount < 20) {
                                $message = "Ошибка! Минимальная сумма для вывода: 20&#8381;";
                            } elseif ($amount > $user->balance) {
                                $message = "Ошибка! Введенная сумма превашает Ваш баланс";
                            } else {
                                $requisition->amount_to_output = $amount;
                                $requisition->status = Requisition::STATUS['waiting'];
                                if ($requisition->save()) {
                                    $user->balance -= $amount;
                                    if ($user->balance < 0) {
                                        $user->balance = 0;
                                    }
                                    $user->save();
                                    $message = "Заявка создана и будет обработана в течении 24-х часов";
                                    $buttons = [
                                        [
                                            ['text' => "\xE2\xAC\x85 На главную страницу", 'callback_data' => 'to-main-page'],
                                        ],
                                    ];
                                }
                            }
                        } elseif ($text != 'text') {
                            $message = "Ошибка! Укажите сумму в цифрах";
                        }
                    } else {
                        $mainPage = $this->_mainPage();
                        $message = $mainPage['message'];
                        $buttons = $mainPage['buttons'];
                        $isInlineKeyboard = false;
                    }

                }
            } else {
                switch ($callbackData) {
                    case 'to-main-page':
                        $mainPage = $this->_mainPage();
                        $message = $mainPage['message'];
                        $buttons = $mainPage['buttons'];
                        $isInlineKeyboard = false;
                        break;
                    case 'subscribe-channel':
                        $channel = TgChannel::where('status', TgChannel::STATUS['enabled'])->orderBy('id')->first();
                        if (!is_null($channel)) {
                            $linkChannel = TgChannel::getChannelLink($channel);
                            $urlChannel = TgChannel::getChannelUrl($channel);
                            $message = "\xE2\x9C\x85 Подпишись на канал:" . "\n" .
                                "\xE2\x9E\xA1 $linkChannel\n" .
                                "\xF0\x9F\x92\xB5 И получи вознаграждение!\n" .
                                "\xE2\x8F\xBA Задание для бота:\n" .
                                "\xF0\x9F\xA4\x96 " . self::BOT;
                            $buttons = [
                                [
                                    ['text' => "\xF0\x9F\x93\xB3 Перейти к каналу", 'callback_data' => 'go-to-channel', 'url' => $urlChannel],
                                ],
                                [
                                    ['text' => "\xF0\x9F\x92\xB5 Проверить подписку", 'callback_data' => 'subscribe-check-channel#' . $channel->id],
                                ],
                            ];
                        }
                        break;
                    case 'join-group':
                        $group = TgGroup::where('status', TgGroup::STATUS['enabled'])->orderBy('id')->first();
                        if (!is_null($group)) {
                            $linkGroup = TgGroup::getGroupLink($group);
                            $urlGroup = TgGroup::getGroupUrl($group);
                            $message = "\xE2\x9C\x85 Присоединись к группе:" . "\n" .
                                "\xE2\x9E\xA1 $linkGroup\n" .
                                "\xF0\x9F\x92\xB5 И получи вознаграждение!\n" .
                                "\xE2\x8F\xBA Задание для бота:\n" .
                                "\xF0\x9F\xA4\x96 " . self::BOT;
                            $buttons = [
                                [
                                    ['text' => "\xF0\x9F\x93\xB3 Перейти к группе", 'callback_data' => 'go-to-group', 'url' => $urlGroup],
                                ],
                                [
                                    ['text' => "\xF0\x9F\x92\xB5 Проверить подписку", 'callback_data' => 'subscribe-check-group#' . $group->id],
                                ],
                            ];
                        }
                        break;
                    case 'get-task':
                        break;
                    case 'invite-friend':
                        break;
                    case 'withdraw_money':
                        $userInfo = User1::getInfo($user);
                        $message = "\xF0\x9F\x91\xA4 Ваш баланс: {$userInfo['balance']}&#8381;\n" .
                            "\xF0\x9F\x92\xB3 Минимальная сумма для вывода: 20&#8381;\n" .
                            "Выберите способ вывода:";
                        $buttons = [
                            [
                                ['text' => "Payeer", 'callback_data' => 'withdraw_payeer'],
                                ['text' => "Bitcoin", 'callback_data' => 'withdraw_bitcoin'],
                                ['text' => "Qiwi", 'callback_data' => 'withdraw_qiwi'],
                                ['text' => "PayPal", 'callback_data' => 'withdraw_paypal'],
                            ],
                            [
                                ['text' => "AdvCash (банковская карта кроме РФ и Беларусь)", 'callback_data' => 'withdraw_advcash'],
                            ],
                        ];
                        break;
                    case 'withdraw_payeer':
                        $requisition = Requisition::createModel(Requisition::PAYMENT_SISTEM['Payeer'], $user);
                        if ($requisition->save()) {
                            $message = "\xF0\x9F\x90\xA5 Введите номер Вашего Payeer кошелька:\n\n" .
                                "Пример: P1000000\xE2\xA4\xB5";
                        }
                        break;
                    case 'withdraw_bitcoin':
                        $requisition = Requisition::createModel(Requisition::PAYMENT_SISTEM['Bitcoin'], $user);
                        if ($requisition->save()) {
                            $message = "\xF0\x9F\x90\xA5 Введите номер Вашего Bitcoin кошелька:\n\n" .
                                "Пример: P1000000\xE2\xA4\xB5";
                        }
                        break;
                    case 'withdraw_qiwi':
                        $requisition = Requisition::createModel(Requisition::PAYMENT_SISTEM['Qiwi'], $user);
                        if ($requisition->save()) {
                            $message = "\xF0\x9F\x90\xA5 Введите номер Вашего Qiwi кошелька:\n\n" .
                                "Пример: P1000000\xE2\xA4\xB5";
                        }
                        break;
                    case 'withdraw_advcash':
                        $requisition = Requisition::createModel(Requisition::PAYMENT_SISTEM['AdvCash'], $user);
                        if ($requisition->save()) {
                            $message = "\xF0\x9F\x90\xA5 Введите номер Вашего AdvCash кошелька:\n\n" .
                                "Пример: P1000000\xE2\xA4\xB5";
                        }
                        break;
                    case 'withdraw_paypal':
                        $requisition = Requisition::createModel(Requisition::PAYMENT_SISTEM['PayPal'], $user);
                        if ($requisition->save()) {
                            $message = "\xF0\x9F\x90\xA5 Введите номер Вашего PayPal кошелька:\n\n" .
                                "Пример: P1000000\xE2\xA4\xB5";
                        }
                        break;
                }
                $callbackDataArr = explode('#', $callbackData);
                if (isset($callbackDataArr[1])) {
                    $id = intval($callbackDataArr[1]);
                    switch ($callbackDataArr[0]) {
                        case 'subscribe-check-channel':
                            $channel = TgChannel::where('id', $id)->where('status', TgChannel::STATUS['enabled'])->first();
                            if (!is_null($channel)) {
                                $link = TgChannel::getChannelLink($channel);
                                $isSubscribe = false;
                                $subscribed = UserSubscribe::checkSubscribe($user->user_id, $id, UserSubscribe::TYPE['channel']);
                                $isLimited = UserSubscribe::overLimit($id, UserSubscribe::TYPE['channel'], $channel->limit);
                                try {
                                    if (!$subscribed && !$isLimited) {
                                        $info = $this->bot->getChatMember($link, $user->user_id);
                                        if (isset($info->ok) && ($info->ok == true) && (isset($info->result, $info->result->status) && in_array($info->result->status, ['member', 'creator']))) {
                                            $isSubscribe = true;
                                        }
                                    }
                                } catch (\Exception $e) {
                                }
                                if (!$subscribed && $isSubscribe) {
                                    $user->subscribe_count += 1;
                                    $user->balance += $this->priceChannelSubscribe;
                                    if ($user->save()) {
                                        Referal::payoutBonus($user, $this->priceReferal);
                                        UserSubscribe::add($user->user_id, $id, UserSubscribe::TYPE['channel']);
                                    }

                                    $nextChannelId = TgChannel::getNextChannelId($id);
                                    if (!empty($nextChannelId)) {
                                        $message = "За подписку на канал Вам зачислено {$this->priceChannelSubscribe}&#8381;\n" .
                                            "Ваш баланс: {$user->balance}&#8381;";
                                        $buttons = [
                                            [
                                                ['text' => "ДАЛЕЕ", 'callback_data' => "subscribe-check-channel#$nextChannelId"],
                                            ],
                                        ];
                                    } else {
                                        $message = "Закончились каналы, на которые нужно подписаться. Посмотрите пожалуйста задания на которых можно больше заработать, а также приглашайте друзей и зарабатывайте на том, что они запустили бота.";
                                    }
                                } elseif ($isLimited || $subscribed) {
                                    $message = "Закончились каналы, на которые нужно подписаться. Посмотрите пожалуйста задания на которых можно больше заработать, а также приглашайте друзей и зарабатывайте на том, что они запустили бота.";
                                } else {
                                    $this->bot->answerCallbackQuery($callbackQueryId, "\xE2\x9D\x97 Ошибка \xE2\x9D\x97\n\nВы не вступили в канал!", true);
                                    exit;
                                }
                            } else {
                                $message = "Закончились каналы, на которые нужно подписаться. Посмотрите пожалуйста задания на которых можно больше заработать, а также приглашайте друзей и зарабатывайте на том, что они запустили бота.";
                            }
                            break;
                        case 'subscribe-check-group':
                            $group = TgGroup::where('id', $id)->where('status', TgGroup::STATUS['enabled'])->first();
                            if (!is_null($group)) {
                                $link = TgGroup::getGroupLink($group);
                                $isSubscribe = false;
                                $subscribed = UserSubscribe::checkSubscribe($user->user_id, $id, UserSubscribe::TYPE['group']);
                                $isLimited = UserSubscribe::overLimit($id, UserSubscribe::TYPE['group'], $group->limit);
                                try {
                                    if (!$subscribed && !$isLimited) {
                                        $info = $this->bot->getChatMember($link, $user->user_id);
                                        if (isset($info->ok) && ($info->ok == true) && (isset($info->result, $info->result->status) && in_array($info->result->status, ['member', 'creator']))) {
                                            $isSubscribe = true;
                                        }
                                    }
                                } catch (\Exception $e) {
                                }
                                if (!$subscribed && $isSubscribe) {
                                    $user->join_group_count += 1;
                                    $user->balance = $user->balance + $this->priceGroupSubscribe;
                                    if ($user->save()) {
                                        Referal::payoutBonus($user, $this->priceReferal);
                                        UserSubscribe::add($user->user_id, $id, UserSubscribe::TYPE['group']);
                                    }
                                    $nextGroupId = TgGroup::getNextGroupId($id);
                                    if (!empty($nextGroupId)) {
                                        $message = "За присоединение к группе Вам зачислено {$this->priceGroupSubscribe}&#8381;\n" .
                                            "Ваш баланс: {$user->balance}&#8381;";
                                        $buttons = [
                                            [
                                                ['text' => "ДАЛЕЕ", 'callback_data' => "subscribe-check-group#$nextGroupId"],
                                            ],
                                        ];
                                    } else {
                                        $message = "Закончились группы, к которым нужно присоединиться. Посмотрите пожалуйста задания на которых можно больше заработать, а также приглашайте друзей и зарабатывайте на том, что они запустили бота.";
                                    }
                                } elseif ($isLimited || $subscribed) {
                                    $message = "Закончились группы, к которым нужно присоединиться. Посмотрите пожалуйста задания на которых можно больше заработать, а также приглашайте друзей и зарабатывайте на том, что они запустили бота.";
                                } else {
                                    $this->bot->answerCallbackQuery($callbackQueryId, "\xE2\x9D\x97 Ошибка \xE2\x9D\x97\n\nВы не присоединились к группе!", true);
                                    exit;
                                }
                            } else {
                                $message = "Закончились группы, к которым нужно присоединиться. Посмотрите пожалуйста задания на которых можно больше заработать, а также приглашайте друзей и зарабатывайте на том, что они запустили бота.";
                            }
                            break;
                    }
                }
            }
            if (!empty($message)) {
                $this->bot->sendMessage($userId, $message, $buttons, $isInlineKeyboard);
            }
        }
    }

    protected function _mainPage() {
        $priceInvitedFriend = Setting::fFormat($this->priceInvitedFriend);
        $priceGroupSubscribe = Setting::fFormat($this->priceGroupSubscribe);
        $priceChannelSubscribe = Setting::fFormat($this->priceChannelSubscribe);
        $priceBonuce = Setting::fFormat($this->priceBonuce);
        $priceTask = Setting::fFormat($this->priceTask);
        return [
            'message' => "\xF0\x9F\x9A\x80 Приветствуем Вас в сервисе " . self::BOT . "\n" .
                "\xF0\x9F\x92\xB0 Лучший бот для заработка в Telegram!\n\n" .
                "\xF0\x9F\x92\xB8 Мы платим:\n" .
                "---------------------------------------------------------\n" .
                "\xF0\x9F\x9A\xB8 {$priceInvitedFriend}&#8381; за друга приглашенного друга\n" .
                "\xF0\x9F\x91\xA5 {$priceGroupSubscribe}&#8381; за подписку на группу\n" .
                "\xF0\x9F\x91\xA4 {$priceChannelSubscribe}&#8381; за подписку на канал\n" .
                "\xF0\x9F\x8E\x81 {$priceBonuce}&#8381; за бонус\n" .
                "\xF0\x9F\x93\x9D {$priceTask}&#8381;+ за задание\n" .
                "---------------------------------------------------------\n" .
                "Тех.поддержка 9:00-18:00" . " {$this->supportAkk}",
            'buttons' => [
                [
                    ['text' => "\xF0\x9F\x92\xB3 Заработать"],
                    ['text' => "\xF0\x9F\x94\x90 Личный кабинет"],
                ],
                [
                    ['text' => "\xF0\x9F\xA7\x91\xE2\x80\x8D\xF0\x9F\x92\xBB Мои рефералы"],
                    ['text' => "\xE2\x84\xB9 Информация"],
                ],
                [
                    ['text' => "\xF0\x9F\x92\xB0 Выплаты"],
                ],
                [
                    ['text' => "\xF0\x9F\x9A\x80 Заказать рекламу/Продвигать свои ТГ проекты"],
                ],
            ],
        ];
    }
}
