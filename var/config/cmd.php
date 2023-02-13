<?php return [
    'route:generate' => '\\App\\Commands\\Autorouter@generate',
    'db:migrate' => '\\App\\Commands\\Migrations@run',
    'db:generate' => '\\App\\Commands\\Migrations@create',
    'db:rollback' => '\\App\\Commands\\Migrations@rollback',
    'facade:create' => '\\App\\Commands\\Facade@create',
    'facade:generate' => '\\App\\Commands\\Facade@generate',
];
