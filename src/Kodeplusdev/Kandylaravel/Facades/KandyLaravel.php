<?php
/**
 * Kandy Laravel facade
 */

namespace Kodeplusdev\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the KandyLaravel class
 *
 * @package Kodeplusdev\Kandylaravel\Facades
 * @see     Kodeplusdev\Kandylaravel\KandyLaravel
 */
class KandyLaravel extends Facade
{

    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kandy-laravel::KandyLaravel';
    }

}
