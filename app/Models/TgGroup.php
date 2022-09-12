<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgGroup extends Model
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

    public static function getNextGroupId($id) {
        $nextGroup = self::where('id', '>', $id)->orderBy('id')->first();
        if (!is_null($nextGroup)) {
            return $nextGroup->id;
        }

        return false;
    }

    public static function getGroupLink($group) {
        $link = preg_replace('/(https:\/\/)?t\.me\/(.+)/i', '$2', $group->url);
        if (substr($link, 0, 1) != '@') {
            $link = '@' . $link;
        }
        if (!empty($link)) {
            return $link;
        }

        return false;
    }

    public static function getGroupUrl($group) {
        $url = $group->url;

        if (!empty($url)) {
            return $url;
        }

        return false;
    }
}
