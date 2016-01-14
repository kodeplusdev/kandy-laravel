<?php
namespace Kodeplus\Kandylaravel;

use Illuminate\Support\ServiceProvider;
use Event;

class KandylaravelServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $eventHandler = new EventHandler();
        Event::subscribe($eventHandler);

        $this->loadViewsFrom(__DIR__.'/views', 'kandy-laravel');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/routes.php';
        $this->app->make('Kodeplus\Kandylaravel\KandyController');

        $this->registerKandyLaravel();

        $this->registerVideo();

        $this->registerButton();

        $this->registerStatus();

        $this->registerAddressBook();

        $this->registerChat();

        $this->registerLiveChat();

        $this->registerCoBrowsing();

        $this->registerSms();

        $this->publishAssets();

        $this->publishMigrations();

        $this->publishPackage();
    }

    /**
     * Auto publish assets when update core
     */
    public function publishAssets(){
        $this->publishes([
            __DIR__.'/../public/assets' => public_path('kandy-io/kandy-laravel/assets'),
        ], 'public');
    }

    /**
     * Auto publish migrations when update core
     */
    public function publishMigrations(){
        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Auto publish package config when update core
     */
    public function publishPackage(){
        $this->publishes([
            __DIR__.'/config/kandy-laravel.php' => config_path('kandy-laravel.php')
        ], 'config');
    }

    /**
     * Register Kandy Laravel
     */
    private function registerKandyLaravel()
    {
        $this->app->bind(
            'kandy-laravel.KandyLaravel',
            function () {
                $kandy = new Kandylaravel();

                $kandy->htmlBuilder = $this->app->make
                (
                    'Illuminate\Html\HtmlBuilder'
                );

                return $kandy;
            }
        );
    }

    /**
     * Register Video
     */
    private function registerVideo()
    {
        $this->app->bind(
            'kandy-laravel.video',
            function () {
                return new Video();
            }
        );
    }

    /**
     * Register Button
     */
    private function registerButton()
    {
        $this->app->bind(
            'kandy-laravel.button',
            function () {
                return new Button();
            }
        );
    }

    /**
     * Register Status
     */
    private function registerStatus()
    {
        $this->app->bind(
            'kandy-laravel.status',
            function () {
                return new Status();
            }
        );
    }

    /**
     * Register address book
     */
    private function registerAddressBook()
    {
        $this->app->bind(
            'kandy-laravel.addressBook',
            function () {
                return new AddressBook();
            }
        );
    }

    /**
     * Register Chat
     */
    private function registerChat()
    {
        $this->app->bind(
            'kandy-laravel.chat',
            function () {
                return new Chat();
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerLiveChat()
    {
        $this->app['kandy-laravel.liveChat'] = $this->app->share(function($app)
        {
            return new \Kodeplus\Kandylaravel\liveChat();
        });
    }

    private function registerCoBrowsing()
    {
        $this->app['kandy-laravel.coBrowsing'] = $this->app->share(function($app)
        {
            return new \Kodeplus\Kandylaravel\CoBrowsing();
        });
    }

    private function registerSms()
    {
        $this->app['kandy-laravel.sms'] = $this->app->share(function($app)
        {
            return new \Kodeplus\Kandylaravel\Sms();
        });
    }
}
