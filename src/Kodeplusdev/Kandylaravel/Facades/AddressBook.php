<?php
/**
 * Bootstrapper Image facade
 */

namespace Kodeplusdev\Kandylaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Label class
 *
 * @package Bootstrapper\Facades
 * @see     Bootstrapper\Label
 */
class AddressBook extends Facade
{   /**
     * {@inheritdoc}
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kandylaravel::addressBook';
    }

}
