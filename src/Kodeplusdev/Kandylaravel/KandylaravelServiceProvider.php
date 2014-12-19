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
//        $this->app['kandylaravel::Kandylaravel'] = $this->app->share(function($app)
//        {
//            $kandy = new Kandylaravel;
//
////            $kandy->html = $this->app->make('Illuminate\Html\HtmlBuilder');
//
//            return $kandy;
//        });

        $this->registerKandyLaravel();

        $this->registerVideo();

        $this->registerButton();
	}

    /**
     *
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
     *
     */
    private function registerButton()
    {
        $this->app->bind(
            'kandylaravel::Button',
            function () {
                return new Button();
            }
        );
    }

    private function registerKandyLaravel()
    {
        $this->app->bind(
            'kandylaravel::KandyLaravel',
            function () {
                $kandy = new Kandylaravel(
//                    $this->app->make('Illuminate\Html\HtmlBuilder')
                );

                $kandy->html = $this->app->make('Illuminate\Html\HtmlBuilder');

                return $kandy;
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
