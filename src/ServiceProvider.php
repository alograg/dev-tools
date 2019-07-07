<?php
namespace Alograg\DevTools;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 *
 * @see ControllerMakeCommand, FactoryMakeCommand, FilterMakeCommand, MigrateMakeCommand, MigrationCreator, ModelMakeCommand, PolicyMakeCommand, TestMakeCommand
 * @package Alograg\DevTools
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $devCommands = [
        'KeyGenerate' => 'command.key.generate',
        'ModelMake'   => 'command.make.model',
    ];

    /**
     * Register any application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $uri = env('APP_URL', 'http://localhost');
            $components = parse_url($uri);
            $server = $_SERVER;
            if (isset($components['path'])) {
                $server = array_merge($server, [
                    'SCRIPT_FILENAME' => $components['path'],
                    'SCRIPT_NAME'     => $components['path'],
                ]);
            }
            $this->app->instance('request', $this->app->make('request')->create(
                $uri, 'GET', [], [], [], $server
            ));
        }
        $this->registerCommands($this->devCommands);
    }

    /**
     * Register the given commands.
     *
     * @param array $commands
     *
     * @return void
     */
    protected function registerCommands(array $commands)
    {
        foreach ($commands as $command => $container) {
            if (method_exists($this, "register{$command}Command")) {
                call_user_func_array([$this, "register{$command}Command"], []);
                continue;
            }
            $className = 'Alograg\\DevTools\\Console\\' . $command . 'Command';
            if (class_exists($className)) {
                $this->app->singleton($container, function ($app) use ($className) {
                    return new $className($app['files']);
                });
            }
        }
        $this->commands(array_values($commands));
    }

}
