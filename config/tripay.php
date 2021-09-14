<?php

/**
*	TriPay Confirguration
*
*	If you don't have an account, please register first
*	in https://tripay.co.id.
*
**/

return [
    
    /**
	*
	*	TriPay API URL
	*
	**/
    
    'api_baseurl'   => 'https://tripay.id/api/v2',

	'api_key'	=> env('TRIPAY_API_KEY', 'your_api_key_here'),

	'pin'	=> env('TRIPAY_PIN', 'your_pin_here'),
	
	'callback_secret'	=> env('TRIPAY_CALLBACK_SECRET', '')

];