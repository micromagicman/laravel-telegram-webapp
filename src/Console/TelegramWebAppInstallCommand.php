<?php

namespace Micromagicman\TelegramWebApp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Micromagicman\TelegramWebApp\TelegramWebAppServiceProvider;

/**
 * Install Telegram WebApp: publish the configuration and scaffold a sample
 * controller, route file and blade view, so newcomers can get started without
 * hand-writing each piece.
 */
class TelegramWebAppInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram-webapp:install
                            {--no-scaffold : Skip scaffolding the sample controller, route and view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the config and scaffold a sample controller, route and view';

    public function handle(): int
    {
        $this->publishConfig();

        if (! $this->option('no-scaffold')) {
            $this->scaffold();
        }

        $this->printEnvHint();

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        $this->call('vendor:publish', [
            '--provider' => TelegramWebAppServiceProvider::class,
            '--tag' => 'telegram-webapp-config',
        ]);
    }

    private function scaffold(): void
    {
        $this->copyStub(
            __DIR__.'/../../stubs/TelegramWebAppController.stub',
            app_path('Http/Controllers/TelegramWebAppController.php')
        );

        $this->copyStub(
            __DIR__.'/../../stubs/telegram-webapp-routes.stub',
            base_path('routes/telegram-webapp.php')
        );

        $this->copyStub(
            __DIR__.'/../../stubs/telegram-webapp.blade.stub',
            resource_path('views/telegram-webapp.blade.php')
        );

        $this->warn('Register the routes by adding to routes/web.php:');
        $this->line("    require base_path('routes/telegram-webapp.php');");
    }

    private function copyStub(string $source, string $destination): void
    {
        if (File::exists($destination)) {
            $this->warn("Skipped existing file: {$destination}");

            return;
        }

        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);
        $this->info("Created: {$destination}");
    }

    private function printEnvHint(): void
    {
        $this->newLine();
        $this->info('Add the following to your .env file:');
        $this->line('    TELEGRAM_BOT_TOKEN=<your-bot-token>');
    }
}
