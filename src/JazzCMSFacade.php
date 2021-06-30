<?php

namespace NsTechNs\JazzCMS;

use Illuminate\Support\Facades\Facade;

class JazzCMSFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return JazzCMS::class;
    }
}
