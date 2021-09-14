<?php 
namespace Tripay\Payments;

use Illuminate\Support\Facades\Facade;

class PaymentTripayFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PaymentTripay';
    }
}
?>