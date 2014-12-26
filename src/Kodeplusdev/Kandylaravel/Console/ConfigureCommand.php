<?php namespace Kodeplusdev\Kandylaravel\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ConfigureCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kandylaravel:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes configuration for KandyLaravel Package.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->call('config:publish', array('package' => 'kodeplusdev/kandylaravel'));
    }

}