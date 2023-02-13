<?php

declare(strict_types=1);

namespace App\Commands;

use RuntimeException;

class Facade
{
    public function generate(): void
    {
        if (!defined('ROOT')) {
            throw new RuntimeException('ROOT constant is not defined');
        }

        $config = new \Wherd\Config\PhpAdapter();
        $facades = $config->load(ROOT . '/var/config/facades.php');

        foreach ($facades as $name => $class_name) {
            $this->create($name, $class_name);
        }

        $count = count($facades);

        echo "Generated $count facades.\n";
    }

    public function create(string $provider, string $class_name): void
    {
        if (!defined('ROOT')) {
            throw new RuntimeException('ROOT constant is not defined');
        }

        $reflector = new \ReflectionClass($class_name); // @phpstan-ignore-line
        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);

        $signatures = '';

        foreach ($methods as $m) {
            if ('__construct' === $m->getName()) {
                continue;
            }

            $signature = ' * @method static';

            $return_type = $m->getReturnType();
            if ($return_type instanceof \ReflectionNamedType) {
                $signature .= ' ' . $return_type->getName();
            }

            $signature .= ' ' . $m->getName() . '(';

            $params = $m->getParameters();
            $method_params = [];

            foreach ($params as $p) {
                $param = '';
                
                $type = $p->getType();
                if ($type) {
                    $param .= $type . ' ';
                }

                $param .= '$' . $p->getName();
                $method_params[] = $param;
            }

            $signature .= join(', ', $method_params) . ")\n";
            $signatures .= $signature;
        }

        $output_class = ucfirst($provider);
        $output = <<<EOL
<?php

namespace App\Facades;

/**
$signatures *
 * @see $class_name
 */
class $output_class
{
    protected static ?$class_name \$instance;

    public static function getInstance(): $class_name
    {
        if (static::\$instance === null) {
            static::\$instance = \Wherd\Foundation\System::getInstance()->providerOf('$provider');
        }

        return static::\$instance;
    }

    /** @param array<mixed> \$arguments */
    public static function __callStatic(string \$name, array \$arguments): mixed
    {
        \$callback = [static::getInstance(), \$name];

        if (is_callable(\$callback)) {
            return call_user_func_array(\$callback, \$arguments);
        }

        throw new \Exception("Method \$name not found on class $class_name");
    }
}

EOL;
        if (!is_dir(ROOT . '/src/Facades/')) {
            mkdir(ROOT . '/src/Facades/', 0777, true);
        }

        file_put_contents(ROOT . '/src/Facades/' . $output_class . '.php', $output);
        echo "$output_class facade created.\n";
    }
}
