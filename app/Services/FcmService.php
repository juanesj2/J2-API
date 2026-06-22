<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = base_path('firebase-credentials.json');
            if (!file_exists($credentialsPath)) {
                $credentialsPath = storage_path('app/firebase-credentials.json');
            }
            if (!file_exists($credentialsPath)) {
                $credentialsPath = storage_path('app/firebase_credentials.json');
            }

            if (file_exists($credentialsPath)) {
                $factory = (new Factory)->withServiceAccount($credentialsPath);
                $this->messaging = $factory->createMessaging();
            } else {
                \Log::warning('FCM Credentials file not found at any known path.');
                $this->messaging = null;
            }
        } catch (\Throwable $e) {
            \Log::error('FCM Initialization Error: ' . $e->getMessage());
            $this->messaging = null;
        }
    }

    public function sendToToken($token, $title, $body, $data = [])
    {
        if (!$token || !$this->messaging) return false;

        try {
            $message = CloudMessage::fromArray([
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => empty($data) ? null : $data,
            ]);

            $this->messaging->send($message);
            return true;
        } catch (\Throwable $e) {
            \Log::error('FCM Send Error: ' . $e->getMessage());
            return false;
        }
    }
}
