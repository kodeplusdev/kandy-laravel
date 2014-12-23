<?php
/**
 * Kandy Chat facade
 */

namespace Kodeplusdev\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Chat class
 *
 * @package Kodeplusdev\Kandylaravel\Facades
 * @see     Kodeplusdev\Kandylaravel\Chat
 */
class Chat extends Facade
{

    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kandylaravel::chat';
    }

}
