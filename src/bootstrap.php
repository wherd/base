<?php

declare(strict_types=1);

use Wherd\Foundation\System;
use Wherd\Http\Kernel;

defined('ROOT') or die('ROOT constant not defined');
defined('WRITABLE') or die('WRITABLE constant not defined');

/*
|-----------------------------------------------------
| Register The Auto Loader
|-----------------------------------------------------
|
| Simply register the autloader function so that we
| don't have to worry about manual loading any of our
| classes later on. It feels great to relax.
|
*/

include ROOT . '/vendor/autoload.php';

/*
|-----------------------------------------------------
| Bind Important Interfaces
|-----------------------------------------------------
|
| Next, we need to bind some important interfaces into
| the provider so we will be able to resolve them
| when needed.
|
*/

const CONFIG_DIR = ROOT . '/var/config';

$app = new System();

$app->provideMultiple([
    'cache' => fn () => new \Wherd\Cache\FileStorage(WRITABLE . '/cache'),
    'config' => new \Wherd\Config\PhpAdapter(),
    'kernel' => new Kernel(),
    'router' => new \Wherd\Http\Router(),
    'db' => function () {
        $config = System::getInstance()->providerOf('config')->load(CONFIG_DIR . '/system.php');
        $db = new \Wherd\Database\Connection(...$config['database']);

        return $db;
    },
    'view' => function () {
        /** @var \Wherd\Config\IAdapter */
        $config_provider = System::getInstance()->providerOf('config');
        $config = $config_provider->load(CONFIG_DIR . '/system.php');

        $engine = new \Wherd\Signal\Compiler(ROOT . '/var/themes');
        $engine->debugMode = ('production' !== $config['mode']);
        $engine->setCacheDirectory(WRITABLE . '/themes');

        $directives = $config_provider->load(CONFIG_DIR . '/directives.php');
        foreach ($directives as $name => $callback) {
            $engine->registerDirective($name, $callback);
        }

        return new \Wherd\Signal\View($engine);
    }
]);

/*
|-----------------------------------------------------
| Load configuration file
|-----------------------------------------------------
|
| Load and parse the main configuration file.
| PHP files are used as its supported configuration file
| type. The site configuration file is system.php the
| rest is loaded as needed and managed by packages.
|
*/

$config_provider = $app->providerOf('config');

$config = $config_provider->load(CONFIG_DIR . '/system.php');

date_default_timezone_set($config['timezone'] ?? 'Europe/Lisbon');

if (! defined('SID') && ($config['session_name'] ?? false)) {
    session_name($config['session_name'] ?? 'wherd');
    session_cache_expire($config['session_expires'] ?? 30);
    session_save_path(WRITABLE . '/sessions');
    session_start();
}

/*
|-----------------------------------------------------
| Bind Custom Interfaces
|-----------------------------------------------------
|
| Next, we need to bind user interfaces into the
| provider so we will be able to resolve them when
| needed.
|
*/

$app->provideMultiple($config_provider->load(CONFIG_DIR . '/providers.php'));

/*
|-----------------------------------------------------
| Load and setup routes
|-----------------------------------------------------
|
| Routes handle incoming requests. The default
| routes are loaded from the configuration file
| but packages can also register and handle routes.
|
*/

/** @var \Wherd\Http\Router */
$route_provider = $app->providerOf('router');
$route_provider->addRoutes($config_provider->load(CONFIG_DIR . '/routes.php'));

/** @var \Wherd\Http\Kernel */
$kernel_provider = $app->providerOf('kernel');
$kernel_provider->register($route_provider);

$middleware = $config_provider->load(CONFIG_DIR . '/middleware.php');
foreach ($middleware as $item) {
    $kernel_provider->register($item);
}

unset($config, $middleware, $config_provider, $kernel_provider, $route_provider);

return $app;
