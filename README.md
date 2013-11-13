Fifth Gear for PHP
=======================
Fifth Gear is an end-to-end ecommerce fulfillment provider. Learn more about [Fifth Gear & our order fulfillment services](http://infifthgear.com/ "fulfillment services"). 

**FIFTHGEAR-PHP** is a php class for interacting with our fulfillment engine. Currently  supports: Order Placing, Order Tracking and Inventory lookup.

**Requirements**

- PHP 5.*
- [PHP Curl](http://php.net/manual/en/curl.installation.php)




## Instantiating the Fifth Gear Object

To create a new connection to Fifth Gear you'll need to provide your companyid, username, password, and development mode. 

**The options for development mode are: **

- **dev** for interacting with your Fifth Gear Test Server
- **prod** for interacting with your Fifth Gear Production server.
 
    
    $fg = new FifthGear('companyid', 'username', 'password', 'dev'); 

If you're unsure of any of these values, please contact your Customer Care Rep.

##Item Lookup

Look up the details of an Item, including it's meta data and personalization templates.
  
    $fg = new FifthGear('companyid', 'username', 'password', 'dev');
    $results = $fg->itemLookup('My-SKU-1234');
    echo json_encode($results);

**Successful Response**

    {
      "success": true,
      "results": {
        "AdditionalSalesPriceText": null,
        "Attributes": null,
        "AvailableToPurchaseQuantity": 0,
        "BasePromotionPrice": 89.95,
        "BaseRetailPrice": 89.95,
        "BaseShopperPromotionPrice": 0,
        "Category": "Super Product",
        "DefaultPricePercentage": 0,
        "DefaultQuantity": 0,
        "Description": "This is a great product! Seriously, buy it.",
        "EMailIDForEGC": null,
        "FormattedDescription": null,
        "GiftCardMessage": null,
        "GiftCardRecipientName": null,
        "GiftCardSendertName": null,
        "GiftCertificateAmount": 0,
        "GiftItemID": null,
        "IsBasePromotionPriceSet": false,
        "IsConfigKit": false,
        "IsDoNotPurchaseActive": false,
        "IsDoNotSellActive": false,
        "IsDropShipItem": false,
        "IsFixedGiftCertificate": false,
        "IsKitPriceDependentOnComponent": false,
        "IsMandatoryKitComponent": false,
        "IsMustForShipping": false,
        "IsPersonalizable": 0,
        "IsPredefinedKit": false,
        "IsSOI": false,
        "IsSelectedKitComponent": false,
        "IsSeries": false,
        "IsStaticKit": false,
        "ItemAttribute": null,
        "ItemNumberAlias": null,
        "KitGroupID": null,
        "KitGroups": [],
        "KitPriceDifference": 0,
        "ModelAttributeValues": [
          {
            "Name": "{4D0355F0-0F93-4D4F-B1FD-AC01186B7D6B}",
            "Value": [
              {
                "Name": "{B5C7C94C-D334-4C66-8137-F4474DA328FE}",
                "Value": "Left Front"
              },
              {
                "Name": "{580B979D-FC3F-4DF5-AD7A-7A1994E93BB4}",
                "Value": "Left Rear"
              },
              {
                "Name": "{C3FEC492-F78D-438E-B316-E316429EAEE9}",
                "Value": "Right Front"
              },
              {
                "Name": "{7C87FAF8-8B58-4392-AE4D-4786A18EE197}",
                "Value": "Right Rear"
              }
            ]
          }
        ],
        "ModelAttributes": [
          {
            "Name": "{4D0355F0-0F93-4D4F-B1FD-AC01186B7D6B}",
            "Value": "Side/Front Or Rear"
          }
        ],
        "ModelItemNumber": "My-SKU-1234",
        "Number": "My-SKU-1234",
        "PersonalizationValues": null,
        "PersonalizedText": null,
        "PromotionPrices": [
          {
            "Name": null,
            "Value": "89.950000"
          }
        ],
        "RelatedItems": null,
        "ReturnPolicy": null,
        "SalesName": "My Awesome Product",
        "SelectedAttributes": [],
        "SelectedQuantity": 0,
        "SelectedUOMData": null,
        "SeriesItemAttributes": null,
        "Status": "Active",
        "TaglineDescription": null,
        "TemplateNumber": null,
        "TemplateVersion": 0,
        "TotalAvailableQuantity": 0,
        "Type": "Physical Item",
        "UOMDetails": null,
        "UPCValue": null
      },
      "request": {
        "time": null,
        "args": null
      },
      "errors": null,
      "service": "ItemLookup"
    }


## Getting an Items Personalization Options
At Fifth Gear we pride ourselves on our ability to personalize almost anything. Here's the instructions for finding an Items personalization options. 

    $fg = new FifthGear('companyid', 'username', 'password', 'dev');

    $fg->getItemPersonalizationOptions('My-SKU-1234');
    echo json_encode($results);

**Successful Response**

    {
      "success": true,
      "results": {
        "ExportPersonalization": [
          {
            "Is_Default": true,
            "Is_Response_Required": true,
            "Item_ID": "76c59887-b679-412a-92c2-fae59a0dd578",
            "Item_Number": "WP-136",
            "List_ID": "c19418be-28cc-41e8-a08b-b3053d3cd1ec",
            "Response_Size": "0",
            "Response_Type": "List",
            "Sort_Order": 1,
            "TemplateLineListName": "Year 71-72",
            "TemplateLineListNumber": "PLN-41",
            "TemplateLineListValues": "1971",
            "TemplateLineNumber": "1",
            "TemplateName": "Name to Print",
            "TemplateNumber": "62",
            "TemplatePrompt": "Your Full Name",
            "TemplateSequence": "1"
          },
          {
            "Is_Default": true,
            "Is_Response_Required": true,
            "Item_ID": "76c59887-b679-412a-92c2-fae59a0dd578",
            "Item_Number": "WP-136",
            "List_ID": null,
            "Response_Size": "10",
            "Response_Type": "String",
            "Sort_Order": 1,
            "TemplateLineListName": null,
            "TemplateLineListNumber": null,
            "TemplateLineListValues": null,
            "TemplateLineNumber": "3",
            "TemplateName": "Birth City",
            "TemplateNumber": "62",
            "TemplatePrompt": "Your Birth City",
            "TemplateSequence": "2"
          }
        ]
      },
      "request": {
        "time": null,
        "args": null
      },
      "errors": null,
      "service": "ExportItemPersonalizationData"
    }

##Single SKU Inventory Lookup

With Fifth Gear's Inventory API you can access real-time inventory data for your entire organization including warehouses, call center, and point of sale. 

**Data Available**

- Quantity available for purchase
- Availability Date of Backorders
- Item Status

**Executing an Inventory Lookup**
    
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
    echo json_encode($results);


## Placing an Order without a Credit Card (Cash) 

You might need the ability to place an order while not collecting a users credit card details. To do this, you'll need to use the "addCashPayment" method.
        
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
       'lastName'=>'McSmith',
       'email'=>'brandon@email.com'
    ));

    $fg->addItem(array(
        'amount' => 1000,
        'sku' => 'CT-103',
        'qty' => 1
    ));


    /// Adding an Item with Personalization data populated
    /// 
    
    $fg->addItem(array(
      'amount'=> 900,
      'sku'   => 'WP-136',
      'qty'   => 1,
      'personalizationTemplateNumber'=>'62', // Must be a string
      'personalizations'=>array(
        array( 'Number'=>1, 'Response'=>'1971'),
        array( 'Number'=>2, 'Response'=>'VIN123'),
        array( 'Number'=>3, 'Response'=>'1234-motor-date-code')
      )
    ));

    // Add a Shipping Charge
    $fg->addShippingCharge(12);

    // Add a Cash Payment (Any CheckNumber is accepted)
    $fg->addCashPayment(array(
        'checkNumber'=>1001
    ));

    echo json_encode($fg->placeOrder());

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

- States provided must be in the 2 digit alpha format. e.g. IN, OH, CA, MA
- Counties provided must be in the 3 digit alpha format. e.g. USA, CAN
 - A collection of all state codes can be found with the object **sc.stateCodes**
- Country provided must be in their 3 Digit Format. e.g. USA, CAN, ALA
- A collection of all country codes can be found with the object **sc.countryCodes**

## Track Order

Track the status of an order, retrieve tracking information and more.

    require_once('fifthgear.php');
    $fg = new FifthGear('companyid', 'username', 'password', 'dev');

    $orderid = "12345";
    $results = $fg->trackOrder($orderid);
    echo json_encode($results);


##Other Helpful Methods##

**Get Order Data** - get the complete Order Object that's passed to Fifth Gear's API. This is helpful for debugging.
    
    $fg->getOrderData();

**Get and Set the Order ID**

    $fg->setOrderId('MyNewOrderId');
    $fg->getOrderId(); // Returns the current Order Id

##Details on how the Submit Order works##

This PHP class is used to simplify the communication between your PHP application and Fifth Gear's Restful API's. You can always communicate directly with Fifth Gear's API without this PHP class, we recommend you take a look at the _call() function in fifthgear.php to learn more. If you read the code in the FifthGear.php you will see the entire JSON string for an Order. This is a stub for the order object that's passed to Fifth Gear, and can always be accessed at:

    echo json_encode($fg->order->data);

This is not a protected variable so you can change anything just by doing **$fg->order->data->Request->BillingAddress->City = "Carmel";**.  

When $fg->placeOrder() is called, the Fifth Gear class will add any additional data to the order object, and pass it to Fifth Gear's Rest based API.