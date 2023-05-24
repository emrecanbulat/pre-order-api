<?php

namespace App\Libraries;

use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
class MessageHelper
{

    /**
     * @param $message
     * @param $recipients
     * @return void
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public static function sendMessage($message, $recipients): void
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, ['from' => $twilio_number, 'body' => $message]);
    }
}
