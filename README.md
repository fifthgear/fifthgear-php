Fifth Gear Integration with PHP
=======================
Fifth Gear is an On-demand fulfillment solutions provider for direct retailers in the eCommerce and catalog space. **FIFTHGEAR-PHP** is a php class for interacting with our fulfillment engine. Currently  supports: Order Placing, Order Tracking and Inventory lookup.

**Requirements**

- PHP 5.*
- [PHP Curl](http://php.net/manual/en/curl.installation.php)

Learn more about [Fifth Gear & our order fulfillment services](http://infifthgear.com/ "Order fulfillment").


## Fifth Gear Object

To create a new connection to Fifth Gear you'll need to provide your companyid, username, password, and development mode. 

**The options for development mode are: **

- **dev** for interacting with your Fifth Gear Test Server
- **prod** for interacting with your Fifth Gear Production server.
 
    
    $fg = new FifthGear('companyid', 'username', 'password', 'dev'); 


##Single SKU Inventory Lookup

With Fifth Gear's Inventory API you can access real-time inventory data for your entire organization including warehouses, call center, and point of sale. 

**Data Available**

- Quantity available for purchase
- Availability Date of Backorders
- Item Status

**Executing an Inventory Lookup**
    
    require_once('fifthgear.php');
    $fg = new FifthGear('companyid', 'username', 'password', 'dev');

    $sku = "CT3";
    $results = $fg->lookupInventory($sku);
    echo json_encode($results);

**Successful Response**

    {
        success: true
        results: {
            AvailableToPurchaseQuantity: 15
            BackOrderAvailableDate: "05/02/2013"
            ItemNumber: "CT-361"
            Status: 0
        },
        errors: null
        service: "ItemInventoryLookup"
    }

**Note:** SKU's that are not present in Fifth Gear will throw an error. We're unable to provide any inventory data on a product that's not in Fifth Gear.


##Bulk SKUs Inventory Lookup

With Fifth Gear's Bulk Inventory API you can access real-time inventory data for your entire organization including warehouses, call center, and point of sale. This service will return a maximum of 5000 records per request. **You will be responsible for passing the correct starting index and end index.** 

**Data Available**

- Quantity available for purchase
- Total SKUS
- ItemNumber


**Executing a Bulk Inventory Lookup**
    
    require_once('fifthgear.php');
    $fg = new FifthGear('companyid', 'username', 'password', 'dev');

    $results = $fg->lookupInventoryBulk(1,3);
    echo json_encode($results);

**Successful Response**


    {
      "success": true,
      "results": {
        "ItemInventories": [
          {
            "AvailableToPurchaseQuantity": 162,
            "ExternalItemNumber": null,
            "ItemNumber": "10001"
          },
          {
            "AvailableToPurchaseQuantity": 0,
            "ExternalItemNumber": null,
            "ItemNumber": "10002"
          },
          {
            "AvailableToPurchaseQuantity": 0,
            "ExternalItemNumber": null,
            "ItemNumber": "10003"
          }
        ],
        "TotalSKUResults": 35105
      },
      "request": {
        "time": 3601,
        "args": null
      },
      "errors": null,
      "service": "ItemInventoryBulkLookup"
    }


## Placing an Order with a Credit Card 

Submit an Credit Card order directly to Fifth Gear's Fulfillment Platform. **NOTE**: If you're collecting users credit card data you ABSOLUTELY required to have a fully tested SSL setup on your server. Fifth Gear will not allow any clients to leverage credit card payment's without proper SSL. 
        
    require_once('fifthgear.php');
    $fg = new FifthGear('companyid', 'username', 'password', 'dev');

    $fg->addCustomer(array(
        'firstName'=>'Brandon',
        'lastName'=>'McSmith',
        'email'=>'email@address.com'
    ));

    $fg->setOrderId('12345');

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

    // Add a Shipping Charge
    $fg->addShippingCharge(12);

    $fg->addPayment(array(
        'number' => '4111111111111111',
        'nameOnCard' => 'Brandon McSmith',
        'cvv' => '123',
        'month' => '03',
        'year' => '2044'
    ));

    $results = $fg->placeOrder();


## Placing an Order without a Credit Card (Cash) 

You might need the ability to place an order while not collecting a users credit card details. To do this, you'll need to use the "addCashPayment" method.
        
    require_once('fifthgear.php');
    $fg = new FifthGear('companyid', 'username', 'password', 'dev');

    $fg->addCustomer(array(
        'firstName'=>'Brandon',
        'lastName'=>'McSmith',
        'email'=>'email@address.com'
    ));

    $fg->setOrderId('12345');

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

    // Add a Shipping Charge
    $fg->addShippingCharge(12);

    // Add a Cash Payment (Any CheckNumber is accepted)
    $fg->addCashPayment(array(
        'checkNumber'=>1001
    ));

    $results = $fg->placeOrder();

 **Cart Submit Response**

    {
      "success": true,
      "results": {
        "OrderReceipt": "4d341e8b-f425-499a-a9a4-79f800d8c0d7",
        "OrderStatus": "NotYetShipped",
        "OrderReferenceNumber": "ord-7HG9IB32F",
        "OrderStatusMessage": "Not Yet Shipped"
      },
      "errors": null,
      "service": "CartSubmit"
    }

**NOTES for Order Placing**

- States provided must be in the 2 Digit Format. e.g. IN, OH, CA, MA
 - A collection of all state codes can be found with the object **sc.stateCodes**
- Country provided must be in their 3 Digit Format. e.g. USA, CAN, ALA
- A collection of all country codes can be found with the object **sc.countryCodes**
- sc.stateCodes and sc.countryCodes can be 

## Track Order

Track the status of an order, retrieve tracking information and more.

    require_once('fifthgear.php');
    $fg = new FifthGear('companyid', 'username', 'password', 'dev');

    $orderid = "12345";
    $results = $fg->trackOrder($orderid);
    echo json_encode($results);


