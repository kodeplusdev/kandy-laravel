<?php
namespace Kodeplusdev\Kandylaravel;
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
        $this->package('kandy-io/kandy-laravel');
        include __DIR__.'/../../routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerKandyLaravel();

        $this->registerVideo();

        $this->registerButton();

        $this->registerStatus();

        $this->registerAddressBook();

        $this->registerChat();

        $this->registerGroupChat();

        $this->publishAssets();

    }

    /**
     * Auto publish assets when update core
     */
    public function publishAssets(){
        if(isset($this->app['artisan'])){
            $this->app['artisan']->call("asset:publish", array("kandy-io/kandy-laravel"));
        }

    }

    /**
     * Register Kandy Laravel
     */
    private function registerKandyLaravel()
    {
        $this->app->bind(
            'kandy-laravel::KandyLaravel',
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
            'kandy-laravel::video',
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
            'kandy-laravel::button',
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
            'kandy-laravel::status',
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
            'kandy-laravel::addressBook',
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
            'kandy-laravel::chat',
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

    private function registerGroupChat()
    {
        $this->app->bind(
            'kandy-laravel::groupChat',
            function () {
                return new GroupChat();
            }
        );
    }

}
