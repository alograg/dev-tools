<?php
namespace Alograg\DevTools\Abstracts;

use Alograg\DevTools\Traits\MakerTrait;
use Illuminate\Console\GeneratorCommand;

abstract class Maker extends GeneratorCommand
{
    use MakerTrait;
}
