<?php
namespace App\Game;

final class Catalog
{
    public const PROPERTIES = [
        'hut'        => ['emoji' => '🏚', 'name' => 'خانه کوچک',   'price' => 3000,    'happiness' => 5,  'fame' => 2,   'min_level' => 1],
        'apartment'  => ['emoji' => '🏠', 'name' => 'آپارتمان',    'price' => 25000,   'happiness' => 10, 'fame' => 6,   'min_level' => 3],
        'villa'      => ['emoji' => '🏡', 'name' => 'خانه ویلایی',  'price' => 120000,  'happiness' => 18, 'fame' => 20,  'min_level' => 6],
        'penthouse'  => ['emoji' => '🏢', 'name' => 'پنت‌هاوس',     'price' => 500000,  'happiness' => 28, 'fame' => 60,  'min_level' => 10],
        'mansion'    => ['emoji' => '🏰', 'name' => 'عمارت',        'price' => 2000000, 'happiness' => 40, 'fame' => 150, 'min_level' => 15],
    ];

    public const VEHICLES = [
        'oldi'     => ['emoji' => '🚗', 'name' => 'دنا پلاس',            'cat' => 'اقتصادی',  'price' => 8000,    'prestige' => 2,   'min_level' => 1],
        'pride_s'  => ['emoji' => '🚗', 'name' => 'کوییک اتوماتیک',      'cat' => 'اقتصادی',  'price' => 12000,   'prestige' => 3,   'min_level' => 2],
        'camry'    => ['emoji' => '🚘', 'name' => 'Toyota Camry',        'cat' => 'سدان',     'price' => 45000,   'prestige' => 10,  'min_level' => 4],
        'benz_e'   => ['emoji' => '🚘', 'name' => 'Mercedes-Benz E200',  'cat' => 'سدان',     'price' => 95000,   'prestige' => 25,  'min_level' => 6],
        'mustang'  => ['emoji' => '🏎', 'name' => 'Ford Mustang GT',     'cat' => 'اسپرت',    'price' => 180000,  'prestige' => 45,  'min_level' => 8],
        'porsche'  => ['emoji' => '🏎', 'name' => 'Porsche 911 Carrera', 'cat' => 'اسپرت',    'price' => 400000,  'prestige' => 80,  'min_level' => 11],
        'rolls'    => ['emoji' => '✨', 'name' => 'Rolls-Royce Ghost',   'cat' => 'لوکس',     'price' => 900000,  'prestige' => 140, 'min_level' => 14],
        'bugatti'  => ['emoji' => '🔥', 'name' => 'Bugatti Chiron',      'cat' => 'سوپراسپرت','price' => 3500000, 'prestige' => 300, 'min_level' => 18],
    ];

    public const ITEMS = [
        'coffee'     => ['emoji' => '☕', 'name' => 'قهوه انرژی‌زا',  'price' => 50,    'effect' => ['energy' => 25],       'min_level' => 1],
        'medkit'     => ['emoji' => '💉', 'name' => 'کیت درمانی',     'price' => 200,   'effect' => ['health' => 40],       'min_level' => 1],
        'suit'       => ['emoji' => '🥼', 'name' => 'کاستوم شیک',     'price' => 5000,  'effect' => ['fame' => 15, 'influence' => 5], 'min_level' => 4],
        'watch'      => ['emoji' => '⌚', 'name' => 'ساعت لوکس',      'price' => 30000, 'effect' => ['fame' => 40, 'influence' => 15], 'min_level' => 7],
        'book_biz'   => ['emoji' => '📚', 'name' => 'کتاب تجارت',     'price' => 800,   'effect' => ['influence' => 20],    'min_level' => 2],
        'gym_pass'   => ['emoji' => '🏋', 'name' => 'پلن باشگاه',     'price' => 1200,  'effect' => ['power' => 15, 'health' => 10], 'min_level' => 2],
    ];
}
