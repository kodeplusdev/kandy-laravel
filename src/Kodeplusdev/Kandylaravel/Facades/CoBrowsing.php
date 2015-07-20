<?php
/**
 * Kandy Button facade
 */

namespace Kodeplusdev\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Button class
 *
 * @package Kodeplusdev\Kandylaravel\Facades
 * @see     Kodeplusdev\Kandylaravel\Button
 */
class CoBrowsing extends Facade
{
    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        // (e.g kandy-laravel::chat)
        return 'kandy-laravel::coBrowsing';
    }

}
