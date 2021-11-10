<?php

namespace Georgie\AutoCreate;

use Illuminate\Support\Facades\Facade;

/**
 * Class Facade
 *
 * @package Georgie\Module
 */
class Factory extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'GAutoCreate';
    }
}
