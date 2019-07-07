<?php
namespace Alograg\DevTools\Traits;

use Illuminate\Support\Str;

/**
 * Trait MakerTrait
 *
 * @property array options
 * @package Alograg\DevTools\Traits
 */
trait MakerTrait
{
    /**
     * @var
     */
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
    }

    /**
     * @return string
     */
    public static function getStubsPath(): string
    {
        return self::$basePath . '/stubs/';
    }

}
