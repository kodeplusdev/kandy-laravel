<?php
namespace Kodeplusdev\Kandylaravel;
use Kodeplusdev\Kandylaravel\Console;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Support\ServiceProvider;
use Artisan;

class KandylaravelServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('kodeplusdev/kandylaravel');
        include __DIR__.'/../../routes.php';
        Artisan::call('asset:publish kodeplusdev/kandylaravel');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        $this->registerKandyLaravel();

        $this->registerVideo();

        $this->registerButton();

        $this->registerStatus();

        $this->registerAddressBook();

        $this->registerChat();
    }

    /**
     * Register Kandy Laravel
     */
    private function registerKandyLaravel()
    {
        $this->app->bind(
            'kandylaravel::KandyLaravel',
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
            'kandylaravel::video',
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
            'kandylaravel::button',
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
            'kandylaravel::status',
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
            'kandylaravel::addressBook',
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
            'kandylaravel::chat',
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
    /**
     * Register Installer Command
     */
    public function registerCommands()
    {
        $this->registerMigrateCommand();
        $this->registerConfigureCommand();
        $this->registerAssetsCommand();
        $this->registerInstallCommand();

        $this->commands(
            'kandylaravel::commands.install'
        );
    }

    /**
     * Register Migrate Command
     */
    public function registerMigrateCommand()
    {
        $this->app['kandylaravel::commands.migrate'] = $this->app->share(function($app)
            {
                return new Console\MigrateCommand;
            });
    }

    /**
     * Register Configure Command
     */
    public function registerConfigureCommand()
    {
        $this->app['kandylaravel::commands.config'] = $this->app->share(function($app)
            {
                return new Console\ConfigureCommand;
            });
    }

    /**
     * Register Assets Command
     */
    public function registerAssetsCommand()
    {
        $this->app['kandylaravel::commands.assets'] = $this->app->share(function($app)
            {
                return new Console\AssetsCommand;
            });
    }

    /**
     * Register InstallCommand
     */
    public function registerInstallCommand()
    {
        $this->app['kandylaravel::commands.install'] = $this->app->share(function($app)
            {
                return new Console\InstallCommand;
            });
    }

}
