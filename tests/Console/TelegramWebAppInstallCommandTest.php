<?php

namespace Micromagicman\TelegramWebApp\Tests\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TelegramWebAppInstallCommandTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    public function test_install_publishes_config_and_scaffolds_files()
    {
        $this->cleanScaffold();

        $exitCode = Artisan::call('telegram-webapp:install');

        $this->assertSame(0, $exitCode);
        $this->assertFileExists(config_path('telegram-webapp.php'));
        $this->assertFileExists(app_path('Http/Controllers/TelegramWebAppController.php'));
        $this->assertFileExists(base_path('routes/telegram-webapp.php'));
        $this->assertFileExists(resource_path('views/telegram-webapp.blade.php'));
    }

    #[Test]
    public function test_install_with_no_scaffold_publishes_config_only()
    {
        $this->cleanScaffold();

        $exitCode = Artisan::call('telegram-webapp:install', ['--no-scaffold' => true]);

        $this->assertSame(0, $exitCode);
        $this->assertFileExists(config_path('telegram-webapp.php'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/TelegramWebAppController.php'));
        $this->assertFileDoesNotExist(base_path('routes/telegram-webapp.php'));
        $this->assertFileDoesNotExist(resource_path('views/telegram-webapp.blade.php'));
    }

    #[Test]
    public function test_install_does_not_overwrite_existing_files()
    {
        $this->cleanScaffold();
        Artisan::call('telegram-webapp:install');
        File::put(app_path('Http/Controllers/TelegramWebAppController.php'), '// custom');

        $exitCode = Artisan::call('telegram-webapp:install');

        $this->assertSame(0, $exitCode);
        $this->assertSame('// custom', File::get(app_path('Http/Controllers/TelegramWebAppController.php')));
    }

    private function cleanScaffold(): void
    {
        File::delete([
            config_path('telegram-webapp.php'),
            app_path('Http/Controllers/TelegramWebAppController.php'),
            base_path('routes/telegram-webapp.php'),
            resource_path('views/telegram-webapp.blade.php'),
        ]);
    }
}
