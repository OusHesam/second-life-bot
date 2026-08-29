<?php
namespace App;

final class Router
{
    /** @var Handler[] */
    private array $handlers = [];

    public function add(Handler $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function dispatch(array $update): void
    {
        $userId = (int)(
            $update['message']['from']['id']
            ?? $update['callback_query']['from']['id']
            ?? 0
        );

        if ($userId && !RateLimiter::allow($userId)) {
            return; // اسپم‌گیر
        }

        foreach ($this->handlers as $handler) {
            try {
                if ($handler->handle($update)) {
                    return;
                }
            } catch (\Throwable $e) {
                error_log(
                    get_class($handler) . ': '
                    . $e->getMessage() . ' @ '
                    . $e->getFile() . ':'
                    . $e->getLine()
                );
                return;
            }
        }
    }
}
