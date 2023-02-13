<?php

declare(strict_types=1);

namespace App\Commands;

use Wherd\Foundation\System;
use RuntimeException;

class Autorouter
{
    public function generate(): void
    {
        if (!defined('ROOT')) {
            throw new RuntimeException('ROOT constant is not defined');
        }

        $routes = [];
        $files = $this->getFiles(ROOT . '/src/Controllers/');

        foreach ($files as $file) {
            $class_name = 'App\\Controllers\\' . str_replace(['/', '.php'], ['\\', ''], $file);
            $reflector = new \ReflectionClass($class_name); // @phpstan-ignore-line
            $methods = $reflector->getMethods();

            foreach ($methods as $method) {
                $attributes = $method->getAttributes();

                if (!empty($attributes)) {
                    foreach ($attributes as $attribute) {
                        if ('App\\Routing\\Route' === $attribute->getName()) {
                            /** @var \App\Routing\Route */
                            $attribute = reset($attributes)->newInstance();
                            $routes[] = [$attribute->method, $attribute->uri, $class_name . '@' . $method->name];
                            echo "{$attribute->method} {$attribute->uri} => {$class_name}@{$method->name}\n";
                        }
                    }
                }
            }
        }

        /** @var \Wherd\Config\IAdapter */
        $config_provider = System::getInstance()->providerOf('config');
        $config_provider->save(ROOT . '/var/config/routes.php', $routes);

        echo 'Generated ' . count($routes) . ' routes from controllers';
    }

    /** @return array<string> */
    protected function getFiles(string $path): array
    {
        $files = [];
        $filenames = scandir($path) ?: [];

        foreach ($filenames as $filename) {
            if ('.' === $filename || '..' === $filename) {
                continue;
            }

            if (is_dir($filename)) {
                $files += $this->getFiles($filename);
                continue;
            }

            if (str_ends_with($filename, '.php')) {
                $files[] = $filename;
            }
        }

        return $files;
    }
}
