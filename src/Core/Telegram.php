<?php

namespace App\Core;

class Telegram
{
    //https://api.telegram.org/bot706337453:AAG8fwCYOz9leKxzvKlu9vcQebKZsOWt2C4/sendMessage?chat_id=-1001284975436&text=EstÃ¡ funcionando!

    private $url = 'https://api.telegram.org/bot{botId}:{defaultToken}sendMessage?chat_id={chatId}&parse_mode=html&text={encodedMessage}';

    public function mountUrl($botId, $defaultToken, $chatId, $encodedMessage)
    {
        $this->url = preg_replace('/\{botId\}/is', $botId, $this->url);
        $this->url = preg_replace('/\{defaultToken\}/is', $defaultToken, $this->url);
        $this->url = preg_replace('/\{chatId\}/is', $chatId, $this->url);
        $this->url = preg_replace('/\{encodedMessage\}/is', $encodedMessage, $this->url);

        return $this->url;
    }

    public function parseMessage($results, $date): string
    {
        $message = "ANIVERSARIANTES DO DIA: " . $date;
        $message .= "\n ------------------------------------\n";
        foreach ($results as $result) {
            $message .= utf8_decode($result['firstname']) . ' ' . utf8_decode($result['lastname']) . ' ' . intval($result['years'] + 1) . " anos\n";
        }

        return urlencode($message);
    }
}