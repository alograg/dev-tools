<?php
namespace Alograg\DevTools\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ModelMakeCommand
 *
 * @package Alograg\DevTools\Console
 */
class ModelMakeCommand extends GeneratorCommand
{
    /**
     *
     */
    const STUBS_MODEL_STUB = '/../stubs/model.stub';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (parent::handle() !== false) {
            // if ($this->option('migration')) {
            //     $table = Str::plural(Str::snake(class_basename($this->argument('name'))));
            //     $this->call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]);
            // }
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

        return $template ? base_path($template) : realpath(__DIR__ . self::STUBS_MODEL_STUB);
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
        return $rootNamespace;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $defaultPath = str_replace(base_path(), '', realpath(__DIR__ . self::STUBS_MODEL_STUB));

        return [
            [
                'template',
                't',
                InputOption::VALUE_OPTIONAL,
                'Relative path to stub file. DEFAULT: ' . $defaultPath,
            ],
            // ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model.'],
        ];
    }

}
