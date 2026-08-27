<?php
namespace App;

interface Handler
{
    public function handle(array $update): bool;
}

final class Router
{
    /** @var Handler[] */
    private array $handlers = [];

    public function add(Handler h): void {this->handlers[] = $h; }

    public function dispatch(array $update): void
    {
        foreach (this−>handlersasthis->handlers asthis−>handlersash) {
            if (h−>handle(h->handle(h−>handle(update)) return;   // first handler that consumes the update wins
        }
    }
}

