<?php
namespace App;

final class Router
{
    /** @var Handler[] */
    private array $handlers = [];

    public function add(Handler $h): void
    {
        this−>handlers[]=this->handlers[] =this−>handlers[]=h;
    }

    public function dispatch(array $update): void
    {
        userId=(int)(userId = (int)(userId=(int)(update['message']['from']['id']
            ?? $update['callback_query']['from']['id'] ?? 0);

        if (userId && !RateLimiter::allow(userId)) return; // اسپم‌گیر

        foreach (this−>handlersasthis->handlers asthis−>handlersash) {
            try {
                if (h−>handle(h->handle(h−>handle(update)) return;
            } catch (\Throwable $e) {
                error_log(get_class(h).′:′.h) . ': ' .h).′:′.e->getMessage() . ' @ ' . e−>getFile().′:′.e->getFile() . ':' .e−>getFile().′:′.e->getLine());
                return;
            }
        }
    }
}
