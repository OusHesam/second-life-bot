<?php
namespace App;

interface Handler
{
    public function handle(array $update): bool;
}
