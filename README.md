Fifth Gear Integration with PHP
=======================
Fifth Gear is an On-demand fulfillment solutions provider for direct retailers in the eCommerce and catalog space. **FIFTHGEAR-PHP** is a Class for interacting with our fulfillment Engine including: Order Placing, Order Tracking and Inventory lookup.

**Requirements**
- PHP 5.*
- [PHP Curl](http://php.net/manual/en/curl.installation.php)

Learn more about [Fifth Gear & our order fulfillment services](http://infifthgear.com/ "Order fulfillment").

##Inventory Lookup

With Fifth Gear's Inventory API, you can access real-time inventory data for your entire organization including warehouses, call center, and point of sale. 

**Data Available**

- Quantity available for purchase
- Availability Date of Backorders
- Item Status

**Executing an Inventory Lookup**
    
    require_once('scapi.php');
    $sc = new SigmaCommerce('companyid', 'username', 'password');

    $sku = "CT3";
    $results = $sc->lookupInventory($sku);
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

## Placing an Order 

Use the Fifth Gear Order Submit API to send new orders from any application directly in to Fifth Gear's Warehouse Management Platform.
        
    require_once('scapi.php');
    $sc = new SigmaCommerce('companyid', 'username', 'password');

    $sc->addCustomer(array(
        'firstName'=>'Brandon',
        'lastName'=>'McSmith',
        'email'=>'email@address.com'
    ));

    $sc->setOrderId('12345');

    $sc->addAddress('both', array(
        'address'=>'123456 Pine View Dr',
        'address2'=>'Suite 123',
        'postal'=>46032,
        'city'=>'Carmel',
        'state'=>'IN',
        'country'=>'USA',
        'firstName'=>'Brandon',
        'lastName'=>'McSmith'
    ));

    $sc->addItem(array(
        'amount' => 1000,
        'sku' => 'CT-103',
        'qty' => 1
    ));
    $sc->addItem(array(
        'amount' => 1000,
        'sku' => 'CT-133',
        'qty' => 4
    ));

    $sc->addPayment(array(
        'number' => '4111111111111111',
        'nameOnCard' => 'Brandon McSmith',
        'cvv' => '123',
        'month' => '03',
        'year' => '2044'
    ));

    $results = $sc->placeOrder();

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

    require_once('scapi.php');
    $sc = new SigmaCommerce('companyid', 'username', 'password');

    $orderid = "12345";
    $results = $sc->trackOrder($orderid);
    echo json_encode($results);


