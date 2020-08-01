<?php

namespace Caishni\Fawry\Tests;

use Caishni\Fawry\FawryServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $testUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->testUser = User::first();
    }

    protected function getPackageProviders($app)
    {
        return [
            FawryServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('fawry.merchant_code', '1tSa6uxz2nS4logVqySD9w==');
        $app['config']->set('fawry.security_key', '384bf62678b14f05b33581c0fe4b4fe3');

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('phone');
        });

        User::create(['email' => 'test@user.com', 'phone' => '01208702602']);
    }
}