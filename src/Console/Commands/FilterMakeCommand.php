<?php
namespace Alograg\DevTools\Console\Commands;

use Alograg\DevTools\Abstracts\Maker;
use Illuminate\Filesystem\Filesystem;
use Laravel\Lumen\Application;
use Symfony\Component\Console\Input\InputOption;

/**
 * Filter Make Command
 */
class FilterMakeCommand extends Maker
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:filter';

    /**
     * Laravel application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = <<<TXT
Create a new filter class
TXT;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter';

    /**
     * @var array
     */
    protected $options = [
        'base',
    ];

    public function __construct(Filesystem $files, Application $app)
    {
        parent::__construct($files);
        $this->app = $app;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['base', null, InputOption::VALUE_NONE, 'From base filter'],
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
        if (!$this->alreadyExists('BaseFilter')) {
            $name = $this->qualifyClass('BaseFilter');
            $path = $this->getPath($name);
            // Next, we will generate the path to the location where this class' file should get
            // written. Then, we will build the class and make the proper replacements on the
            // stub files so that it gets the correctly formatted namespace and class name.
            $this->makeDirectory($path);
            $this->files->put($path, $this->buildClass($name));
        }

        return parent::handle();
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
        return $this->app['config']->get('filters.namespace');
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        if ($this->app['config']->get('filters.path')) {
            return $this->app['config']->get('filters.path') . '/' . class_basename($name) . '.php';
        } else {
            return parent::getPath($name);
        }
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        if ($name == $this->qualifyClass('BaseFilter')) {
            $stub = $this->files->get($this->getStub());

            return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
        }

        return parent::buildClass($name);
    }
}
