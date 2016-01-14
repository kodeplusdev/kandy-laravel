<?php
/**
 * Created by PhpStorm.
 * User: Khanh
 * Date: 22/7/2015
 * Time: 10:33 AM
 */

namespace Kodeplus\Kandylaravel;
use Config;
use Illuminate\Database\Eloquent\Model as Eloquent;


class KandyUserLogin extends Eloquent {
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

    protected $table = 'kandy_user_login';

    public $timestamps = false;

    protected $fillable = array(
        'kandy_user_id', 'type', 'status', 'browser_agent','ip_address', 'time'
    );

} 