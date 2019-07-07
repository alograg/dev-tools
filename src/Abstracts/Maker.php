<?php
namespace Alograg\DevTools\Abstracts;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

abstract class Maker extends GeneratorCommand
{
    public static $basePath;

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        self::$basePath = realpath($this->option('stub-path') ?: __DIR__ . '/../');
        if (parent::handle() === false) {
            return false;
        }

        return null;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $name = Str::singular(strtolower($this->type));
        $variation = null;
        foreach ($this->options as $option) {
            if ($this->option($option)) {
                $variation = $option;
            }
        }

        return self::getStubsPath() . implode('.', array_filter([$name, $variation, 'stub']));
        // if ($this->option('parent')) {
        //     return self::getStubsPath() .'controller.nested.stub';
        // } elseif ($this->option('model')) {
        //     return self::getStubsPath() .'controller.model.stub';
        // } elseif ($this->option('resource')) {
        //     return self::getStubsPath() .'controller.stub';
        // }
        //
        // return self::getStubsPath() .'controller.plain.stub';
    }

    /**
     * @return string
     */
    public static function getStubsPath(): string
    {
        return self::$basePath . '/stubs/';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['stub-path', 'sp', InputOption::VALUE_OPTIONAL, 'Path relative a stubs repo'],
        ];
    }

}
