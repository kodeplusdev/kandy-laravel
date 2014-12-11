<?php
namespace Kodeplusdev\Kandylaravel;

use Illuminate\Database\Eloquent\Model as Eloquent;
use \Config;
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
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        // Set the prefix
        $tableName = \Config::get('kandylaravel::kandy_user_table');
        $this->table = $this->prefix . $tableName;
    }
}

?>
