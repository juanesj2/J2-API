<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    protected $messaging;
    public $lastError = null;

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
            $this->lastError = 'Init Exception: ' . $e->getMessage();
            \Log::error('FCM Initialization Error: ' . $e->getMessage());
            $this->messaging = null;
        }
    }

    public function sendToToken($token, $title, $body, $data = [])
    {
        if (!$token) {
            $this->lastError = 'Token is missing';
            return false;
        }
        if (!$this->messaging) {
            $this->lastError = $this->lastError ?: 'Messaging is null (Credentials file missing or invalid)';
            return false;
        }

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
            $this->lastError = $e->getMessage();
            \Log::error('FCM Send Error: ' . $e->getMessage());
            return false;
        }
    }
}
