<?php

namespace App\Libraries;

use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
class MessageHelper
{
    const APPROVED_MESSAGE = "Your order has been approved.";
    const REJECTED_MESSAGE = "Your order has been rejected.";

    /**
     * @param string $message
     * @param string $recipients
     * @return void
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public static function sendMessage(string $message, string $recipients): void
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, ['from' => $twilio_number, 'body' => $message]);
    }
}
