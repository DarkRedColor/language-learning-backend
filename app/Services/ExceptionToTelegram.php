<?php

namespace App\Services;

use Exception;

use Telegram\Bot\Api;
use URL;


class ExceptionToTelegram
{

    public static function sendMessage($exception)
    {

        if(!\Str::contains($exception->getMessage(),'Class cache does not exist') && !\Str::contains($exception->getMessage(),'You requested 1 items')) {
            $message = '<b>[' . now('Europe/Kiev')->toDateString() . "]</b>\n";
            $message .= "\xE2\x9D\x97" . '<b>Error:</b> ' . $exception->getMessage() . "\n";
            $message .= "\xF0\x9F\x93\x81" . '<b>File:</b> ' . $exception->getFile() . "\n";
            $message .= "\x23\xE2\x83\xA3" . '<b>Line:</b> ' . $exception->getLine() . "\n";
            $message .= "\xF0\x9F\x94\x97" . '<b>Url:</b> ' . URL::current() . "\n";
            if (\Auth::user() !== null)$message .= "\xF0\x9F\x99\x85" . '<b>User:</b> ' . \Auth::user()->email . "\n";
            $message .= "<pre>Отчет об ошибке.</pre>\n";

            try {

                $apiTelegram = new Api(config('values.telegram_bot_token'));
                $apiTelegram->sendMessage([
                    'chat_id' => '-1001598065235',//чат id 132030283 // групп чат id -433308994
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]);


            } catch (Exception $e) {
                // dump($e->getMessage());
            }
        }
    }
}
