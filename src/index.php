<?php


use App\Basket;
use App\Contract;
use App\Product;

$container = require __DIR__ . '/config/bootstrap.php';

function message(string $message): void {
    echo $message . PHP_EOL;
}

$period = 12;

 message("Creating $period months contract");

/** @var Contract $contract */
$contract = $container->get(Contract::class);
$contract->setPeriod($period);


message('Creating Basket with DI');


/** @var Basket $basket */
$basket = $container->get(Basket::class);


try {
    message('Adding Product P001 to Basket');
    $basket->addProduct(new Product('P001', 'Photography', 20000));
    message('Adding Product P003 to Basket');
    $basket->addProduct(new Product('P003', 'Gas Certificate', 8350));

    if ($basket->hasPromotion()) {
        message('Basket promotion has been applied!');
    }

    message('Total: £' . ($basket->getTotal() / 100));

} catch (Exception $e) {
    return $e->getMessage();
}




