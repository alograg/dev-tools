<?php
namespace Alograg\DevTools\Console\Commands;

use Alograg\DevTools\Abstracts\Maker;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * Model Make Command
 */
class ModelMakeCommand extends Maker
{
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
    protected $description = <<<TEXT
 Create a new Eloquent model class
TEXT;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->createFilter();
        if (parent::handle() === false && !$this->option('force')) {
            return false;
        }
        if ($this->option('all')) {
            $this->input->setOption('factory', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('resource', true);
        }
        if ($this->option('factory')) {
            $this->createFactory();
        }
        if ($this->option('migration')) {
            $this->createMigration();
        }
        if ($this->option('controller') || $this->option('resource')) {
            $this->createController();
        }
        $this->generateTranslation();

        return true;
    }

    /**
     * Create a filter for the model.
     *
     * @return void
     */
    protected function createFilter()
    {
        $model = Str::studly(class_basename($this->argument('name')));
        $this->call('make:filter', [
            'name'        => $model . 'Filter',
            '--stub-path' => $this->option('stub-path') ?: null,
        ]);
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createFactory()
    {
        $factory = Str::studly(class_basename($this->argument('name')));
        $this->call('make:factory', [
            'name'        => "{$factory}Factory",
            '--model'     => $this->qualifyClass($this->getNameInput()),
            '--stub-path' => $this->option('stub-path') ?: null,
        ]);
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = $this->option('pivot') ?
            Str::snake(class_basename($this->argument('name'))) :
            Str::plural(Str::snake(class_basename($this->argument('name'))));
        $this->call('make:migration', [
            'name'     => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $controller = Str::studly(class_basename($this->argument('name')));
        $modelName = $this->qualifyClass($this->getNameInput());
        $this->call('make:controller', [
            'name'        => "{$controller}Controller",
            '--model'     => $this->option('resource') ? $modelName : null,
            '--stub-path' => $this->option('stub-path') ?: null,
        ]);
    }

    /**
     * Generate Translation File
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
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
        return $rootNamespace;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            [
                'all',
                'a',
                InputOption::VALUE_NONE,
                'Generate a migration, factory, and resource controller for the model',
            ],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            [
                'pivot',
                'p',
                InputOption::VALUE_NONE,
                'Indicates if the generated model should be a custom intermediate table model',
            ],
            [
                'resource',
                'r',
                InputOption::VALUE_NONE,
                'Indicates if the generated controller should be a resource controller',
            ],
        ]);
    }
}
