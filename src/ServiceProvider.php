<?php
namespace Alograg\DevTools;

use Alograg\DevTools\Console\Commands\ControllerMakeCommand;
use Alograg\DevTools\Console\Commands\FactoryMakeCommand;
use Alograg\DevTools\Console\Commands\FilterMakeCommand;
use Alograg\DevTools\Console\Commands\MigrateMakeCommand;
use Alograg\DevTools\Console\Commands\MigrationCreator;
use Alograg\DevTools\Console\Commands\ModelMakeCommand;
use Alograg\DevTools\Console\Commands\PolicyMakeCommand;
use Alograg\DevTools\Console\Commands\TestMakeCommand;
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
        'ControllerMake' => 'command.controller.make',
        'FactoryMake'    => 'command.factory.make',
        'FilterMake'     => 'command.filter.make',
        'MigrateMake'    => 'command.migrate.make',
        'ModelMake'      => 'command.model.make',
        'PolicyMake'     => 'command.policy.make',
        'TestMake'       => 'command.test.make',
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
        $this->registerCreator();
        $this->registerCommands($this->devCommands);
    }

    /**
     * Register the migration creator.
     *
     * @return void
     */
    protected function registerCreator()
    {
        $this->app->singleton('migration.creator', function ($app) {
            return new MigrationCreator($app['files']);
        });
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
            $commandClass = 'Alograg\\DevTools\\Console\\Commands\\' . $command . 'Command';
            $this->app->singleton($container, function ($app) use ($commandClass) {
                return new $commandClass($app['files'], $app);
            });
        }
        $this->commands(array_values($commands));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(array_values($this->devCommands), ['migration.creator']);
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateMakeCommand()
    {
        $this->app->singleton('command.migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];
            $composer = $app['composer'];

            return new MigrateMakeCommand($creator, $composer);
        });
    }

}
