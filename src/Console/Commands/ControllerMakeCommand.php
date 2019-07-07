<?php
namespace Alograg\DevTools\Console\Commands;

use Alograg\DevTools\Abstracts\Maker;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

/**
 * Controller Make Command
 */
class ControllerMakeCommand extends Maker
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = <<<TXT
Create a new controller class
TXT;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';
    /**
     * @var array
     */
    protected $options = [
        'parent',
        'model',
        'resource',
    ];

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['rout', null, InputOption::VALUE_OPTIONAL, 'Add to specific roouter. Default: web'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Generate a resource controller class.'],
            ['invokable', 'i', InputOption::VALUE_NONE, 'Generate a single method, invokable controller class.'],
            ['parent', 'p', InputOption::VALUE_OPTIONAL, 'Generate a nested resource controller class.'],
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (parent::handle() === false) {
            return false;
        }
        $this->createTest();
        $this->generateTranslation();
        $this->appendRouteFile();

        return null;
    }

    /**
     * Append Route Files
     *
     * @return void
     */
    protected function appendRouteFile()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $nameWithoutNamespace = str_replace($this->getDefaultNamespace(trim($this->rootNamespace(), '\\')) . '\\', '',
            $name);
        $file = base_path('routes/' . ($this->option('rout') ?: 'web') . '.php');
        $routeName = $this->getRouteName($name);
        $routePath = $this->getRoutePath($name);
        $routeDefinition = "\$router->get('$routePath', [ 'as' => '$routeName', 'uses' => '$nameWithoutNamespace' ]);" . PHP_EOL;
        if ($this->option('parent') || $this->option('model') || $this->option('resource')) {
            $routePathExploded = explode('/', $routePath);
            $as = str_replace('/', '.', $routePath);
            if (count($routePathExploded) > 1) {
                array_pop($routePathExploded);
                $prefix = implode('/', $routePathExploded) . '/';
            } else {
                $prefix = '';
            }
            $asExploded = explode('.', $as);
            $models = array_pop($asExploded);
            $model = Str::singular($models);
            if ($this->option('parent')) {
                $parents = array_pop($asExploded);
                $parent = Str::singular($parents);
                $routeDefinition =
                    PHP_EOL .
                    "\$router->get('{$prefix}{$parents}/{{$parent}}/$models', [ 'as' => '$as.index', 'uses' => '$nameWithoutNamespace@index' ]);" . PHP_EOL .
                    "\$router->post('{$prefix}{$parents}/{{$parent}}/$models', [ 'as' => '$as.store', 'uses' => '$nameWithoutNamespace@store' ]);" . PHP_EOL .
                    "\$router->get('{$prefix}{$parents}/{{$parent}}/$models/{{$model}}', [ 'as' => '$as.show', 'uses' => '$nameWithoutNamespace@show' ]);" . PHP_EOL .
                    "\$router->put('{$prefix}{$parents}/{{$parent}}/$models/{{$model}}', [ 'as' => '$as.update', 'uses' => '$nameWithoutNamespace@update' ]);" . PHP_EOL .
                    "\$router->patch('{$prefix}{$parents}/{{$parent}}/$models/{{$model}}', [ 'as' => '$as.update', 'uses' => '$nameWithoutNamespace@update' ]);" . PHP_EOL .
                    "\$router->delete('{$prefix}{$parents}/{{$parent}}/$models/{{$model}}', [ 'as' => '$as.destroy', 'uses' => '$nameWithoutNamespace@destroy' ]);" . PHP_EOL;
            } else {
                $routeDefinition =
                    PHP_EOL .
                    "\$router->get('{$prefix}{$models}', [ 'as' => '$as.index', 'uses' => '$nameWithoutNamespace@index' ]);" . PHP_EOL .
                    "\$router->post('{$prefix}{$models}', [ 'as' => '$as.store', 'uses' => '$nameWithoutNamespace@store' ]);" . PHP_EOL .
                    "\$router->get('{$prefix}{$models}/{{$model}}', [ 'as' => '$as.show', 'uses' => '$nameWithoutNamespace@show' ]);" . PHP_EOL .
                    "\$router->put('{$prefix}{$models}/{{$model}}', [ 'as' => '$as.update', 'uses' => '$nameWithoutNamespace@update' ]);" . PHP_EOL .
                    "\$router->patch('{$prefix}{$models}/{{$model}}', [ 'as' => '$as.update', 'uses' => '$nameWithoutNamespace@update' ]);" . PHP_EOL .
                    "\$router->delete('{$prefix}{$models}/{{$model}}', [ 'as' => '$as.destroy', 'uses' => '$nameWithoutNamespace@destroy' ]);" . PHP_EOL;
            }
        }
        file_put_contents($file, $routeDefinition, FILE_APPEND);
        $this->warn($file . ' modified.');
    }

    /**
     * Create a test for the controller.
     *
     * @return void
     */
    protected function createTest()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $controllerClass = Str::replaceFirst($this->getDefaultNamespace(trim($this->rootNamespace(), '\\')) . '\\', '',
            $name);
        $this->call('make:test', [
            'name'        => $controllerClass . 'Test',
            '--parent'    => $this->option('parent') ?: null,
            '--model'     => $this->option('model') ?: null,
            '--resource'  => $this->option('resource') ?: false,
            '--stub-path' => $this->option('stub-path') ?: null,
        ]);
    }

    /**
     * Generate Translation File
     */
    protected function generateTranslation()
    {
        $this->call('make:translation', [
            'name'        => $this->getNameInput(),
            '--stub-path' => $this->option('stub-path') ?: null,
        ]);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param string $name
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);
        $replace = [];
        if ($this->option('parent')) {
            $replace['DummyFullParentClass'] = $controllerNamespace . 'Controller';
            $replace['DummyParentClass'] = class_basename($controllerNamespace . 'Controller');
            $replace['parent_dummy_route'] = $this->getParentRouteName($name);
            $replace = $this->buildParentReplacements();
        }
        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }
        $replace['dummy_route'] = $this->getRouteName($name);
        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
    }

    /**
     * Build the replacements for a parent controller.
     *
     * @return array
     */
    protected function buildParentReplacements()
    {
        $parentModelClass = $this->parseModel($this->option('parent'));
        if (!$this->files->exists($this->getPath($parentModelClass))) {
            if ($this->confirm("A {$parentModelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model',
                    ['name' => str_replace($this->rootNamespace(), '', $parentModelClass), '-m' => true, '-f' => true]);
            }
        }
        $policyClass = Str::replaceFirst($this->rootNamespace(), $this->rootNamespace() . 'Policies\\',
                $parentModelClass) . 'Policy';
        if (!$this->files->exists($this->getPath($policyClass))) {
            if ($this->confirm("A {$policyClass} policy does not exist. Do you want to generate it?", true)) {
                $this->call('make:policy', ['name' => $policyClass, '--model' => class_basename($parentModelClass)]);
            }
        }

        return [
            'ParentDummyFullModelClass'          => $parentModelClass,
            'ParentDummyModelClass'              => class_basename($parentModelClass),
            'ParentDummyModelVariable'           => lcfirst(class_basename($parentModelClass)),
            'parent_dummy_model_variable'        => Str::snake(class_basename($parentModelClass)),
            'parent_dummy_model_plural_variable' => Str::plural(Str::snake(class_basename($parentModelClass))),
            'ParentDummyTitle'                   => ucwords(Str::snake(class_basename($parentModelClass), ' ')),
        ];
    }

    /**
     * Build the model replacement values.
     *
     * @param array $replace
     *
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));
        if (!$this->files->exists($this->getPath($modelClass))) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model',
                    ['name' => str_replace($this->rootNamespace(), '', $modelClass), '-m' => true, '-f' => true]);
            }
        }
        $policyClass = Str::replaceFirst($this->rootNamespace(), $this->rootNamespace() . 'Policies\\',
                $modelClass) . 'Policy';
        if (!$this->files->exists($this->getPath($policyClass))) {
            if ($this->confirm("A {$policyClass} policy does not exist. Do you want to generate it?", true)) {
                $this->call('make:policy', ['name' => $policyClass, '--model' => class_basename($modelClass)]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass'         => $modelClass,
            'DummyModelClass'             => class_basename($modelClass),
            'DummyModelVariable'          => lcfirst(class_basename($modelClass)),
            'dummyModelVariable'          => Str::camel(class_basename($modelClass)),
            'dummy_model_variable'        => Str::snake(class_basename($modelClass)),
            'dummy_model_plural_variable' => Str::plural(Str::snake(class_basename($modelClass))),
            'DummyTitle'                  => ucwords(Str::snake(class_basename($modelClass), ' ')),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }
        $model = trim(str_replace('/', '\\', $model), '\\');
        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . $model;
        }

        return $model;
    }

    /**
     * Get the route name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getRouteName($name)
    {
        $name = Str::replaceFirst($this->getDefaultNamespace(trim($this->rootNamespace(), '\\')) . '\\', '', $name);
        $name = Str::replaceLast('Controller', '', $name);
        $names = explode('\\', $name);
        foreach ($names as $key => $value) {
            $names[$key] = Str::snake($value);
        }
        if ($this->option('parent') && count($names) >= 2) {
            $model = Str::plural(array_pop($names));
            $parent = Str::plural(array_pop($names));
            array_push($names, $parent, $model);
        } elseif (($this->option('model') || $this->option('resource')) && count($names) >= 1) {
            $model = Str::plural(array_pop($names));
            array_push($names, $model);
        }
        $name = implode('.', $names);

        return str_replace('\\', '.', $name);
    }

    /**
     * Get the route name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getParentRouteName($name)
    {
        $name = Str::replaceFirst($this->getDefaultNamespace(trim($this->rootNamespace(), '\\')) . '\\', '', $name);
        $name = Str::replaceLast('Controller', '', $name);
        $names = explode('\\', $name);
        foreach ($names as $key => $value) {
            $names[$key] = Str::snake($value);
        }
        if (count($names) >= 2) {
            array_pop($names);
            $parent = Str::plural(array_pop($names));
            array_push($names, $parent);
        }
        $name = implode('.', $names);

        return str_replace('\\', '.', $name);
    }

    /**
     * Get the route path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getRoutePath($name)
    {
        $routeName = $this->getRouteName($name);
        $routeNameExploded = explode('.', $routeName);
        $routePath = str_replace('.', '/', $this->getRouteName($routeName));
        if ($this->option('parent') && count($routeNameExploded) >= 2) {
            $routePath = Str::replaceLast('/', '.', $routePath);
        }

        return $routePath;
    }

}
