<?php
namespace App\Core;

class Env
{
    private static array $vars = array();

    /**
     * Load variables from a .env file path. Missing file is not an error.
     * Supports lines: KEY=VALUE, quoted values, and comments starting with #.
     * @param string $file
     * @return void
     */
    public static function load(string $file)
    {
        if (!is_file($file) || !is_readable($file)) {
            return;
        }
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $trim = ltrim($line);
            if ($trim === '' || $trim[0] === '#') { continue; }
            // Allow inline comments using # if not inside quotes
            $kv = self::splitKeyValue($line);
            if ($kv === null) { continue; }
            list($key, $value) = $kv;
            $key = trim($key);
            if ($key === '') { continue; }
            $value = self::sanitizeValue($value);
            self::$vars[$key] = $value;
            // Also expose to $_ENV and putenv for interoperability
            $_ENV[$key] = $value;
            if (function_exists('putenv')) { @putenv($key . '=' . $value); }
        }
    }

    /**
     * Get variable from env, falling back to loaded vars and $_ENV/$_SERVER.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, self::$vars)) { return self::$vars[$key]; }
        if (isset($_ENV[$key])) { return $_ENV[$key]; }
        if (isset($_SERVER[$key])) { return $_SERVER[$key]; }
        $val = getenv($key);
        return $val !== false ? $val : $default;
    }

    /**
     * Return all loaded variables as array.
     * @return array
     */
    public static function all(): array
    {
        return self::$vars;
    }

    private static function splitKeyValue($line): ?array
    {
        $len = strlen($line);
        $inSingle = false; $inDouble = false;
        for ($i = 0; $i < $len; $i++) {
            $ch = $line[$i];
            if ($ch === "'" && !$inDouble) { $inSingle = !$inSingle; }
            elseif ($ch === '"' && !$inSingle) { $inDouble = !$inDouble; }
            elseif ($ch === '=' && !$inSingle && !$inDouble) {
                $key = substr($line, 0, $i);
                $rest = substr($line, $i + 1);
                // Strip inline comment outside quotes
                $restLen = strlen($rest);
                $inS = false; $inD = false; $commentPos = -1;
                for ($j = 0; $j < $restLen; $j++) {
                    $ch2 = $rest[$j];
                    if ($ch2 === "'" && !$inD) { $inS = !$inS; }
                    elseif ($ch2 === '"' && !$inS) { $inD = !$inD; }
                    elseif ($ch2 === '#' && !$inS && !$inD) { $commentPos = $j; break; }
                }
                if ($commentPos >= 0) {
                    $value = substr($rest, 0, $commentPos);
                } else {
                    $value = $rest;
                }
                return array($key, $value);
            }
        }
        return null;
    }

    private static function sanitizeValue($value)
    {
        $value = trim($value);
        if ($value === '') { return ''; }
        $first = $value[0];
        $last = $value[strlen($value)-1];
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            $inner = substr($value, 1, -1);
            if ($first === '"') {
                // Unescape common sequences for double-quoted values
                $inner = str_replace(array('\\n','\\r','\\t','\\"','\\\\'), array("\n","\r","\t","\"","\\"), $inner);
            }
            $value = $inner;
        }
        // Cast common literals
        $lower = strtolower($value);
        if ($lower === 'true') { return true; }
        if ($lower === 'false') { return false; }
        if ($lower === 'null') { return null; }
        // Numeric detection
        if (is_numeric($value)) {
            // Keep as string for safety in config unless integer-like
            if (ctype_digit(strval($value))) { return (int)$value; }
            return (float)$value;
        }
        return $value;
    }
}
