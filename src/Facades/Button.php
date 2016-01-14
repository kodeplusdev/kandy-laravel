<?php
/**
 * Kandy Button facade
 */

namespace Kodeplus\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Button class
 *
 * @package Kodeplus\Kandylaravel\Facades
 * @see     Kodeplus\Kandylaravel\Button
 */
class Button extends Facade
{
    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        // (e.g kandy-laravel.chat)
        return 'kandy-laravel.button';
    }

}
