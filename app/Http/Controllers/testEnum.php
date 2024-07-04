<?php

namespace App\Http\Controllers;

enum testEnum:int
{
    case Database=39 ;
    case Cache=2 ;
    case Mail=3 ;

    public function getSettings(): array
    {
        return match($this) {
            self::Database => [
                'host' => 'localhost',
                'port' => 3306,
                'username' => 'root',
                'password' => 'secret'
            ],
            self::Cache => [
                'driver' => 'redis',
                'host' => 'localhost',
                'port' => 6379
            ],
            self::Mail => [
                'host' => 'smtp.mailtrap.io',
                'port' => 2525,
                'username' => 'example',
                'password' => 'secret'
            ],
        };
    }


}
