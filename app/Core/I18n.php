<?php
namespace App\Core;

class I18n
{
    /** @var string */
    private static string $locale = 'en';

    /** @var array<string,array<string,string>> */
    private static array $messages = array();

    /** Load messages for a given locale from Langs/<locale>.json, with fallback to en */
    private static function loadMessages(string $locale): void
    {
        if (isset(self::$messages[$locale])) { return; }
        $root = dirname(__DIR__, 2);
        $file = $root . DIRECTORY_SEPARATOR . 'Langs' . DIRECTORY_SEPARATOR . $locale . '.json';
        $data = array();
        if (is_file($file)) {
            $json = @file_get_contents($file);
            if ($json !== false) {
                $decoded = @json_decode($json, true);
                if (is_array($decoded)) { $data = $decoded; }
            }
        }
        // If target missing and not en, try load en as base
        if (empty($data) && $locale !== 'en') {
            self::loadMessages('en');
            self::$messages[$locale] = self::$messages['en'] ?? array();
            return;
        }
        self::$messages[$locale] = $data;
    }

    public static function setLocale(string $locale): void
    {
        $locale = strtolower($locale);
        if ($locale !== 'en' && $locale !== 'th') {
            // allow any file present; if no file, fallback later
            $maybeFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Langs' . DIRECTORY_SEPARATOR . $locale . '.json';
            if (!is_file($maybeFile)) { $locale = 'en'; }
        }
        self::loadMessages($locale);
        // Always ensure english fallback is loaded
        self::loadMessages('en');
        self::$locale = $locale;
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }

    public static function t(string $key, array $replacements = array()): string
    {
        // Ensure messages are loaded in case called before setLocale
        if (!isset(self::$messages[self::$locale])) {
            self::setLocale(self::$locale);
        }
        $msg = self::$messages[self::$locale][$key] ?? self::$messages['en'][$key] ?? $key;
        foreach ($replacements as $k => $v) {
            $msg = str_replace('{' . $k . '}', (string)$v, $msg);
        }
        return $msg;
    }
}
