<?php
/**
 * Kandy Status facade
 */

namespace Kodeplus\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Status class
 *
 * @package Kodeplus\Kandylaravel\Facades
 * @see     Kodeplus\Kandylaravel\Status
 */
class Status extends Facade
{
    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kandy-laravel.status';
    }

}
