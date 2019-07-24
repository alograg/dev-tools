<?php
namespace Alograg\DevTools\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ControllerMakeCommand
 *
 * @package Alograg\DevTools\Console
 */
class ControllerMakeCommand extends GeneratorCommand
{
    /**
     *
     */
    const STUBS_CONTROLLER_STUB = '/../stubs/controller.stub';

    /** {@inheritdoc} */
    protected $name = 'make:controller';

    /** {@inheritdoc} */
    protected $description = 'Create a new REST controller class';

    /** {@inheritdoc} */
    protected $type = 'Controller';

    /**
     * @var null
     */
    protected $routerPath = null;

    /** {@inheritdoc} */
    public function handle()
    {
        if (parent::handle() !== false) {
        }
        $addRouts = $this->option('router');
        if ($addRouts != 'none') {
            $this->routerPath = $addRouts;
            if (is_null($this->routerPath)) {
                $this->routerPath = 'routes/api.php';
            }
            $this->appendRouteFile();
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $template = $this->option('template');
        $vendorsPath = realpath(__DIR__.self::STUBS_CONTROLLER_STUB);

        return $template != $vendorsPath ? base_path($template) : $vendorsPath;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Append Route Files
     *
     * @return void
     */
    protected function appendRouteFile()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $nameWithoutNamespace = str_replace($this->getDefaultNamespace(trim($this->rootNamespace(),
                '\\')).'\\', '', $name);
        $nameWithoutNamespace = Str::replaceFirst('Http\\Controllers\\', '', $nameWithoutNamespace);
        $file = base_path($this->routerPath);
        $as = $this->getRouteName($name);
        $routePath = $this->getRoutePath($name);
        $routeDefinition = [
            PHP_EOL."// {$name}",
            "\$router->get('{$routePath}', [ 'as' => '$as.index', 'uses' => '$nameWithoutNamespace@all' ]);",
            "\$router->post('{$routePath}/{id}', [ 'as' => '$as.store', 'uses' => '$nameWithoutNamespace@add' ]);",
            "\$router->get('{$routePath}/{id}', [ 'as' => '$as.show', 'uses' => '$nameWithoutNamespace@get' ]);",
            "\$router->put('{$routePath}/{id}', [ 'as' => '$as.update', 'uses' => '$nameWithoutNamespace@put' ]);",
            "\$router->patch('{$routePath}/{id}', [ 'as' => '$as.update', 'uses' => '$nameWithoutNamespace@put' ]);",
            "\$router->delete('{$routePath}/{id}', [ 'as' => '$as.destroy', 'uses' => '$nameWithoutNamespace@remove' ]);"
        ];
        file_put_contents($file, implode(PHP_EOL, $routeDefinition).PHP_EOL,
            FILE_APPEND);
        $this->warn($file.' modified.');
    }

    /**
     * Get the route name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function getRouteName($name)
    {
        $name = Str::replaceFirst('Http\\Controllers\\', '', $name);
        $name = Str::replaceFirst($this->getDefaultNamespace(trim($this->rootNamespace(),
                '\\')).'\\', '', $name);
        $name = Str::replaceLast('Controller', '', $name);
        $names = explode('\\', $name);
        foreach ($names as $key => $value) {
            $names[$key] = snake_case($value);
        }
        $name = implode('.', $names);

        return str_replace('\\', '.', $name);
    }

    /**
     * Get the route path.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function getRoutePath($name)
    {
        $routeName = $this->getRouteName($name);
        $routeNameExploded = explode('.', $routeName);
        $routePath = str_replace('.', '/', $this->getRouteName($routeName));

        return $routePath;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $defaultPath = str_replace(base_path(), '',
            realpath(__DIR__.self::STUBS_CONTROLLER_STUB));

        return [
            [
                'template',
                't',
                InputOption::VALUE_OPTIONAL,
                'Relative path to stub file',
                $defaultPath,
            ],
            [
                'router',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Router file to add routes. If you dont set the path it take: routes/api.php',
                'none'
            ],
        ];
    }

}
