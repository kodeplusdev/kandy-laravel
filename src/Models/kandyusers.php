<?php
namespace Kodeplus\Kandylaravel;

use Config;
use Illuminate\Database\Eloquent\Model as Eloquent;

class KandyUsers extends Eloquent
{
    /**
     * Table prefix
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        // Set the prefix
        $tableName = \Config::get('kandy-laravel.kandy_user_table');
        $this->table = $this->prefix . $tableName;
    }
}

?>
