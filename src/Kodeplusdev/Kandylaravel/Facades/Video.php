<?php
/**
 * Kandy Video facade
 */

namespace Kodeplusdev\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Video class
 *
 * @package Kodeplusdev\Kandylaravel\Facades
 * @see     Kodeplusdev\Kandylaravel\Video
 */
class Video extends Facade
{
    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kandy-laravel::video';
    }

}
