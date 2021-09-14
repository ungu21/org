<?php 
namespace Tripay\Payments;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class PaymentTripayServiceProvider extends ServiceProvider
{
     /**
     * Bootstrap the application services.
     * 
     * @return void
     */

    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     *@return void 
     */

     public function register()
     {
         App::bind('PaymentTripay',function(){
             return new PaymentTripay;
         });
     }
}
?>