<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgChannel extends Model
{
    use HasFactory;

    const STATUS = [
        'disabled' => 0,
        'enabled' => 1,
    ];

    public static function getStatusList() {
        return [
            self::STATUS['disabled'] => 'Неактивный',
            self::STATUS['enabled'] => 'Активный',
        ];
    }

    public static function getNextChannelId($id) {
        $nextChannel = self::where('id', '>', $id)->orderBy('id')->first();
        if (!is_null($nextChannel)) {
            return $nextChannel->id;
        }

        return false;
    }

    public static function getChannelLink($channel) {
        $link = $channel->alias;
        if (empty($link)) {
            $link = preg_replace('/(https:\/\/)?t\.me\/(.+)/i', '$2', $link->url);
        }
        if (substr($link, 0, 1) != '@') {
            $link = '@' . $link;
        }
        if (!empty($link)) {
            return $link;
        }

        return false;
    }

    public static function getChannelUrl($channel) {
        $url = $channel->url;
        if (empty($url)) {
            $url = 'https://t.me/' . ltrim($channel->alias, '@');
        }
        if (!empty($url)) {
            return $url;
        }

        return false;
    }

}
