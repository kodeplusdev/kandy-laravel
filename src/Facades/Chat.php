<?php
/**
 * Kandy Chat facade
 */

namespace Kodeplus\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Chat class
 *
 * @package Kodeplus\Kandylaravel\Facades
 * @see     Kodeplus\Kandylaravel\Chat
 */
class Chat extends Facade
{

    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kandy-laravel.chat';
    }

}
