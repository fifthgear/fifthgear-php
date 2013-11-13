<?php

require_once('fifthgear.php');

// First with setup the Fifth Gear API
$fg = new FifthGear('companyid', 'username', 'password', 'dev');

///
// How to access the different testing methods
// localhost/examples.php?service=inventory
// Or you can pick the service manually by uncommenting the defaultService below.
///

//$defaultService = "inventory";
//$defaultService = "bulkinventory";
//$defaultService = "getItemDetail";
//$defaultService = "personalization";
$defaultService = "placeorder";
//$defaultService = "trackorder";
//$defaultService = "codes";

$testService = (array_key_exists("service", $_GET)) ? $_GET['service'] : $defaultService;

header("Content-Type:text/json");

switch($testService) {

/***************************************
	    ______                    __                __             
	   /  _/ /____  ____ ___     / /   ____  ____  / /____  ______ 
	   / // __/ _ \/ __ `__ \   / /   / __ \/ __ \/ //_/ / / / __ \
	 _/ // /_/  __/ / / / / /  / /___/ /_/ / /_/ / ,< / /_/ / /_/ /
	/___/\__/\___/_/ /_/ /_/  /_____/\____/\____/_/|_|\__,_/ .___/ 
	                                                      /_/           
***************************************/

	case "getItemDetail" :

		$results = $fg->getItemDetail('WP-136');
		echo json_encode($results);

	break;

/***************************************
	   ______     __     ______                    ____                                   ___             __  _           
	  / ____/__  / /_   /  _/ /____  ____ ___     / __ \___  ______________  ____  ____ _/ (_)___  ____ _/ /_(_)___  ____ 
	 / / __/ _ \/ __/   / // __/ _ \/ __ `__ \   / /_/ / _ \/ ___/ ___/ __ \/ __ \/ __ `/ / /_  / / __ `/ __/ / __ \/ __ \
	/ /_/ /  __/ /_   _/ // /_/  __/ / / / / /  / ____/  __/ /  (__  ) /_/ / / / / /_/ / / / / /_/ /_/ / /_/ / /_/ / / / /
	\____/\___/\__/  /___/\__/\___/_/ /_/ /_/  /_/    \___/_/  /____/\____/_/ /_/\__,_/_/_/ /___/\__,_/\__/_/\____/_/ /_/ 

	Item Personalization fields can be pulled from Fifth Gear using the getItemPersonalizationOptions method. 

	It's recommended that this call isn't used in real time. This method is very resource and time intensive, 
	and should be used to rarely to pull down the data for local storage.
	                                                                                                                      
***************************************/

	case "personalization" : 

		$results = $fg->getItemPersonalizationOptions('WP-136');
		echo json_encode($results);

	break;

/**********************************************************************************************************************************
	    ____                      __                      __                __             
	   /  _/___ _   _____  ____  / /_____  _______  __   / /   ____  ____  / /____  ______ 
	   / // __ \ | / / _ \/ __ \/ __/ __ \/ ___/ / / /  / /   / __ \/ __ \/ //_/ / / / __ \
	 _/ // / / / |/ /  __/ / / / /_/ /_/ / /  / /_/ /  / /___/ /_/ / /_/ / ,< / /_/ / /_/ /
	/___/_/ /_/|___/\___/_/ /_/\__/\____/_/   \__, /  /_____/\____/\____/_/|_|\__,_/ .___/ 
	                                         /____/                               /_/      
**********************************************************************************************************************************/

	case "inventory" :

		$sku = "CT-103";
		$results = $fg->lookupInventory($sku);
		echo json_encode($results);

	break;


	case "bulkinventory" :

		$results = $fg->lookupInventoryBulk(1,10);
		echo json_encode($results);

	break;

/*****************************************************************************************************
	  ______                __                      __   ____           __         
	 /_  __/________ ______/ /__   ____ _____  ____/ /  / __ \_________/ /__  _____
	  / / / ___/ __ `/ ___/ //_/  / __ `/ __ \/ __  /  / / / / ___/ __  / _ \/ ___/
	 / / / /  / /_/ / /__/ ,<    / /_/ / / / / /_/ /  / /_/ / /  / /_/ /  __/ /    
	/_/ /_/   \__,_/\___/_/|_|   \__,_/_/ /_/\__,_/   \____/_/   \__,_/\___/_/     
	                                                                               
******************************************************************************************************/

	case "trackorder"  :

		$orderid = "12345";
		$results = $fg->trackOrder($orderid);
		echo json_encode($results);


	break;

/*****************************************************************************************************
	    ____  __                   ___             ____           __         
	   / __ \/ /___ _________     /   |  ____     / __ \_________/ /__  _____
	  / /_/ / / __ `/ ___/ _ \   / /| | / __ \   / / / / ___/ __  / _ \/ ___/
	 / ____/ / /_/ / /__/  __/  / ___ |/ / / /  / /_/ / /  / /_/ /  __/ /    
	/_/   /_/\__,_/\___/\___/  /_/  |_/_/ /_/   \____/_/   \__,_/\___/_/     
	                                                                         
*****************************************************************************************************/
	case "placeorder" : 

		// Test the different payment types Cash or Credit

		//$type = "cash"; 
		$type = "credit";

		// Add a customer to this order
		$fg->addCustomer(array(
			'firstName'	=>'Bob',
			'lastName'	=>'Smith',
			'email'		=>'bob.smith@prodigy.net'
		));

		// The Order ID MUST BE UNIQUE. Duplicate order ID's will fail.
		// In this example we'll just generate a random 10 character string.
		$fg->setOrderId('order-'.substr(md5(time().rand(100,10900000)), 0,10)); 

		// the Address Method can be used to populate both "shipping" "billing" or "both";
		// Make sure to provide an email address for the address block. 
		$fg->addAddress('both', array(
			'address'	=>'123456 Pine View Dr',
			'address2'	=>'Suite 123',
			'postal'	=>46032,
			'city'		=>'Carmel',
			'state'		=>'IN',
			'country'	=>'USA',
			'firstName'	=>'Bob',
			'lastName'	=>'Smith',
			'email'		=>'bob.smith@prodigy.net'
		));

		// Add a $12 shipping charge.
		$fg->addShippingCharge(12);


		/*
			::: ITEMS WITH PERSONALIZATION :::

			You can also add items to your cart that support product personalization.
			You first need to know the different personalization options for a given option using the getItemPersonalizationOptions() method. 
			- Pass the TemplateNumber (as a string) for the item
			- Pass an array of personalizations options 
				- Number = the TemplateSequence 
				- Response = the data to be personalized
		
		*/

		// Add a item to the order
		$fg->addItem(array(
			'amount'=> 900,
			'sku' 	=> 'WP-136',
			'qty' 	=> 1
		));

		// Add an item with personalization to the order
		$fg->addItem(array(
			'amount'=> 900,
			'sku' 	=> 'WP-136',
			'qty' 	=> 1,
			'personalizationTemplateNumber'=>'62', // Must be a string
			'personalizations'=>array(
				array( 'Number'=>1, 'Response'=>'1971'),
				array( 'Number'=>2, 'Response'=>'VIN123'),
				array( 'Number'=>3, 'Response'=>'1234-motor-date-code')
			)
		));


		$fg->order->data->Request->OrderMessage1 = "This is message 1";
		$fg->order->data->Request->OrderMessage2 = "This is message 2";
		$fg->order->data->Request->OrderMessage3 = "This is message 3";
		$fg->order->data->Request->OrderMessage4 = "This is message 4";
		$fg->order->data->Request->OrderMessage5 = "This is message 5";
		$fg->order->data->Request->OrderMessage6 = "This is message 6";



		/***************************************
		
		Checking out with Cash or Credit

		***************************************/

		if($type=="credit") {
			// Using a Credit Card
			$fg->addPayment(array(
				'number' 	=> '4111111111111111',
				'nameOnCard'=> 'Brandon McSmith',
				'cvv' 		=> '123',
				'month' 	=> '03',
				'year' 		=> '2044'
			));
		} elseif($type=="cash") {
			// Using a "Cash" - any check number will do.
			$fg->addCashPayment(array(
				'checkNumber'=>1001
			));
		}
		
		$output = array(
			'placeorder'=>$fg->placeOrder(),
			'orderData'=>$fg->getOrderData()
		);

		$results = $fg->placeOrder();
		
		$results = $fg->getOrderData();
		echo json_encode($results);

	break; 

/**************************************************************************************************************************************************
	   _____ __        __                          __   ______                  __                ______          __         
	  / ___// /_____ _/ /____     ____ _____  ____/ /  / ____/___  __  ______  / /________  __   / ____/___  ____/ /__  _____
	  \__ \/ __/ __ `/ __/ _ \   / __ `/ __ \/ __  /  / /   / __ \/ / / / __ \/ __/ ___/ / / /  / /   / __ \/ __  / _ \/ ___/
	 ___/ / /_/ /_/ / /_/  __/  / /_/ / / / / /_/ /  / /___/ /_/ / /_/ / / / / /_/ /  / /_/ /  / /___/ /_/ / /_/ /  __(__  ) 
	/____/\__/\__,_/\__/\___/   \__,_/_/ /_/\__,_/   \____/\____/\__,_/_/ /_/\__/_/   \__, /   \____/\____/\__,_/\___/____/  
	                                                                                 /____/                                  
***************************************************************************************************************************************************/

	case "codes" : 
		
		echo json_encode(array(
			'CAN' => $fg->getCountryCode('CAN'),
			'USA' => $fg->getCountryCode('USA'),
			'GRC' => $fg->getCountryCode('GRC'),
			'IN' => $fg->getStateCode('IN'),
			'OH' => $fg->getStateCode('OH'),
			'CA' => $fg->getStateCode('CA')
		));

	break;

}



