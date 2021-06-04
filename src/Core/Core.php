<?php

namespace App\Core;

class Core
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @var Telegram
     */
    private $telegram;

    private $user;
    private $data;

    public function __construct(Database $database, Telegram $telegram, $user, $date = null)
    {
        $this->database = $database;
        $this->telegram = $telegram;
        $this->user = $user;
        $this->date = $date;
    }

    public function initProcess()
    {
        if (!preg_match('/([0-9]{2})\/([0-9]{2})/', $this->date, $matches)) {
            echo 'INCORRECT DATE';
            return;
        }
        [, $day, $month] = $matches;

        // $sql = 'SELECT * FROM birthday_' . $this->user . ' WHERE day=' . $day . ' && month=' . $month . ';';

        $results = $this->database->getBirthdays($day, $month, $this->user);

        foreach ($results as $result) {
            $this->database->updateBirthdayData($result['id'], $this->user);
        }

        if (empty($results)) {
            return [];
        }

        $message = $this->telegram->parseMessage($results, $this->date);

        [$botId, $chatId, $defaultToken] = $this->getTelegramUserData();
        $telegramUrl = $this->telegram->mountUrl($botId, $defaultToken, $chatId, $message);

        $this->sendRequest($telegramUrl);
    }

    public function getTelegramUserData()
    {
        $sql = 'SELECT botID, chatID, defaultToken FROM users WHERE username = "' . $this->user . '"';
        $result = $this->database->getData($sql);

        if (empty($result)) {
            echo 'USER DOES NOT HAVE TELEGRAM DATA';
            die();
        }

        $params = [];

        foreach($result[0] as $param) {
            $params[] = $param;
        }

        return $params;
    }

    public function sendRequest($url)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST , 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = json_decode(curl_exec($ch));
        if (!$response->ok) {
            echo 'REQUEST FAILED';
            die();
        }

        print_r($response);
        echo 'OK';
    }
}