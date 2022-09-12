<?php


namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;

class TgBot
{
    const TYPE_GET = 'get';
    const TYPE_POST = 'post';

    /**
     * @var string $apiUrl
     */
    public $apiUrl;

    /**
     * @var string $token
     */
    public $token;

    /**
     * @var string $urlWebhook
     */
    public $urlWebhook;

    /**
     * @var integer $timeout
     */
    public $timeout = 5.0;

    /**
     * @var integer $readTimeout
     */
    public $readTimeout = 30;

    /**
     * @var integer $concurrency
     */
    public $concurrency = 5;

    /**
     * @var array $extendedConfig
     */
    public $extendedConfig = [];

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param string $apiUrl
     * @return TgBot
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return TgBot
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlWebhook()
    {
        return $this->urlWebhook;
    }

    /**
     * @param string $urlWebhook
     * @return TgBot
     */
    public function setUrlWebhook($urlWebhook)
    {
        $this->urlWebhook = $urlWebhook;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return TgBot
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getConcurrency()
    {
        return $this->concurrency;
    }

    /**
     * @param int $concurrency
     * @return TgBot
     */
    public function setConcurrency($concurrency)
    {
        $this->concurrency = $concurrency;
        return $this;
    }

    /**
     * @property-description Вебхук
     * @return mixed
     * @throws \Exception
     */
    public function setWebhook()
    {
        return $this->_invokeMethod('setWebhook', ['url' => $this->urlWebhook], self::TYPE_GET);
    }

    public function getWebhookInfo()
    {
        return $this->_invokeMethod('getWebhookInfo', [], self::TYPE_POST);
    }

    public function getChatMember($chat_id, $user_id)
    {
        return $this->_invokeMethod('getChatMember', ['chat_id' => $chat_id, 'user_id' => $user_id], self::TYPE_GET);
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }

    /**
     * Метод
     * @param $method
     * @param array $params
     * @param $type
     * @return mixed
     * @throws \Exception
     */
    protected function _invokeMethod($method, $params = [], $type = self::TYPE_POST)
    {
        if (!isset($this->apiUrl) || empty($this->apiUrl)) {
            throw new \Exception('Empty apiUrl' . "\nMethod: " . __METHOD__ . "\nLine: " . __LINE__, 500);
        }
        if (!isset($this->token) || empty($this->token)) {
            throw new \Exception('Empty token' . "\nMethod: " . __METHOD__ . "\nLine: " . __LINE__, 500);
        }
        if (!isset($method) || empty($method)) {
            throw new \Exception('Empty parameter method' . "\nMethod: " . __METHOD__ . "\nLine: " . __LINE__, 500);
        }

        $apiUrl = $this->apiUrl;
        if (mb_substr($apiUrl, -1) != '/') {
            $apiUrl = $this->apiUrl . '/';
        }

        switch ($type) {
            case self::TYPE_POST:
                $params['method'] = $method;

                $config = [
                    'base_uri' => $apiUrl . 'bot' . $this->token . '/',
                    'timeout'  => $this->timeout,
                    'read_timeout' => $this->readTimeout,
                ];
                if(is_array($this->extendedConfig) && !empty($this->extendedConfig)) {
                    $config = array_merge($config, $this->extendedConfig);
                }
                $client = new Client($config);

                $request = $client->post($method, [
                    RequestOptions::JSON => $params
                ]);
                $response = $request->getBody()->getContents();
                return json_decode($response);
                break;
            case self::TYPE_GET:

                $config = [
                    'timeout'  => $this->timeout,
                    'read_timeout' => $this->readTimeout,
                ];
                if(is_array($this->extendedConfig) && !empty($this->extendedConfig)) {
                    $config = array_merge($config, $this->extendedConfig);
                }
                $client = new Client($config);
                $query = [];
                if (is_array($params) && !empty($params)) {
                    $query = ['query' => $params];
                }
                $request = $client->get($apiUrl . 'bot' . $this->token . '/' . $method, $query);
                $response = $request->getBody()->getContents();
                return json_decode($response);
                break;
        }
    }

    /**
     * Вызов метода
     * @param $method
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    protected function _invokeAsyncMethod($method, $params = [])
    {
        if (!isset($this->token) || empty($this->token)) {
            throw new \Exception('Empty token' . "\nMethod: " . __METHOD__ . "\nLine: " . __LINE__, 500);
        }
        if (!isset($method) || empty($method)) {
            throw new \Exception('Empty parameter method' . "\nMethod: " . __METHOD__ . "\nLine: " . __LINE__, 500);
        }

        $apiUrl = $this->apiUrl;
        if (mb_substr($apiUrl, -1) != '/') {
            $apiUrl = $this->apiUrl . '/';
        }

        $config = [
            'base_uri' => $apiUrl . 'bot' . $this->token . '/',
            'timeout'  => $this->timeout,
            'read_timeout' => $this->readTimeout,
        ];
        if(is_array($this->extendedConfig) && !empty($this->extendedConfig)) {
            $config = array_merge($config, $this->extendedConfig);
        }
        $client = new Client($config);

        $requests = function ($total) use ($params, $method) {
            for ($i = 0; $i < $total; $i++) {
                $params[$i]['method'] = $method;
                yield new Request('POST', $method, ['Content-type' => 'application/json'], json_encode($params[$i]));
            }
        };

        $result = [];
        $pool = new Pool($client, $requests(count($params)), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function (ResponseInterface $response, $index) use (&$result) {
                $result[$index] = json_decode($response->getBody()->getContents());
            },
            'rejected' => function ($reason, $index) use (&$result) {
                if ($reason instanceof ClientException) {
                    $result[$index] = json_decode($reason->getResponse()->getBody()->getContents());
                }
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
        return $result;
    }

    /**
     * @property-description Подготовка отправки текстового сообщения
     * @param $chatId
     * @param $message
     * @param array $keyboard
     * @return mixed
     * @throws \Exception
     */
    public function prepareSendMessageKeyboard($chatId, $message, $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
            'reply_markup' => [
                'keyboard' => $keyboard,
                'one_time_keyboard' => false, //скрыть клаву после нажатия
                'resize_keyboard' => true,
                'selective' => false, //показать клаву только юзеру у которого прописано в сообщении параметр reply_to_message_id
            ],
        ];
        return $params;
    }

    /**
     * @property-description Подготовка отправки текстового сообщения с клавиатурой InlineKeyboardMarkup
     * @param $chatId
     * @param $message
     * @param array $keyboard
     * @return mixed
     * @throws \Exception
     */
    public function prepareSendMessageInlineKeyboard($chatId, $message, $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
            'reply_markup' => [
                'inline_keyboard' => $keyboard,
            ],
        ];
        return $params;
    }

    /**
     * @param $chatId
     * @param $message
     * @param array $keyboard
     * @return mixed
     * @throws \Exception
     */
    public function prepareSendCallbackQuery($chatId, $message, $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
            'reply_markup' => [
                'inline_keyboard' => $keyboard,
                'one_time_keyboard' => false,
                'resize_keyboard' => true,
                'selective' => false,
            ],
        ];
        return $params;
    }

    /**
     * @param $callbackQueryId
     * @param $text
     * @param $showAlert
     * @param $url
     * @param $cacheTime
     * @return mixed
     * @throws \Exception
     */
    public function prepareAnswerCallbackQuery($callbackQueryId, $text, $showAlert, $url = '', $cacheTime = 0)
    {
        $params = [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
            'url' => $url,
            'cache_time' => $cacheTime,
        ];
        return $params;
    }

    /**
     * @property-description Подготовка отправки текстового сообщения с клавиатурой InlineKeyboardMarkup
     * @param $chatId
     * @param $messageId
     * @param $message
     * @param array $keyboard
     * @return mixed
     * @throws \Exception
     */
    public function prepareSendReplyMessageInlineKeyboard($chatId, $messageId, $message, $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'reply_to_message_id' => $messageId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
            'reply_markup' => [
                'inline_keyboard' => $keyboard,
            ],
        ];
        return $params;
    }

    /**
     * @property-description Подготовока отправки фото с сервера Телеграм по его fileId
     * @param $chatId
     * @param $fileId
     * @param string $message
     * @param array $keyboard
     * @return mixed
     */
    public function prepareSendPhotoByFileId($chatId, $fileId, $message="", $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'photo' => $fileId,
        ];
        if(!empty($message)) {
            $params['caption'] = $message;
        }
        if(!empty($keyboard)) {
            $params['reply_markup'] = [
                'inline_keyboard' => $keyboard,
            ];
        }
        return $params;
    }

    /**
     * @property-description Подготовка к отправке документа с сервера Телеграм по его fileId
     * @param $chatId
     * @param $fileId
     * @param string $message
     * @param array $keyboard
     * @return mixed
     */
    public function prepareSendDocumentByFileId($chatId, $fileId, $message="", $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'document' => $fileId,
        ];
        if(!empty($message)) {
            $params['caption'] = $message;
        }
        if(!empty($keyboard)) {
            $params['reply_markup'] = [
                'inline_keyboard' => $keyboard,
            ];
        }
        return $params;
    }

    /**
     * @property-description Подготовка к отправке аудио с сервера Телеграм по его fileId
     * @param $chatId
     * @param $fileId
     * @param string $message
     * @param array $keyboard
     * @return mixed
     */
    public function prepareSendAudioByFileId($chatId, $fileId, $message="", $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'document' => $fileId,
        ];
        if(!empty($message)) {
            $params['caption'] = $message;
        }
        if(!empty($keyboard)) {
            $params['reply_markup'] = [
                'inline_keyboard' => $keyboard,
            ];
        }
        return $params;
    }

    /**
     * @property-description Подготовка к отправке voice с сервера Телеграм по его fileId
     * @param $chatId
     * @param $fileId
     * @param string $message
     * @param array $keyboard
     * @return mixed
     */
    public function prepareSendVoiceByFileId($chatId, $fileId, $message="", $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'document' => $fileId,
        ];
        if(!empty($message)) {
            $params['caption'] = $message;
        }
        if(!empty($keyboard)) {
            $params['reply_markup'] = [
                'inline_keyboard' => $keyboard,
            ];
        }
        return $params;
    }

    /**
     * @property-description Подготовка к отправке видео с сервера Телеграм по его fileId
     * @param $chatId
     * @param $fileId
     * @param string $message
     * @param array $keyboard
     * @return mixed
     */
    public function prepareSendVideoByFileId($chatId, $fileId, $message="", $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'document' => $fileId,
        ];
        if(!empty($message)) {
            $params['caption'] = $message;
        }
        if(!empty($keyboard)) {
            $params['reply_markup'] = [
                'inline_keyboard' => $keyboard,
            ];
        }
        return $params;
    }

    /**
     * @property-description Подготовка к отправке stiker с сервера Телеграм по его fileId
     * @param $chatId
     * @param $fileId
     * @return mixed
     * @throws \Exception
     */
    public function prepareSendStickerByFileId($chatId, $fileId, $keyboard = [])
    {
        $params = [
            'chat_id' => $chatId,
            'sticker' => $fileId,
        ];
        if(!empty($keyboard)) {
            $params['reply_markup'] = [
                'inline_keyboard' => $keyboard,
            ];
        }
        return $params;
    }

    /**
     * @property-description Отправка текстового сообщения
     * @param $chatId
     * @param $message
     * @param array $keyboard
     * @param bool $isInline
     * @return mixed
     * @throws \Exception
     */
    public function sendMessage($chatId, $message, $keyboard = [], $isInline = true)
    {
        if ($isInline) {
            return $this->sendMessageInlineKeyboard($chatId, $message, $keyboard);
        }
        return $this->sendMessageKeyboard($chatId, $message, $keyboard);
    }

    /**
     * @property-description Отправка текстового сообщения с клавиатурой InlineKeyboardMarkup
     * @param $chatId
     * @param $message
     * @param array $keyboard
     * @return mixed
     * @throws \Exception
     */
    public function sendMessageInlineKeyboard($chatId, $message, $keyboard = [])
    {
        $params = $this->prepareSendMessageInlineKeyboard($chatId, $message, $keyboard);
        return $this->_invokeMethod('sendMessage', $params);
    }

    /**
     * @property-description Отправка текстового сообщения
     * @param $chatId
     * @param $message
     * @param array $keyboard
     * @return mixed
     * @throws \Exception
     */
    public function sendMessageKeyboard($chatId, $message, $keyboard = [])
    {
        $params = $this->prepareSendMessageKeyboard($chatId, $message, $keyboard);
        return $this->_invokeMethod('sendMessage', $params);
    }
    /**
     * @property-description Асинхронная отправка текстового сообщения
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function asyncSendMessageKeyboard($params)
    {
        return $this->_invokeAsyncMethod('sendMessage', $params);
    }

    /**
     * @param $chatId
     * @param $message
     * @param array $keyboard
     * @return mixed
     * @throws \Exception
     */
    public function answerCallbackQuery($callbackQueryId, $text, $showAlert)
    {
        $params = $this->prepareAnswerCallbackQuery($callbackQueryId, $text, $showAlert);
        return $this->_invokeMethod('answerCallbackQuery', $params);
    }
}
