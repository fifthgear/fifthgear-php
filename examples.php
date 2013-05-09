<?

require_once('fifthgear.php');
$fg = new FifthGear('companyid', 'username', 'password');

$results = null;

$testService = "inventory";
//$testService = "placeorder";
//$testService = "trackorder";
//$testService = "codes";

header("Content-Type:text/json");

switch($testService) {

/**********************************************************************************************************************************
d888888b d8b   db db    db d88888b d8b   db d888888b  .d88b.  d8888b. db    db   db       .d88b.   .d88b.  db   dD db    db d8888b. 
  `88'   888o  88 88    88 88'     888o  88 `~~88~~' .8P  Y8. 88  `8D `8b  d8'   88      .8P  Y8. .8P  Y8. 88 ,8P' 88    88 88  `8D 
   88    88V8o 88 Y8    8P 88ooooo 88V8o 88    88    88    88 88oobY'  `8bd8'    88      88    88 88    88 88,8P   88    88 88oodD' 
   88    88 V8o88 `8b  d8' 88~~~~~ 88 V8o88    88    88    88 88`8b      88      88      88    88 88    88 88`8b   88    88 88~~~   
  .88.   88  V888  `8bd8'  88.     88  V888    88    `8b  d8' 88 `88.    88      88booo. `8b  d8' `8b  d8' 88 `88. 88b  d88 88      
Y888888P VP   V8P    YP    Y88888P VP   V8P    YP     `Y88P'  88   YD    YP      Y88888P  `Y88P'   `Y88P'  YP   YD ~Y8888P' 88      
**********************************************************************************************************************************/

	case "inventory" :

		$sku = "CT-103";
		$results = $fg->lookupInventory($sku);
		echo json_encode($results);

	break;

/*****************************************************************************************************
d888888b d8888b.  .d8b.   .o88b. db   dD    .d8b.  d8b   db    .d88b.  d8888b. d8888b. d88888b d8888b. 
`~~88~~' 88  `8D d8' `8b d8P  Y8 88 ,8P'   d8' `8b 888o  88   .8P  Y8. 88  `8D 88  `8D 88'     88  `8D 
   88    88oobY' 88ooo88 8P      88,8P     88ooo88 88V8o 88   88    88 88oobY' 88   88 88ooooo 88oobY' 
   88    88`8b   88~~~88 8b      88`8b     88~~~88 88 V8o88   88    88 88`8b   88   88 88~~~~~ 88`8b   
   88    88 `88. 88   88 Y8b  d8 88 `88.   88   88 88  V888   `8b  d8' 88 `88. 88  .8D 88.     88 `88. 
   YP    88   YD YP   YP  `Y88P' YP   YD   YP   YP VP   V8P    `Y88P'  88   YD Y8888D' Y88888P 88   YD 
******************************************************************************************************/

	case "trackorder"  :

		$orderid = "12345";
		$results = $fg->trackOrder($orderid);
		echo json_encode($results);


	break;

/*****************************************************************************************************
d8888b. db       .d8b.   .o88b. d88888b    .d8b.  d8b   db    .d88b.  d8888b. d8888b. d88888b d8888b. 
88  `8D 88      d8' `8b d8P  Y8 88'       d8' `8b 888o  88   .8P  Y8. 88  `8D 88  `8D 88'     88  `8D 
88oodD' 88      88ooo88 8P      88ooooo   88ooo88 88V8o 88   88    88 88oobY' 88   88 88ooooo 88oobY' 
88~~~   88      88~~~88 8b      88~~~~~   88~~~88 88 V8o88   88    88 88`8b   88   88 88~~~~~ 88`8b   
88      88booo. 88   88 Y8b  d8 88.       88   88 88  V888   `8b  d8' 88 `88. 88  .8D 88.     88 `88. 
88      Y88888P YP   YP  `Y88P' Y88888P   YP   YP VP   V8P    `Y88P'  88   YD Y8888D' Y88888P 88   YD 
*****************************************************************************************************/
	case "placeorder" : 

		$fg->addCustomer(array(
			'firstName'=>'Brandon',
			'lastName'=>'McSmith',
			'email'=>'brandon@McSmith.com'
		));

		$fg->setOrderId('1234x5'); // This needs to be a unique ID each time you create an order

		$fg->addAddress('both', array(
			'address'=>'123456 Pine View Dr',
			'address2'=>'Suite 123',
			'postal'=>46032,
			'city'=>'Carmel',
			'state'=>'IN',
			'country'=>'USA',
			'firstName'=>'Brandon',
			'lastName'=>'McSmith'
		));

		$fg->addItem(array(
			'amount' => 1000,
			'sku' => 'CT-103',
			'qty' => 1
		));
		$fg->addItem(array(
			'amount' => 1000,
			'sku' => 'CT-133',
			'qty' => 4
		));

		$fg->addPayment(array(
			'number' => '4111111111111111',
			'nameOnCard' => 'Brandon McSmith',
			'cvv' => '123',
			'month' => '03',
			'year' => '2044'
		));

		$results = $fg->placeOrder();
		echo json_encode($results);

	break; 

/**************************************************************************************************************************************************
.d8888. d888888b  .d8b.  d888888b d88888b    .o88b.  .d88b.  db    db d8b   db d888888b d8888b. db    db    .o88b.  .d88b.  d8888b. d88888b .d8888. 
88'  YP `~~88~~' d8' `8b `~~88~~' 88'       d8P  Y8 .8P  Y8. 88    88 888o  88 `~~88~~' 88  `8D `8b  d8'   d8P  Y8 .8P  Y8. 88  `8D 88'     88'  YP 
`8bo.      88    88ooo88    88    88ooooo   8P      88    88 88    88 88V8o 88    88    88oobY'  `8bd8'    8P      88    88 88   88 88ooooo `8bo.   
  `Y8b.    88    88~~~88    88    88~~~~~   8b      88    88 88    88 88 V8o88    88    88`8b      88      8b      88    88 88   88 88~~~~~   `Y8b. 
db   8D    88    88   88    88    88.       Y8b  d8 `8b  d8' 88b  d88 88  V888    88    88 `88.    88      Y8b  d8 `8b  d8' 88  .8D 88.     db   8D 
`8888Y'    YP    YP   YP    YP    Y88888P    `Y88P'  `Y88P'  ~Y8888P' VP   V8P    YP    88   YD    YP       `Y88P'  `Y88P'  Y8888D' Y88888P `8888Y' 
***************************************************************************************************************************************************/

	case "codes" : 
		
		echo json_encode(array(
			'CAN'	=> $fg->getCountryCode('CAN'),
			'USA'	=> $fg->getCountryCode('USA'),
			'GRC'	=> $fg->getCountryCode('GRC'),
			'IN'	=> $fg->getStateCode('IN'),
			'OH'	=> $fg->getStateCode('OH'),
			'CA'	=> $fg->getStateCode('CA')
		));

	break;

}



