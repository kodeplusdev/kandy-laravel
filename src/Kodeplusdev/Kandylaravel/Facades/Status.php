<?php
/**
 * Kandy Status facade
 */

namespace Kodeplusdev\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Status class
 *
 * @package Kodeplusdev\Kandylaravel\Facades
 * @see     Kodeplusdev\Kandylaravel\Status
 */
class Status extends Facade
{
    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kandy-laravel::status';
    }

}
