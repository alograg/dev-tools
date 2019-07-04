<?php


namespace Alograg\DevTools\Abstracts;


use Illuminate\Console\GeneratorCommand;

abstract class Maker extends GeneratorCommand
{
  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    if ($this->option('parent')) {
      return __DIR__.'/stubs/controller.nested.stub';
    } elseif ($this->option('model')) {
      return __DIR__.'/stubs/controller.model.stub';
    } elseif ($this->option('resource')) {
      return __DIR__.'/stubs/controller.stub';
    }

    return __DIR__.'/stubs/controller.plain.stub';
  }
}
