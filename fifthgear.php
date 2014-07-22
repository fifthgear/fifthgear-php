<?php 

/**
*  FifthGear Example Class for PHP
*
* @package    FifthGear
* @version    0.5.11
* ...
*/

/*
d88888b d888888b d88888b d888888b db   db  d888b  d88888b  .d8b.  d8888b.   d8888b. db   db d8888b. 
88'       `88'   88'     `~~88~~' 88   88 88' Y8b 88'     d8' `8b 88  `8D   88  `8D 88   88 88  `8D 
88ooo      88    88ooo      88    88ooo88 88      88ooooo 88ooo88 88oobY'   88oodD' 88ooo88 88oodD' 
88~~~      88    88~~~      88    88~~~88 88  ooo 88~~~~~ 88~~~88 88`8b     88~~~   88~~~88 88~~~   
88        .88.   88         88    88   88 88. ~8~ 88.     88   88 88 `88.   88      88   88 88      
YP      Y888888P YP         YP    YP   YP  Y888P  Y88888P YP   YP 88   YD   88      YP   YP 88      
 
An example API wrapper for Fifth Gear's raw api (http://github.com/fifthgear/fifthgear-raw)

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR 
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE 
USE OR OTHER DEALINGS IN THE SOFTWARE.

Learn more about Fifth Gear at http://www.inFIFTHGEAR.com/                                                                                                                                                                                                                                                                     
*/                              

class FifthGear {

	public $orderData = array();
	public $inventoryData = array();
	public $trackingData = array();
	public $paymentType = null;
	private $paymentZipcode = null;
	private $paymentGateway = 'Authorize.Net';

	/// Config container for company, username, password, and host
	/// Unsure of your credentials or the base path?? Email prodsup 
	public $config = array(
		 'company' 	=> null,	
		 'user' 	=> null,
		 'password' => null,
		 'basepath'	=> null,
		 'host' 	=> 'commerceservices.infifthgear.com'
	);

	/**
	* Contstruct the FifthGear Object
	*
	* companyid, username and password - you can get these from your Customer Care Representative.
	*
	* @param string $companyid
	* @param string $username
	* @param string $password 
	* @return object Self
	*/
	function __construct($companyid, $username, $password, $mode='dev') {
		
		$this->config['company']=$companyid;
		$this->config['user']=$username;
		$this->config['password']=$password;

		if($mode=='prod') {
			$this->config['basepath']="/v2.0/CommerceServices.svc/Rest";
		} else{
			$this->config['basepath']="/v2.0/CommerceServices.svc/Rest";
			$this->config['host'] = "https://commerceservicestest.infifthgear.com"; // New CommerceService API Endpoint
		}

		// Create a blank order request Stub
		$this->order = (object) 'text';
		$this->order->data = json_decode('{
			"CompanyId": "'.$companyid.'",
			"Request": {
				"BillingAddress": {
					"Address1": "",
					"Address2": "",
					"City": "",
					"CountryCode": "",
					"Email": "",
					"Fax": "",
					"IsGiftAddress": false,
					"Organization": "",
					"PhoneNumber": "",
					"PostalCode": "",
					"StateOrProvinceCode": ""
				},
				"Charges": [{
					"Amount": "",
					"ChargeCode": "Shipping Charges"
				}],
				"CountryCode": null,
				"CurrencyCode": 154,
				"Customer": {
					"CustomerNumber": "",
					"FirstName": "",
					"LastName": "",
					"MiddleName": "",
					"RefCustomerNumber": "",
					"Email": ""
				},
				"Discounts": [],
				"Items": [],
				"OrderType": "internet",
				"OrderDate": "",
				"OrderMessage": "",
				"OrderReferenceNumber": "",
				"Payment": {
					"IsOnAccountPayment": "false",
					"RedeemablePayments": []
				},
				"ShipTos": [{
					"CarrierAccountNumber": "",
					"ExternalShipCode": "ground",
					"Recipient": {
						"FirstName": "",
						"LastName": "",
						"MiddleName": ""
					},
					"ShipLineID": 1,
					"ShippingAddress": {
						"Address1": "",
						"Address2": "",
						"City": "",
						"CountryCode": null,
						"Email": null,
						"Fax": "",
						"IsGiftAddress": false,
						"Organization": "",
						"PhoneNumber": "",
						"PostalCode": "",
						"StateOrProvinceCode": null
					}
				}],
				"Source": "",
				"SourceCode": ""
			}
		}');
		
	}

	// This needs to be removed.
	public function validate() { 
		$order = $this->order->data->Request;
		$errors = array();
		$errors[]="hi";
		if(count($errors)>0) {
			var_dump($errors);
			die();
		}
	}

	/**
	* Primary Fifth Gear Service Call
	*
	* @return object
	*/
	private function call($service, $data=array()) {
		// Init Curl
		$curl = curl_init();
		// Convert to JSON string if not already
		$data = (is_string($data)) ? $data : json_encode($data);
		
		$callURL = 'https://'.$this->config['user'].':'.urlencode($this->config['password']).'@'.$this->config['host'].$this->config['basepath'].'/'.$service;
		// Curl Options
		curl_setopt($curl, CURLOPT_URL, $callURL);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/json'));
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($curl);

		curl_close ($curl);
	
		return $this->handleResponse($response, $service, json_decode($data));
	}

	/**
	* Track the status of an order and it's current shipping status with shipping vendor tracking number
	*
	* @param string $orderId
	* @return object
	*/
	public function trackOrder($orderId) {

		$data = (object) "tracking";
		$data->CompanyId = $this->config['company'];
		$data->Request = $orderId;

		return $this->call('OrderStatusLookupByRefNumber', $data);

	}

	public function getOrderData() {
		return $this->order->data;
	}


	/***************************************
	
	itemInventoryBulkLookup

	companyId
	startRange = 0
	endRange = 100

	- availabletopromise
	- itemnumber
	- totalSKUResults ()
		
	***************************************/


	/**
	* Get the current Inventory levels for a sku in the FifthGear Warehouse
	* 
	* @param string $sku
	* @return object FifthGearResponse
	*/
	public function lookupInventory($sku) {

		$data = (object) "inventoryLookup";
		$data->CompanyId = $this->config['company'];
		$data->Request = $sku;

		return $this->call('ItemInventoryLookup', $data);

	}

	/**
	* Get the current Inventory levels for a ALL sku in the FifthGear Warehouse
	* 
	* @param integer $start number of items to start at. 
	* @param integer $end number of items to stop at (max 5000 results per request)
	* @return object FifthGearResponse
	*/
	public function lookupInventoryBulk($start=1,$end=5000) {

		$data = (object) "ItemInventoryBulkLookup";
		$data->CompanyId = $this->config['company'];
		$data->startRange=$start;
		$data->endRange = $end;

		return $this->call('ItemInventoryBulkLookup', $data);

	}


	/**
	* Get the current status of a subset of Orders
	* 
	* @param integer $start number of items to start at. 
	* @param integer $end number of items to stop at (max 5000 results per request)
	* @return object FifthGearResponse
	*/
	public function lookupOrderStatusBulk($fromDate=null, $toDate=null, $start=1,$end=100) {

		$data = (object) "OrderStatusBulkLookup";
		$data->CompanyId = $this->config['company'];
		@$data->FromDate='/Date('.(date("U",$fromDate)*1000).'-0500)/'; // Adding the @ to avoid any 5.4 timezone errors.
		@$data->ToDate = '/Date('.(date("U",$toDate)*1000).'-0500)/';
		$data->StartRange = $start;
		$data->EndRange = $end;

		return $this->call('OrderStatusBulkLookup', $data);

	}

	/**
	* Lookup an Items Detail
	*
	* @param array sku 
	* @return json
	*/

	public function getItemDetail($sku) {

		$data = (object) "ItemLookup";
		$data->CompanyId = $this->config['company'];
		$data->Request=$sku;	

		return $this->call('ItemLookup', $data);

	}

	public function getItemPersonalizationOptions($sku) {

		$data = (object) "ExportItemPersonalizationData";
		$data->CompanyId = $this->config['company'];
		$data->Request=$sku;

		return $this->call('ExportItemPersonalizationData', $data);

	}
	
	/**
	* Add a Customer to an Order
	*
	* @param array firstname,lastname,email 
	* @return bool
	*/
	public function addCustomer($params=array('firstName'=>null, 'lastName'=>null, 'email'=>null)) {
		$this->order->data->Request->Customer->FirstName =$params['firstName'];
		$this->order->data->Request->Customer->LastName =$params['lastName'];
		$this->order->data->Request->Customer->Email = $params['email'];
		
		return true;
	
	}

	/**
	* Add an Address to an Order
	*
	* @param string $type billing,shipping,both
	* @param array $params address,address2,postal,city,state,country,firstName,lastName,phone
	* @return object
	*/
	public function addAddress($type, $params=array(
		        'address'	=>null,
		        'Address2'	=> null,
		        'postal'	=> null,
		        'city'		=> null,
		        'state'		=> null,
		        'country'	=> null,
		        'firstName'	=> null,
		        'lastName'	=> null,
		        'phone'		=>null
		)) {

		$addressPack = (object) 'address-pack';
		$addressPack->address = (array_key_exists('address', $params)) ? $params['address'] : null;
		$addressPack->address2 = (array_key_exists('address2', $params)) ? $params['address2'] : null;
		$addressPack->city = (array_key_exists('city', $params)) ? $params['city'] : null;
		$addressPack->state = (array_key_exists('state', $params)) ? $params['state'] : null;
		$addressPack->country = (array_key_exists('country', $params)) ? $params['country'] : null;
		$addressPack->postal = (array_key_exists('postal', $params)) ? $params['postal'] : null;
		$addressPack->email = (array_key_exists('email', $params)) ? $params['email'] : null;
		$addressPack->phone = (array_key_exists('phone', $params)) ? $params['phone'] : null;
		$addressPack->organization = (array_key_exists('organization', $params)) ? $params['organization'] : null;
		$addressPack->firstName = (array_key_exists('firstName', $params)) ? $params['firstName'] : null;
		$addressPack->lastName = (array_key_exists('lastName', $params)) ? $params['lastName'] : null;
		$addressPack->gift = (array_key_exists('gift', $params)) ? $params['gift'] : false;

		if($type=="shipping") {
			$this->addShippingAddress($addressPack);
		} elseif($type=="billing") {
			$this->addBillingAddress($addressPack);
		} else {
			$this->addShippingAddress($addressPack);
			$this->addBillingAddress($addressPack);
		}

		return $addressPack;

	}

	/**
	* Add Shipping Address
	*
	* @param array $addressPack address,address2,city,state,country,phone,organization,email,gift,lastName,firstName 
	* @return FifthGearResponse
	*/
	private function addShippingAddress($addressPack) {
		$this->order->data->Request->ShipTos[0]->ShippingAddress->Address1 				= $addressPack->address;
		$this->order->data->Request->ShipTos[0]->ShippingAddress->Address2 				= $addressPack->address2;
		$this->order->data->Request->ShipTos[0]->ShippingAddress->City 					= $addressPack->city;
		$this->order->data->Request->ShipTos[0]->ShippingAddress->StateOrProvinceCode 	= $this->getStateCode($addressPack->state);
		$this->order->data->Request->ShipTos[0]->ShippingAddress->CountryCode 			= $this->getCountryCode($addressPack->country);
		$this->order->data->Request->ShipTos[0]->ShippingAddress->PhoneNumber 			= $addressPack->phone;
		$this->order->data->Request->ShipTos[0]->ShippingAddress->Organization 			= $addressPack->organization;
		$this->order->data->Request->ShipTos[0]->ShippingAddress->Email 				= $addressPack->email;
		$this->order->data->Request->ShipTos[0]->ShippingAddress->IsGiftAddress 		= $addressPack->gift;
		$this->order->data->Request->ShipTos[0]->ShippingAddress->PostalCode 			= $addressPack->postal;
		$this->order->data->Request->ShipTos[0]->Recipient->FirstName 					= $addressPack->firstName;
		$this->order->data->Request->ShipTos[0]->Recipient->LastName 					= $addressPack->lastName;

	}

	/**
	* Add a Billing Address
	*
	* @param array $addressPack address,address2,city,state,country,phone,organization,email,gift,lastName,firstName 
	* @return FifthGearResponse
	*/
	private function addBillingAddress($addressPack) {
		$this->order->data->Request->BillingAddress->Address1 							= $addressPack->address;
		$this->order->data->Request->BillingAddress->Address2 							= $addressPack->address2;
		$this->order->data->Request->BillingAddress->City 								= $addressPack->city;
		$this->order->data->Request->BillingAddress->StateOrProvinceCode 				= $this->getStateCode($addressPack->state);
		$this->order->data->Request->BillingAddress->CountryCode 						= $this->getCountryCode($addressPack->country);
		$this->order->data->Request->BillingAddress->PostalCode 						= $addressPack->postal;
		$this->order->data->Request->BillingAddress->PhoneNumber 						= $addressPack->phone;
		$this->order->data->Request->BillingAddress->Organization 						= $addressPack->organization;
		$this->order->data->Request->BillingAddress->Address1 							= $addressPack->address;
		$this->order->data->Request->BillingAddress->Email 								= $addressPack->email;
		$this->paymentZipcode = $addressPack->postal;
	}

	/**
	* Add an Item to an Order
	*
	* @param array $data sky, qty, amount
	* @return object FifthGearResponse
	*/
	public function addItem($data=array('sku'=>null, 'qty'=>1, 'amount'=>null)) {
		$lineNumber = count($this->order->data->Request->Items)+1;
		
		$item = (object) "item";
		$item->ShipTo 			= 1;
		$item->Amount 			= (array_key_exists('amount', $data)) ? $data['amount'] : null;
		$item->ItemNumber 		= (array_key_exists('sku', $data)) ? $data['sku'] : null;
		$item->Quantity 		= (array_key_exists('qty', $data)) ? $data['qty'] : 1;
		$item->Discounts 		= array();
		$item->ParentLineNumber = 0;
		$item->GroupName 		= null;
		$item->LineNumber 		= $lineNumber;
		$item->Comments 		= null;
		$personalizations	= (array_key_exists('personalizations', $data)) ? $data['personalizations'] : null;
		$templateNumber	= (array_key_exists('personalizationTemplateNumber', $data)) ? $data['personalizationTemplateNumber'] : null;
		
		if($personalizations!=null&&$templateNumber!=null) {
			$personalObj = (object)'Personalizations';
			$personalObj->TemplateNumber = $templateNumber; // Assign the template number to Personalization->TemplateNumber
			$personalObj->Personalization = $personalizations; // Assign personalization settings array personalization object
			$item->Personalizations = $personalObj; // assign personalization object to this item in the order
		}

		$this->order->data->Request->Items[count($this->order->data->Request->Items)]=$item; // add item to Order Data

	}

	/**
	* Add a payment to an order
	*
	* Number = Card Number
	*
	* CVV = Security number on the back of the card
	*
	* Year = four digit expiration year
	* 
	* Month = two digit expiration month
	*
	* @param array $params number,cvv,nameOnCard, month, year 
	* @return object
	*/
	public function addPayment($params=array('number'=>null, 'cvv'=>null,'nameOnCard'=>null, 'month'=>null, 'year'=>null)) {
		
		$this->paymentType = "credit";

		$number 	= (array_key_exists('number', $params)) ? $params['number'] : null;
		$cvv 		= (array_key_exists('cvv', $params)) ? $params['cvv'] : null;
		$month 		= (array_key_exists('month', $params)) ? $params['month'] : null;
		$year 		= (array_key_exists('year', $params)) ? $params['year'] : null;
		$nameOnCard = (array_key_exists('nameOnCard', $params)) ? $params['nameOnCard'] : null;

		$this->order->data->Request->Payment->CreditCardPayments = array();
		$this->order->data->Request->Payment->CreditCardPayments[0] = (object)array();
		
		$this->order->data->Request->Payment->CreditCardPayments[0]->CVV 						= $cvv;
		$this->order->data->Request->Payment->CreditCardPayments[0]->Number 					= $number;
		$this->order->data->Request->Payment->CreditCardPayments[0]->ExpirationMonth 			= $month;
		$this->order->data->Request->Payment->CreditCardPayments[0]->ExpirationYear 			= $year;
		$this->order->data->Request->Payment->CreditCardPayments[0]->HolderName 				= $nameOnCard;
		$this->order->data->Request->Payment->CreditCardPayments[0]->AddressZip 				= "";
		$this->order->data->Request->Payment->CreditCardPayments[0]->AuthorizationAmount 		= null;
		$this->order->data->Request->Payment->CreditCardPayments[0]->AuthorizationCode 			= "";
		$this->order->data->Request->Payment->CreditCardPayments[0]->IsAuthorizationAmountSpecified = true;
		$this->order->data->Request->Payment->CreditCardPayments[0]->OrderReferenceNumber 		= 100000;
		$this->order->data->Request->Payment->CreditCardPayments[0]->TransactionReferenceNumber = 100000;
		$this->order->data->Request->Payment->CreditCardPayments[0]->AuthorizationProcessor 	= $this->paymentGateway;

	}

	/**
	* Place order with Cash
	* This is used to place orders without needing a credit card.
	*
	* @return object FifthGearResponse
	*/
	public function addCashPayment($params=array('checkNumber'=>null)) {

		$this->paymentType = "cash";

		$this->order->data->Request->Payment->CashPayment = (object)array();
		$this->order->data->Request->Payment->CashPayment->Amount = 0;
		$this->order->data->Request->Payment->CashPayment->ChequeNumber	= $params['checkNumber'];
		@$this->order->data->Request->Payment->CashPayment->ChequeDate	= '/Date('.(date("U",time())*1000).'-0500)/';


	}

	/**
	* Place order with WireTransfer (can be renamed in fullfillment system - ex. 'Prepaid')
	* This is used to place orders without needing a credit card.
	*
	* @return object FifthGearResponse
	*/
	public function addWireTransferPayment() {

		$this->paymentType = "wiretransfer";
		$this->order->data->Request->Payment->WireTransferPayment = (object)array();
		$this->order->data->Request->Payment->WireTransferPayment->Amount = 0;
	}


	/**
	* Set an Shipping Charge for an Order
	*
	* @param number $amount
	* @return bool
	*/
	public function addShippingCharge($amount) {
		$this->order->data->Request->Charges[0]->Amount = $amount;
		return true;
	}

	/**
	* Set an OrderID for an Order
	*
	* @param string $id
	* @return bool
	*/
	public function setOrderId($id) {
		$this->order->data->Request->OrderReferenceNumber = $id;
		return true;
	}




	/**
	* Get the OrderId for the current Object
	*
	* @return string $OrderId
	*/
	public function getOrderId() {
		return $this->order->data->Request->OrderReferenceNumber;
	}

	/**
	* Place an Order
	*
	* This has no options, but make sure you use the addAddress, addPayment, addCustomer functions.
	*
	* @return object FifthGearResponse
	*/
	public function placeOrder() { 
		@$this->order->data->Request->OrderDate = '/Date('.(date("U",time())*1000).'-0500)/';
		if($this->order->data->Request->OrderReferenceNumber==null) {
			$this->order->data->Request->OrderReferenceNumber = 'ord-'.substr(Hash('sha1', time().rand(0,100000)),0,8);
		}

		// Calculate total Amount
		$total = 0;
		$items = $this->order->data->Request->Items; // Get each Item from the Cart

		foreach($items as $item) {
			$total = $total + $item->Amount; // Need to know if QTY is added automatically or not?
			// $total = $total + ($item->Amount*$item->Quantity);
		}

		//$this->order->data->Request->Charges[0]->Amount = $total; // Set the total

		$shippingCharge = 0;
		foreach($this->order->data->Request->Charges as $charge) {

			if($charge->ChargeCode=="Shipping Charges") {
				$shippingCharge = $shippingCharge + $shippingCharge+$charge->Amount;
			}
		}

		if($this->paymentType=="credit") {
			// If you didn't set the HolderName, we'll pull it from the billing first and last.
			if($this->order->data->Request->Payment->CreditCardPayments[0]->HolderName==null) {
				$this->order->data->Request->Payment->CreditCardPayments[0]->HolderName = $this->order->data->Request->BillingAddress->FirstName." ".$this->order->data->Request->BillingAddress->LastName;
			}
			$this->order->data->Request->Payment->CreditCardPayments[0]->AddressZip 	= $this->paymentZipcode;
			// Set authorization amount - not sure if this is needed anymore.
			$this->order->data->Request->Payment->CreditCardPayments[0]->AuthorizationAmount = $total + $shippingCharge;
		} elseif ($this->paymentType=="cash") {
			// If it's a cash payment
			// Add shipping charges to total Amount
			$this->order->data->Request->Payment->CashPayment->Amount = $total + $shippingCharge;
		} elseif ($this->paymentType=="wiretransfer") {
			// If it's a wiretransfer payment
			// Add shipping charges to total Amount
			$this->order->data->Request->Payment->WireTransferPayment->Amount = $total + $shippingCharge;
		}

		$this->order->data->Request->CountryCode = $this->order->data->Request->ShipTos[0]->ShippingAddress->CountryCode;

		//echo json_encode($this->order->data->Request);
		return $this->call('CartSubmit', $this->order->data);
	
	}
	
	/// handleResponse is the generic handler for a FifthGear response
	/// It cleans things up and makes it consistent to other SCAPI clients.
	private function handleResponse($response, $service, $data = null ) {
		
		// Lets check if it's json
		
		$responsej = json_decode($response);
		$errors = null;



		if (json_last_error() === JSON_ERROR_NONE) { 
		    //do something with $json. It's ready to use 
			
		} else { 
		    //yep, it's not JSON. Log error or alert someone or do nothing 
		    $responsej = (object) "error";
		    $responsej->message = $response;
		    $responsej->OperationRequest = null;
		} 

		$success = false;

		if($responsej==null) {
			$responsej = (object) "error";
		    $responsej->message = $response;
		    $responsej->OperationRequest = null;
		    $responsej->Response = null;
		}

		if($responsej->OperationRequest==null) {
			$responsej->OperationRequest = (object)array('Errors'=>null);
			$responsej->OperationRequest->RequestProcessingTime = null;
			$responsej->OperationRequest->Arguments = null;
		}

		if($responsej->OperationRequest->Errors == null) {
			$success = true;
		} else {
			$errors = array();
			$errs = $responsej->OperationRequest->Errors;
			foreach($errs as $err) {
				$errors[]=array('msg'=>$err->Message, 'code'=>$err->Code);
			}
		}

		$finalResponse = array(
			'success' => $success,
			'results' => $responsej->Response,
			'request' => array(
				'time'=>$responsej->OperationRequest->RequestProcessingTime,
				'args'=> $responsej->OperationRequest->Arguments,
			),
			'errors' => $errors,
			'service'=>$service
		);

		if($service=="CartSubmit") {
			$finalResponse['orderId'] = $data->Request->OrderReferenceNumber;
		}

		return $finalResponse;
	}

	/**
	* Get a FifthGear State Code
	*
	* Returns the internal FifthGear state code for a given state
	*
	* State must be provided in the two letter format: eg IN, OH, CA
	*
	* @param string 
	* @return number $code
	*/
	public function getStateCode($stateInitials) {
		$stateID = trim(strtoupper($stateInitials));
		$codes = json_decode($this->stateCodes);
		if(property_exists($codes, $stateID)) {
			return $codes->{$stateID}->code;
		} else {
			return 23;
		}
		
	}

	/**
	* Get a State Name from a State Code 
	*/
	
	public function getStateName($stateCode) {

		$codes = json_decode($this->stateCodes);

		foreach($codes as $key => $code) {

			if($code->code==$stateCode) {
				return $key;
			}
		}

		return null;
	
	}

	/**
	* Get a FifthGear Country Code
	*
	* Returns the internal FifthGear Country code for a given Country
	*
	* Country must be provided in the four letter format: eg USA, CAN, ALA
	*
	* @param string 
	* @return number $code
	*/
	public function getCountryCode($countryIntials) {
		$countryID = trim(strtoupper($countryIntials));
		$codes = json_decode($this->countryCodes);
		if(property_exists($codes, $countryID)==true) {
			return $codes->{$countryID}->code;
		} else {
			return 231;	
		}	
	}

	public $countryCodes = '{"Other":{"code":0,"name":"Other"},"AFG":{"code":1,"name":"AFGHANISTAN"},"ALA":{"code":2,"name":"AALAND ISLANDS"},"ALB":{"code":3,"name":"ALBANIA"},"DZA":{"code":4,"name":"ALGERIA"},"ASM":{"code":5,"name":"AMERICAN SAMOA"},"AND":{"code":6,"name":"ANDORRA"},"AGO":{"code":7,"name":"ANGOLA"},"AIA":{"code":8,"name":"ANGUILLA"},"ATA":{"code":9,"name":"ANTARCTICA"},"ATG":{"code":10,"name":"ANTIGUA AND BARBUDA"},"ARG":{"code":11,"name":"ARGENTINA"},"ARM":{"code":12,"name":"ARMENIA"},"ABW":{"code":13,"name":"ARUBA"},"AUS":{"code":14,"name":"AUSTRALIA"},"AUT":{"code":15,"name":"AUSTRIA"},"AZE":{"code":16,"name":"AZERBAIJAN"},"BHS":{"code":17,"name":"BAHAMAS"},"BHR":{"code":18,"name":"BAHRAIN"},"BGD":{"code":19,"name":"BANGLADESH"},"BRB":{"code":20,"name":"BARBADOS"},"BLR":{"code":21,"name":"BELARUS"},"BEL":{"code":22,"name":"BELGIUM"},"BLZ":{"code":23,"name":"BELIZE"},"BEN":{"code":24,"name":"BENIN"},"BMU":{"code":25,"name":"BERMUDA"},"BTN":{"code":26,"name":"BHUTAN"},"BOL":{"code":27,"name":"BOLIVIA"},"BIH":{"code":28,"name":"BOSNIA AND HERZEGOWINA"},"BWA":{"code":29,"name":"BOTSWANA"},"BVT":{"code":30,"name":"BOUVET ISLAND"},"BRA":{"code":31,"name":"BRAZIL"},"IOT":{"code":32,"name":"BRITISH INDIAN OCEAN TERRITORY"},"BRN":{"code":33,"name":"BRUNEI DARUSSALAM"},"BGR":{"code":34,"name":"BULGARIA"},"BFA":{"code":35,"name":"BURKINA FASO"},"BDI":{"code":36,"name":"BURUNDI"},"KHM":{"code":37,"name":"CAMBODIA"},"CMR":{"code":38,"name":"CAMEROON"},"CAN":{"code":39,"name":"CANADA"},"CPV":{"code":40,"name":"CAPE VERDE"},"CYM":{"code":41,"name":"CAYMAN ISLANDS"},"CAF":{"code":42,"name":"CENTRAL AFRICAN REPUBLIC"},"TCD":{"code":43,"name":"CHAD"},"CHL":{"code":44,"name":"CHILE"},"CHN":{"code":45,"name":"CHINA"},"CXR":{"code":46,"name":"CHRISTMAS ISLAND"},"CCK":{"code":47,"name":"COCOS (KEELING) ISLANDS"},"COL":{"code":48,"name":"COLOMBIA"},"COM":{"code":49,"name":"COMOROS"},"COG":{"code":50,"name":"CONGO, Democratic Republic of"},"COD":{"code":51,"name":"CONGO, Republic of"},"COK":{"code":52,"name":"COOK ISLANDS"},"CRI":{"code":53,"name":"COSTA RICA "},"CIV":{"code":54,"name":"COTE D\'IVOIRE"},"HRV":{"code":55,"name":"CROATIA"},"CUB":{"code":56,"name":"CUBA"},"CYP":{"code":57,"name":"CYPRUS"},"CZE":{"code":58,"name":"CZECH REPUBLIC"},"DNK":{"code":59,"name":"DENMARK"},"DJI":{"code":60,"name":"DJIBOUTI"},"DMA":{"code":61,"name":"DOMINICA"},"DOM":{"code":62,"name":"DOMINICAN REPUBLIC"},"ECU":{"code":63,"name":"ECUADOR"},"EGY":{"code":64,"name":"EGYPT"},"SLV":{"code":65,"name":"EL SALVADOR "},"GNQ":{"code":66,"name":"EQUATORIAL GUINEA"},"ERI":{"code":67,"name":"ERITREA"},"EST":{"code":68,"name":"ESTONIA"},"ETH":{"code":69,"name":"ETHIOPIA"},"FLK":{"code":70,"name":"FALKLAND ISLANDS"},"FRO":{"code":71,"name":"FAROE ISLANDS"},"FJI":{"code":72,"name":"FIJI"},"FIN":{"code":73,"name":"FINLAND"},"FRA":{"code":74,"name":"FRANCE"},"GUF":{"code":75,"name":"FRENCH GUIANA"},"PYF":{"code":76,"name":"FRENCH POLYNESIA"},"ATF":{"code":77,"name":"FRENCH SOUTHERN TERRITORIES"},"GAB":{"code":78,"name":"GABON"},"GMB":{"code":79,"name":"GAMBIA"},"GEO":{"code":80,"name":"GEORGIA"},"DEU":{"code":81,"name":"GERMANY"},"GHA":{"code":82,"name":"GHANA"},"GIB":{"code":83,"name":"GIBRALTAR"},"GRC":{"code":84,"name":"GREECE"},"GRL":{"code":85,"name":"GREENLAND"},"GRD":{"code":86,"name":"GRENADA"},"GLP":{"code":87,"name":"GUADELOUPE"},"GUM":{"code":88,"name":"GUAM"},"GTM":{"code":89,"name":"GUATEMALA"},"GIN":{"code":91,"name":"GUINEA"},"GNB":{"code":92,"name":"GUINEA-BISSAU"},"GUY":{"code":93,"name":"GUYANA"},"HTI":{"code":94,"name":"HAITI"},"HMD":{"code":95,"name":"HEARD AND MC DONALD ISLANDS"},"VAT":{"code":96,"name":"VATICAN CITY STATE"},"HND":{"code":97,"name":"HONDURAS"},"HKG":{"code":98,"name":"HONG KONG"},"HUN":{"code":99,"name":"HUNGARY"},"ISL":{"code":100,"name":"ICELAND"},"IND":{"code":101,"name":"INDIA"},"IDN":{"code":102,"name":"INDONESIA"},"IRN":{"code":103,"name":"IRAN"},"IRQ":{"code":104,"name":"IRAQ"},"IRL":{"code":105,"name":"IRELAND"},"ISR":{"code":107,"name":"ISRAEL"},"ITA":{"code":108,"name":"ITALY"},"JAM":{"code":109,"name":"JAMAICA"},"JPN":{"code":110,"name":"JAPAN"},"JEY":{"code":111,"name":"JEY"},"JOR":{"code":112,"name":"JORDAN"},"KAZ":{"code":113,"name":"KAZAKHSTAN"},"KEN":{"code":114,"name":"KENYA"},"KIR":{"code":115,"name":"KIRIBATI"},"PRK":{"code":116,"name":"KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF"},"KOR":{"code":117,"name":"KOREA, REPUBLIC OF  "},"KWT":{"code":118,"name":"KUWAIT"},"KGZ":{"code":119,"name":"KYRGYZSTAN"},"LAO":{"code":120,"name":"LAO PEOPLE\'S DEMOCRATIC REPUBLIC"},"LVA":{"code":121,"name":"LATVIA"},"LBN":{"code":122,"name":"LEBANON"},"LSO":{"code":123,"name":"LESOTHO"},"LBR":{"code":124,"name":"LIBERIA"},"LBY":{"code":125,"name":"LIBYAN ARAB JAMAHIRIYA "},"LIE":{"code":126,"name":"LIECHTENSTEIN"},"LTU":{"code":127,"name":"LITHUANIA"},"LUX":{"code":128,"name":"LUXEMBOURG"},"MAC":{"code":129,"name":"MACAU"},"MKD":{"code":130,"name":"MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF"},"MDG":{"code":131,"name":"MADAGASCAR"},"MWI":{"code":132,"name":"MALAWI"},"MYS":{"code":133,"name":"MALAYSIA"},"MDV":{"code":134,"name":"MALDIVES"},"MLI":{"code":135,"name":"MALI"},"MLT":{"code":136,"name":"MALTA"},"MHL":{"code":137,"name":"MARSHALL ISLANDS"},"MTQ":{"code":138,"name":"MARTINIQUE"},"MRT":{"code":139,"name":"MAURITANIA"},"MUS":{"code":140,"name":"MAURITIUS"},"MYT":{"code":141,"name":"MAYOTTE"},"MEX":{"code":142,"name":"MEXICO"},"FSM":{"code":143,"name":"MICRONESIA, FEDERATED STATES OF"},"MDA":{"code":144,"name":"MOLDOVA, REPUBLIC OF "},"MCO":{"code":145,"name":"MONACO"},"MNG":{"code":146,"name":"MONGOLIA"},"MNE":{"code":147,"name":"MNE"},"MSR":{"code":148,"name":"MONTSERRAT"},"MAR":{"code":149,"name":"MOROCCO"},"MOZ":{"code":150,"name":"MOZAMBIQUE"},"MMR":{"code":151,"name":"MYANMAR"},"NAM":{"code":152,"name":"NAMIBIA"},"NRU":{"code":153,"name":"NAURU"},"NPL":{"code":154,"name":"NEPAL"},"NLD":{"code":155,"name":"NETHERLANDS"},"ANT":{"code":156,"name":"NETHERLANDS ANTILLES"},"NCL":{"code":157,"name":"NEW CALEDONIA"},"NZL":{"code":158,"name":"NEW ZEALAND"},"NIC":{"code":159,"name":"NICARAGUA"},"NER":{"code":160,"name":"NIGER"},"NGA":{"code":161,"name":"NIGERIA"},"NIU":{"code":162,"name":"NIUE"},"NFK":{"code":163,"name":"NORFOLK ISLAND"},"MNP":{"code":164,"name":"NORTHERN MARIANA ISLANDS"},"NOR":{"code":165,"name":"NORWAY"},"OMN":{"code":166,"name":"OMAN"},"PAK":{"code":167,"name":"PAKISTAN"},"PLW":{"code":168,"name":"PALAU"},"PSE":{"code":169,"name":"PALESTINIAN TERRITORY, Occupied "},"PAN":{"code":170,"name":"PANAMA"},"PNG":{"code":171,"name":"PAPUA NEW GUINEA"},"PRY":{"code":172,"name":"PARAGUAY"},"PER":{"code":173,"name":"PERU"},"PHL":{"code":174,"name":"PHILIPPINES"},"PCN":{"code":175,"name":"PITCAIRN"},"POL":{"code":176,"name":"POLAND"},"PRT":{"code":177,"name":"PORTUGAL"},"PRI":{"code":178,"name":"PUERTO RICO"},"QAT":{"code":179,"name":"QATAR"},"REU":{"code":180,"name":"REUNION"},"ROM":{"code":181,"name":"ROMANIA"},"RUS":{"code":182,"name":"RUSSIAN FEDERATION"},"RWA":{"code":183,"name":"RWANDA"},"SHN":{"code":184,"name":"SAINT HELENA"},"KNA":{"code":185,"name":"SAINT KITTS AND NEVIS"},"LCA":{"code":186,"name":"SAINT LUCIA "},"SPM":{"code":187,"name":"SAINT PIERRE AND MIQUELON"},"VCT":{"code":188,"name":"SAINT VINCENT AND THE GRENADINES "},"WSM":{"code":189,"name":"SAMOA"},"SMR":{"code":190,"name":"SAN MARINO"},"STP":{"code":191,"name":"SAO TOME AND PRINCIPE"},"SAU":{"code":192,"name":"SAUDI ARABIA"},"SEN":{"code":193,"name":"SENEGAL"},"SRB":{"code":194,"name":"SERBIA AND MONTENEGRO"},"SYC":{"code":195,"name":"SEYCHELLES"},"SLE":{"code":196,"name":"SIERRA LEONE"},"SGP":{"code":197,"name":"SINGAPORE"},"SVK":{"code":198,"name":"SLOVAKIA"},"SVN":{"code":199,"name":"SLOVENIA"},"SLB":{"code":200,"name":"SOLOMON ISLANDS"},"SOM":{"code":201,"name":"SOMALIA"},"ZAF":{"code":202,"name":"SOUTH AFRICA"},"SGS":{"code":203,"name":"SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS"},"ESP":{"code":204,"name":"SPAIN"},"LKA":{"code":205,"name":"SRI LANKA"},"SDN":{"code":206,"name":"SUDAN"},"SUR":{"code":207,"name":"SURINAME"},"SJM":{"code":208,"name":"SVALBARD AND JAN MAYEN ISLANDS"},"SWZ":{"code":209,"name":"SWAZILAND"},"SWE":{"code":210,"name":"SWEDEN"},"CHE":{"code":211,"name":"SWITZERLAND"},"SYR":{"code":212,"name":"SYRIAN ARAB REPUBLIC"},"TWN":{"code":213,"name":"TAIWAN"},"TJK":{"code":214,"name":"TAJIKISTAN"},"TZA":{"code":215,"name":"TANZANIA, UNITED REPUBLIC OF"},"THA":{"code":216,"name":"THAILAND"},"TLS":{"code":217,"name":"TIMOR-LESTE"},"TGO":{"code":218,"name":"TOGO"},"TKL":{"code":219,"name":"TOKELAU"},"TON":{"code":220,"name":"TONGA"},"TTO":{"code":221,"name":"TRINIDAD AND TOBAGO"},"TUN":{"code":222,"name":"TUNISIA"},"TUR":{"code":223,"name":"TURKEY"},"TKM":{"code":224,"name":"TURKMENISTAN"},"TCA":{"code":225,"name":"TURKS AND CAICOS ISLANDS"},"TUV":{"code":226,"name":"TUVALU"},"UGA":{"code":227,"name":"UGANDA"},"UKR":{"code":228,"name":"UKRAINE"},"ARE":{"code":229,"name":"UNITED ARAB EMIRATES"},"GBR":{"code":230,"name":"UNITED KINGDOM"},"USA":{"code":231,"name":"UNITED STATES"},"UMI":{"code":232,"name":"UNITED STATES MINOR OUTLYING ISLANDS "},"URY":{"code":233,"name":"URUGUAY"},"UZB":{"code":234,"name":"UZBEKISTAN"},"VUT":{"code":235,"name":"VANUATU"},"VEN":{"code":236,"name":"VENEZUELA"},"VNM":{"code":237,"name":"VIET NAM"},"VGB":{"code":238,"name":"VIRGIN ISLANDS (BRITISH) "},"VIR":{"code":239,"name":"VIRGIN ISLANDS (U.S.) "},"WLF":{"code":240,"name":"WALLIS AND FUTUNA ISLANDS"},"ESH":{"code":241,"name":"WESTERN SAHARA"},"YEM":{"code":242,"name":"YEMEN"},"ZMB":{"code":243,"name":"ZAMBIA"},"ZWE":{"code":244,"name":"ZIMBABWE"}}';
	public $stateCodes = '{"PR": {"code": 0,"name": "Puerto Rico"},"NULL": {"code": 0,"name": "Unknown"},"AS": {"code": 1,"name": "American Samoa"},"FM": {"code": 2,"name": "Federated States of Micronesia"},"GU": {"code": 3,"name": "Guam"},"MH": {"code": 4,"name": "Marshall Islands"},"MP": {"code": 5,"name": "Northern Mariana Islands"},"PW": {"code": 6,"name": "Palau"},"VI": {"code": 7,"name": "Virgin Islands"},"UM": {"code": 8,"name": "Minor Outlying Islands"},"AL": {"code": 9,"name": "Alabama"},"AK": {"code": 10,"name": "Alaska"},"AZ": {"code": 11,"name": "Arizona"},"AR": {"code": 12,"name": "Arkansas"},"CA": {"code": 13,"name": "California"},"CO": {"code": 14,"name": "Colorado"},"CT": {"code": 15,"name": "Connecticut"},"DE": {"code": 16,"name": "Delaware"},"DC": {"code": 17,"name": "Washington DC"},"FL": {"code": 18,"name": "Florida"},"GA": {"code": 19,"name": "Georgia"},"HI": {"code": 20,"name": "Hawaii"},"ID": {"code": 21,"name": "Idaho"},"IL": {"code": 22,"name": "Illinois"},"IN": {"code": 23,"name": "Indiana"},"IA": {"code": 24,"name": "Iowa"},"KS": {"code": 25,"name": "Kansas"},"KY": {"code": 26,"name": "Kentucky"},"LA": {"code": 27,"name": "Louisiana"},"ME": {"code": 28,"name": "Maine"},"MD": {"code": 29,"name": "Maryland"},"MA": {"code": 30,"name": "Massachusetts"},"MI": {"code": 31,"name": "Michigan"},"MN": {"code": 32,"name": "Minnesota"},"MS": {"code": 33,"name": "Mississippi"},"MO": {"code": 34,"name": "Missouri"},"MT": {"code": 35,"name": "Montana"},"NE": {"code": 36,"name": "Nebraska"},"NV": {"code": 37,"name": "Nevada"},"NH": {"code": 38,"name": "New Hampshire"},"NJ": {"code": 39,"name": "New Jersey"},"NM": {"code": 40,"name": "New Mexico"},"NY": {"code": 41,"name": "New York"},"NC": {"code": 42,"name": "North Carolina"},"ND": {"code": 43,"name": "North Dakota"},"OH": {"code": 44,"name": "Ohio"},"OK": {"code": 45,"name": "Oklahoma"},"OR": {"code": 46,"name": "Oregon"},"PA": {"code": 47,"name": "Pennsylvania"},"RI": {"code": 48,"name": "Rhode Island"},"SC": {"code": 49,"name": "South Carolina"},"SD": {"code": 50,"name": "South Dakota"},"TN": {"code": 51,"name": "Tennessee"},"TX": {"code": 52,"name": "Texas"},"UT": {"code": 53,"name": "Utah"},"VT": {"code": 54,"name": "Vermont"},"VA": {"code": 55,"name": "Virginia"},"WA": {"code": 56,"name": "Washington"},"WV": {"code": 57,"name": "West Virginia"},"WI": {"code": 58,"name": "Wisconsin"},"WY": {"code": 59,"name": "Wyoming"},"AE": {"code": 60,"name": "Armed Forces Europe"},"AA": {"code": 61,"name": "Armed Forces Africa"},"AP": {"code": 62,"name": "Armed Forces Pacific"},"AB": {"code": 63,"name": "Alberta"},"BC": {"code": 64,"name": "British Columbia"},"MB": {"code": 65,"name": "Manitoba"},"NB": {"code": 66,"name": "New Brunswick"},"NL": {"code": 67,"name": "Newfoundland and Labrador"},"NT": {"code": 68,"name": "Northwest Territories"},"NS": {"code": 69,"name": "Nova Scotia"},"ON": {"code": 70,"name": "Ontario"},"PE": {"code": 71,"name": "Prince Edward Island"},"QC": {"code": 72,"name": "Quebec"},"SK": {"code": 73,"name": "Saskatchewan"},"YT": {"code": 74,"name": "Yukon"},"NU": {"code": 75,"name": "Nunavut"},"Other": {"code": 76,"name": "Other"}}';

}



?>
