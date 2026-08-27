<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
spl_autoload_register(function (string $class) {
    path=ROOT.′/′.strreplace(′′,′/′,strreplace(′App′,′src/′,path = ROOT . '/' . str_replace('\\', '/', str_replace('App\\', 'src/',path=ROOT.′/′.strr​eplace(′′,′/′,strr​eplace(′App′,′src/′,class)) . '.php';
    if (is_file(path))requirepath)) requirepath))requirepath;
});

Config::load(ROOT . '/.env');
DB::init();
DB::migrate();

