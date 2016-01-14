<?php
/**
 * Kandy AddressBook facade
 */

namespace Kodeplus\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the AddressBook class
 *
 * @package Kodeplus\Kandylaravel\Facades
 * @see     Kodeplus\Kandylaravel\AddressBook
 */
class AddressBook extends Facade
{
    /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kandy-laravel.addressBook';
    }

}
