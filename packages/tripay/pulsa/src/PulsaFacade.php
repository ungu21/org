<?php

namespace Tripay\Pulsa;

use Illuminate\Support\Facades\Facade;

class PulsaFacade extends Facade
{
	protected static function getFacadeAccessor() {
	    return 'Pulsa';
	}
}