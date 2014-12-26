<?php namespace Kodeplusdev\Kandylaravel\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kandylaravel:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Command for KandyLaravel Package.';

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
        //$this->call('kandylaravel:config');
        $this->call('kandylaravel:assets');
        /*if ($this->confirm('Have you configured your database yet?')) {
            $this->call('kandylaravel:migrate');
        } else {
            $this->comment('Your database has not been migrated, run artisan faq:migrate before use');
        }*/
    }

}