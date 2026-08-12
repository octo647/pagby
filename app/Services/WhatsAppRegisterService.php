<?php

namespace App\Services;

class WhatsAppRegisterService
{
    public static function getRegisteredNumbers()
    {
        $path = base_path('storage/app/whatsapp_registered.json');
        if (!file_exists($path)) return [];
        $json = file_get_contents($path);
        return json_decode($json, true) ?? [];
    }

    public static function getWhatsappByIdentifier($identifier)
    {
        $all = self::getRegisteredNumbers();
        foreach ($all as $entry) {
            if (isset($entry['identificador']) && $entry['identificador'] == $identifier) {
                return $entry['phone'];
            }
        }
        return null;
    }
}
