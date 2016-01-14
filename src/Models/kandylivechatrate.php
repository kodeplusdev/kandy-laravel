<?php
namespace Kodeplus\Kandylaravel;

use Config;
use Illuminate\Database\Eloquent\Model as Eloquent;

class KandyLiveChatRate extends Eloquent
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
        'point', 'main_user_id', 'rated_time', 'comment', 'rated_by'
    );

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        // Set the prefix
        $tableName = \Config::get('kandy-laravel.kandy_live_chat_rate_table');
        $this->table = $this->prefix . $tableName;
    }
}

?>
