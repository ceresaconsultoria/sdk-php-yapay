<?php

$payment = new \Yapay\Payment([
    'base_uri' => \Yapay\Core\YPHttp::BASE_URL_SANDBOX
]);

$urlNotification = base_url("payment-notification") . "?" . http_build_query(array(
    "storeId" => $configuration->storeId
));

//--

$reference = [
    "data1",
    "data2",
];

//--

$validation = explode("/", $body->creditCard->cardValidate);       

//--

$phone = onlyNumbers($body->order->customerPhone);
$phoneType = YPHelper::CONTATO_RESIDENCIAL;

if($body->order->customerCellphone){
    $phone = onlyNumbers($body->order->customerCellphone);
    $phoneType = YPHelper::CONTATO_CELULAR;
}

//--

$transactionProducts = [];

foreach($products as $product){
    $skuCode = $product->productId;

    if($product->variantId){
        $skuCode .= "+".$product->variantId;
    }

    $transactionProducts[] = array(
        "description" => $product->name,
        "quantity" => $product->quantity,
        "price_unit" => number_format($product->currentPrice, 2, ".", ""),
        "code" => $product->productId,
        "sku_code" => $skuCode,
        "extra" => "",
    );
}

//--

$ip = "3.137.129.131";

//--

$customer = array(
    "cpf" => onlyNumbers($body->order->customer->cpf),
    "email" => $body->order->customerEmail,
    "contacts" => array(
        array(
            "type_contact" => $phoneType,
            "number_contact" => $phone
        )
    ),
    "addresses" => array(
        array(
            "type_address" => YPHelper::ENDERECO_ENTREGA,
            "postal_code" => $body->order->shippingZipCode,
            "street" => $body->order->shippingStreet,
            "number" => $body->order->shippingNumber,
            "completion" => $body->order->shippingComplement,
            "neighborhood" => $body->order->shippingNeighborhood,
            "city" => $body->order->shippingCityName,
            "state" => $body->order->shippingStateUf,
        ),
        array(
            "type_address" => YPHelper::ENDERECO_COBRANCA,
            "postal_code" => $body->order->billingZipCode,
            "street" => $body->order->billingStreet,
            "number" => $body->order->billingNumber,
            "completion" => $body->order->billingComplement,
            "neighborhood" => $body->order->billingNeighborhood,
            "city" => $body->order->billingCityName,
            "state" => $body->order->billingStateUf,
        )
    )
);

if($body->order->customerType == 'pf'){
    $customer = array_merge($customer, [
        "name" => $body->order->customerFullName,
        "birth_date" => dateFromDB($body->order->customerBirthdate),
    ]);
}
else{
    $customer = array_merge($customer, [
        "trade_name" => $body->order->customerSocialReason,
        "company_name" => $body->order->customerSocialReason,
        "cnpj" => onlyNumbers($body->order->customerCpfCnpj),
    ]);
}  

$transaction = [
    "order_number" => $body->order->id,
    "available_payment_methods" => "2,3,4,5,6,7,14,15,16,18,19,21,22,23",
    "customer_ip" => $ip,
    "shipping_type" => $body->order->shippingMethodOptionTitle,
    "shipping_price" => number_format($body->order->shippingMethodOptionValue, 2, ".", ""),
    "price_discount" => "",
    "url_notification" => $urlNotification,
    "free" => implode("|", $reference),
];

if((float)$body->order->discounts > 0){
    $transaction['price_discount'] = number_format($body->order->discounts, 2, ".", "");
}

if((float)$body->order->additions > 0){
    $transaction['price_additional'] = number_format($body->order->additions, 2, ".", "");
}

$paymentData = array(
    "token_account" => $yapayToken,
    "reseller_token" => $resellerToken,
    "payment" => array(
        "payment_method_id" => $body->creditCard->cardFlag,
        "card_name" => $body->creditCard->cardName,
        "card_number" => onlyNumbers($body->creditCard->cardNumber),
        "card_expdate_month" => $validation[0],
        "card_expdate_year" => $validation[1],
        "card_cvv" => $body->creditCard->cardCcv,
        "split" => $body->creditCard->installment,
    ),
    "customer" => $customer,
    "transaction_product" => $transactionProducts,
    "transaction" => $transaction,
    "transaction_trace" => array(
        "estimated_date" => dateFromDB($body->order->expectedDeliveryDate)
    )
);

$paymentResponse = $payment->execute($paymentData);