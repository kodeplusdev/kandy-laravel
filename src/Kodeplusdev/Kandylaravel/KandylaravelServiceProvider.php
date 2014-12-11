<?php namespace Kodeplusdev\Kandylaravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Html\HtmlBuilder;
class KandylaravelServiceProvider extends ServiceProvider {

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
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{


        $this->registerVideo();
        $this->registerKandyLaravel();

        /*$this->app->booting(function()
            {
                $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                $loader->alias('Kandylaravel', 'Kodeplusdev\Kandylaravel\Facades\Kandylaravel');
            });*/
	}
    private function registerVideo()
    {
        $this->app->bind(
            'kandylaravel::video',
            function () {
                return new Video();
            }
        );
    }
    private function registerKandyLaravel()
    {
        $this->app->bind(
            'kandylaravel::KandyLaravel',
            function () {
                return new Kandylaravel(
                    $this->app->make('Illuminate\Html\HtmlBuilder')
                );
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

}
