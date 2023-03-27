<?php 

//THis webhook is for tutorial 
        include('database/mydb.php');
        require_once '../stripe/stripe-php-master/init.php';

// Set your secret key. Remember to switch to your live secret key in production!
// See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey('sk_live_lfGZqKMMcq7FCeVWPLaGipsE00p9LzsWkx');

// If you are testing your webhook locally with the Stripe CLI you
// can find the endpoint's secret by running `stripe listen`
// Otherwise, find your endpoint's secret in your webhook settings in the Developer Dashboard
$endpoint_secret = 'whsec_A5jqlDObAV4sIjC9xOUkMFJgNYbO2LwB';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  // Invalid signature
  http_response_code(400);
  exit();
}

$invoice = \Stripe\Invoice::retrieve($event->data->object->invoice);
$product_description = $invoice->lines->data[0]->description;

//"Nome prodotto da aggiornare"
//+ mail ad amministrazione

$id = $event->data->object->id;
$amount = $event->data->object->amount;
$amount_dec = $amount / 100 ;
$amount_def = $amount_dec / 1.22;
$currency = $event->data->object->currency;
//$cus_email = $event->data->object->receipt_email;
//$billing_phone = $event->data->object->phone;
//$iva = $event->data->object->customer_tax_ids->type;
$description = $event->data->object->description;
$nome_prodotto = "";
$customer = $event->data->object->customer; 

//$ch = $stripe->customers->allTaxIds(
//  $customer,
//  ['limit' => 3]
//);

\Stripe\Stripe::setApiKey('sk_live_lfGZqKMMcq7FCeVWPLaGipsE00p9LzsWkx');

  


try {
 $ch = \Stripe\Customer::allTaxIds(
 $customer,
 ['limit' => 3]
);
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  exit();
}


//$taxiva = $ch->data[3];
$type = $ch->object;
$iva = $ch->data[0]->value;
$iva = substr($iva, 2);

\Stripe\Stripe::setApiKey('sk_live_lfGZqKMMcq7FCeVWPLaGipsE00p9LzsWkx');


 $cust = \Stripe\Customer::retrieve(
 $customer,
 []
);



//$taxiva = $ch->data[3];
$name = $cust->name;
$email = $cust->email;
$metadata = $cust->metadata->sdi;
$address = $cust->address->line1;
$cap = $cust->address->postal_code;
$state = $cust->address->state;
$city = $cust->address->city;
$country = $cust->address->country;


//$prod = \Stripe\products::retrieve(
 //$customer,
 //[]
//);

//$iva = $ch->data[0]->value;

//$iva = $a["name"];

$date = date("Y-m-d");

echo $invoice;
echo $event->data->object;

//$foo = 5;
    //$bar = $foo++;

// Handle the event
echo 'Received: ' . $product_description .'-'. $amount_dec .'-';

    

http_response_code(200);



// NOTE: this is a complete request, but please customize it before trying to send it!

// In this example we are using our PHP SDK
// https://packagist.org/packages/fattureincloud/fattureincloud-php-sdk

use FattureInCloud\Model\Currency;
use FattureInCloud\Model\DocumentTemplate;
use FattureInCloud\Model\Entity;
use FattureInCloud\Model\IssuedDocument;
use FattureInCloud\Model\IssuedDocumentItemsListItem;
use FattureInCloud\Model\IssuedDocumentPaymentsListItem;
use FattureInCloud\Model\IssuedDocumentEiData;
use FattureInCloud\Model\IssuedDocumentStatus;
use FattureInCloud\Model\IssuedDocumentType;
use FattureInCloud\Model\CreateIssuedDocumentRequest;
use FattureInCloud\Model\Language;
use FattureInCloud\Model\PaymentAccount;
use FattureInCloud\Model\PaymentMethod;
use FattureInCloud\Model\VatType;

require_once('fattureincloud-php-sdk.phar');

//set your access token
$config = FattureInCloud\Configuration::getDefaultConfiguration()->setAccessToken('a/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJyZWYiOiJ1V2VyMmVYcGs5QklhcmRWNlk1and1OTdOQlJ2Yk9nUCJ9.yL6pNBN1kPKN_ZS7wgP1W2eXuLBMkiWoPtYk1_tsI9c');

$apiInstance = new FattureInCloud\Api\IssuedDocumentsApi(
    new GuzzleHttp\Client(),
    $config
);

//set your company id
$company_id = 593415;
    
$entity = new Entity;
$entity
    ->setId("65543277")
    ->setName($name)
    ->setVatNumber($iva)
    ->setTaxCode($iva)
    ->setAddressStreet($address)
    ->setAddressPostalCode($cap)
    ->setAddressCity($city)
    ->setAddressProvince($state)
    ->setCountry("Italia")
    ->setEiCode($metadata);

$invoice = new IssuedDocument;
$invoice->setType(IssuedDocumentType::INVOICE);
$invoice->setEntity($entity);
$invoice->setDate(new DateTime($date));
//$invoice->setNumber("");
$invoice->setNumeration("");

$invoice->setSubject($description);
$invoice->setEInvoice(true);
$invoice->setShowPaymentMethod(true);
$invoice->setEiData(
new IssuedDocumentEiData(
array(
"payment_method" => "MP19"
)
)
);

$invoice->setVisibleSubject($description);
$invoice->setCurrency(
    new Currency(
        array(
           "id" => "EUR" 
        )
    )
);
$invoice->setLanguage(
    new Language(
        array(
            "code" => "it",
            "name" => "italiano"
        )
    )
);
// Here we set e_invoice and ei_data

$invoice->setItemsList(
    array(
        new IssuedDocumentItemsListItem(
            array(
                "product_id" => 27103813,
                "code" => "",
                "name" => $product_description,
                "net_price" => $amount_def,
                "category" => $product_description,
                "discount" => 0,
                "qty" => 1,
                "vat" => new VatType(
                    array(
                        "id" => 0
                    )
                )
            )
        )
    )
);
$invoice->setPaymentMethod(
    new PaymentMethod(
        array(
            "id" => 1737602
        )
    )
);
$invoice->setPaymentsList(
    array(
        new IssuedDocumentPaymentsListItem(
            array(
                "amount" => $amount_dec,
                "due_date" => new DateTime($date),
                "paid_date" => new DateTime($date),
                "status" => IssuedDocumentStatus::NOT_PAID,
                "payment_account" => new PaymentAccount(
                    array(
                        "id" => 624223
                    )
                )
            )
        )
    )
);
//$invoice->setAttachmentToken("YmMyNWYxYzIwMTU3N2Y4ZGE3ZjZiMzg5OWY0ODNkZDQveXl5LmRvYw");
//$invoice->setTemplate(
//    new DocumentTemplate(
//        array(
//            "id" => 2821
 //       )
 //   )
//);

// Here we put our invoice in the request object
$create_issued_document_request = new CreateIssuedDocumentRequest;
$create_issued_document_request->setData($invoice);

// Now we are all set for the final call
// Create the invoice: https://github.com/fattureincloud/fattureincloud-php-sdk/blob/master/docs/Api/IssuedDocumentsApi.md#createissueddocument
try {
    $result = $apiInstance->createIssuedDocument($company_id, $create_issued_document_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IssuedDocumentsApi->createIssuedDocument: ', $e->getMessage(), PHP_EOL;
}



?>