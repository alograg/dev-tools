<?php
namespace Alograg\DevTools\Abstracts;

use Alograg\DevTools\Traits\MakerTrait;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

abstract class Maker extends GeneratorCommand
{
    use MakerTrait;

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
