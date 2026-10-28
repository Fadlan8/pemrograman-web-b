<?php

class Logger
{
    private static string $logDir = ROOT_PATH . '/logs/errors';

    public static function error(string $message, array $context = []): void
    {
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0777, true);
        }
        file_put_contents(
            self::$logDir . '/error-' . date('YmdHis') . '.json',
            json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'message' => $message,
                'context' => $context,
            ], JSON_PRETTY_PRINT)
        );
    }
}
