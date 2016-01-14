<?php
namespace Kodeplus\Kandylaravel;

use Config;
use Illuminate\Database\Eloquent\Model as Eloquent;

class KandyLiveChat extends Eloquent
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

    public $timestamps = false;

    protected $fillable = array(
        'agent_user_id', 'customer_user_id', 'customer_name', 'customer_email','begin_at', 'end_at'
    );

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        // Set the prefix
        $tableName = \Config::get('kandy-laravel.kandy_live_chat_table');
        $this->table = $this->prefix . $tableName;
    }
}

?>
