<?php

require(__DIR__ . '/../setup.php');

$app = new FrameworkX\App();

$baseUri = '/bookstore';

$modelControllers = [
    '/address'          => Blrf\Bookstore\Controller\Address::class,
    '/addressStatus'    => Blrf\Bookstore\Controller\AddressStatus::class,
    '/book'             => Blrf\Bookstore\Controller\Book::class,
    '/bookLanguage'     => Blrf\Bookstore\Controller\BookLanguage::class,
    '/country'          => Blrf\Bookstore\Controller\Country::class,
    '/customer'         => Blrf\Bookstore\Controller\Customer::class,
    '/publisher'        => Blrf\Bookstore\Controller\Publisher::class,
    '/shippingMethod'   => Blrf\Bookstore\Controller\ShippingMethod::class
];

$app->get($baseUri, Blrf\Bookstore\Controller\Index::class);

foreach ($modelControllers as $path => $ctrl) {
    $app->get($baseUri . $path . '/{opt:metadata}', $ctrl);
    // get model by pk (with optional related)
    $app->get($baseUri  . $path . '/{id:\d+}[/{opt:related}]', $ctrl);
    // create model
    $app->put($baseUri . $path, $ctrl);
    // delete model
    $app->delete($baseUri . $path . '/{id}', $ctrl);
    // search models
    $app->post($baseUri . $path . '[/{opt:stream}]', $ctrl);
}

/**
 * Special case for customer addresses.
 *
 * Customer has many addresses and CustomerAddress is further linked to Address with AddressStatus
 * field.
 */
// get model by Pk
$app->get(
    $baseUri . '/customer/{cid:\d+}/address/{aid:\d+}[/{opt:related}]',
    Blrf\Bookstore\Controller\CustomerAddress::class
);
// search addreses
$app->post($baseUri . '/customer/{cid}/address', Blrf\Bookstore\Controller\CustomerAddress::class);

$app->run();
