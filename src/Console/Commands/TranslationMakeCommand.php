<?php
namespace Alograg\DevTools\Console\Commands;

use Alograg\DevTools\Abstracts\Maker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class TranslationMakeCommand
 *
 * Generate Translation File
 *
 * @package Alograg\DevTools\Console\Commands
 */
class TranslationMakeCommand extends Maker
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:translation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = <<<TXT
Create a new translation file
TXT;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Translation';

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getTranslationPath($name);
        $this->makeDirectory($path);
        if (!$this->files->exists($path)) {
            $this->files->put($path, $this->buildTranslation($name));
            $this->info('Translation generated:');
            $this->warn($path);
        } else {
            $this->info('Translation exist:');
            $this->error($path);
        }

        return null;
    }

    /**
     * Get the translation path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getTranslationPath($name)
    {
        $name = $this->getTranslationName($name);

        return base_path() . '/resources/lang/en/' . $name . '.php';
    }

    /**
     * Get the translation name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getTranslationName($name)
    {
        $name = $this->getRouteName($name);
        $name = Arr::last(explode('.', $name));

        return $name;
    }

    /**
     * Build the translation with the given name.
     *
     * @param string $name
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildTranslation($name)
    {
        $name = $this->getRouteName($name);
        $name = Arr::last(explode('.', $name));
        $name = str_replace('_', ' ', $name);
        $replace = [
            'dummy_model_plural_variable' => $name,
            'dummy_model_variable'        => Str::singular($name),
        ];

        return str_replace(array_keys($replace), array_values($replace),
            $this->files->get($this->getTranslationStub()));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getTranslationStub()
    {
        return self::getStubsPath() . 'translation.stub';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
        ]);
    }

}
