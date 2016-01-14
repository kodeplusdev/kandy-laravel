<?php
/**
 * Kandy Laravel facade
 */

namespace Kodeplus\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the KandyLaravel class
 *
 * @package Kodeplus\Kandylaravel\Facades
 * @see     Kodeplus\Kandylaravel\KandyLaravel
 */
class KandyLaravel extends Facade
{

    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kandy-laravel.KandyLaravel';
    }

}
