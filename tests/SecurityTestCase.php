<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class SecurityTestCase extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        // Refuse a cached production configuration before Laravel is booted.
        $cache = getenv('APP_CONFIG_CACHE');
        if (is_file(dirname(__DIR__).'/bootstrap/cache/config.php') || ($cache && is_file($cache))) {
            throw new \RuntimeException('Execute esta suite em uma cópia de testes sem config cache.');
        }

        $environment = [
            'APP_ENV' => 'testing',
            'APP_DEBUG' => 'false',
            'APP_KEY' => 'base64:'.base64_encode(random_bytes(32)),
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'DB_URL' => '',
            'CACHE_STORE' => 'array',
            'SESSION_DRIVER' => 'array',
            'QUEUE_CONNECTION' => 'sync',
            'MAIL_MAILER' => 'array',
            'FILESYSTEM_DISK' => 'local',
            'AWS_ACCESS_KEY_ID' => '',
            'AWS_SECRET_ACCESS_KEY' => '',
            'AWS_BUCKET' => '',
        ];

        foreach ($environment as $name => $value) {
            putenv($name.'='.$value);
            $_ENV[$name] = $_SERVER[$name] = $value;
        }

        $app = parent::createApplication();
        // No named connection can accidentally point at MySQL/Heroku.
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections', [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        return $app;
    }
}
