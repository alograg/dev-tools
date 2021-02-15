<?php
namespace Alograg\DevTools\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Str;

class DisplayEnvConfigCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = <<<TEXT
show:env:config
TEXT;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show enviroment config';
    /**
     * @var array|bool|string|null
     */
    protected $key;

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        print json_encode(config()->all(), JSON_PRETTY_PRINT);
    }
}
