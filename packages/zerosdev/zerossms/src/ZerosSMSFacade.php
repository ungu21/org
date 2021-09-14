<?php

namespace ZerosDev\ZerosSMS;

use Illuminate\Support\Facades\Facade;

class ZerosSMSFacade extends Facade
{
	protected static function getFacadeAccessor() {
	    return 'ZerosSMS';
	}
}