<?php
namespace Alograg\DevTools\Console\Commands;

use Alograg\DevTools\Traits\MakerTrait;
use Illuminate\Database\Console\Factories\FactoryMakeCommand as Command;

class FactoryMakeCommand extends Command
{
    use MakerTrait;
}
