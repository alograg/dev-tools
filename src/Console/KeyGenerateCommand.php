<?php
namespace Alograg\DevTools\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Str;

class KeyGenerateCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = <<<TEXT
key:generate
    {--key=APP_KEY : Key to modify files}
    {--s|show : Display the key instead of modifying files}
TEXT;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application key';
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
        $this->key = $this->option('key');
        if ($this->option('show')) {
            $this->line('<comment>' . env($this->key) . '</comment>');
            return;
        }
        $key = $this->generateRandomKey();
        // Next, we will replace the application key in the environment file so it is
        // automatically setup for this developer. This key gets generated using a
        // secure random byte generator and is later base64 encoded for storage.
        if (!$this->setKeyInEnvironmentFile($key)) {
            return;
        }
        $this->info("Application key [$key] set successfully.");
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     * @throws \Exception
     */
    protected function generateRandomKey()
    {
        return Str::random(32);
    }

    /**
     * Set the application key in the environment file.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey = env($this->key);
        if (strlen($currentKey) !== 0 && (!$this->confirmToProceed())) {
            return false;
        }
        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param string $key
     *
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key)
    {
        $basePath = base_path('.env');
        $keyReplacementPattern = $this->keyReplacementPattern();
        $newKey = $this->key . '=' . $key;
        $envContents = file_get_contents($basePath);
        $pregReplace = preg_replace(
            $keyReplacementPattern,
            $newKey,
            $envContents
        );
        file_put_contents($basePath, $pregReplace);
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $currentKey = env($this->key);
        $escaped = preg_quote('=' . $currentKey, '/');

        return "/^{$this->key}{$escaped}/m";
    }
}
