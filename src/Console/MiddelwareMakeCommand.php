<?php
namespace Alograg\DevTools\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MiddelwareMakeCommand extends GeneratorCommand
{
    /**
     *
     */
    const STUBS_MODEL_STUB = '/../stubs/middleware.stub';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:middleware';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new middleware class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Middleware';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (parent::handle() !== false) {
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
                'Relative path to stub file.',
                $defaultPath,
            ],
            // ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model.'],
        ];
    }

}
