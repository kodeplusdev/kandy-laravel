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
class Button extends Facade
{
    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        //TODO: why Button has B in capital whereas others are in lower case
        // (e.g kandylaravel::chat)
        return 'kandylaravel::Button';
    }

}
