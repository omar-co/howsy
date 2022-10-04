<?php

namespace App;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Exceptions\DuplicationException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Services\PromotionService;

class BasketTest extends TestCase {

    private Basket $basket;

    private mixed $container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function setUp(): void {
        $this->container = new Container();
        $this->basket = $this->container->make(Basket::class);
    }

    /**
     * @return void
     */
    public function testDependencyInjectionUserInBasket(): void {

        $this->assertInstanceOf(User::class, $this->basket->getUser());
    }

    /**
     * @return void
     */
    public function testDependencyInjectionContractInUserInBasket(): void {

        $this->assertInstanceOf(Contract::class, $this->basket->getUser()->getContract());
    }

    /**
     * @dataProvider providePhotographyProduct
     * @param $product
     * @return Basket
     * @throws Exception
     */
    public function testAddOneProduct($product): Basket {
        $this->basket->addProduct($product);

        $this->assertCount(1, $this->basket->getProducts());

        return $this->basket;
    }

    /**
     * @dataProvider provideMultipleProductsLastDuplicated
     * @param array $products contains the products
     * @param int $productsInBasket number of products expected in basket
     * @param bool $expectsException if duplicate products exists then this need to be true
     * @return void
     * @throws Exception
     */
    public function testAddMultipleProducts(array $products, int $productsInBasket, bool $expectsException): void {

        if ($expectsException) {
            $this->expectException(DuplicationException::class);
        }

        foreach ($products as $product) {
            $this->basket->addProduct($product);
        }

        $this->assertCount($productsInBasket, $this->basket->getProducts());
    }

    /**
     * @return void
     */
    public function testBasketWithOutPromotionAssigned(): void {

        $this->basket->getUser()->getContract()->setPeriod(6);

        $this->assertFalse($this->basket->hasPromotion());

    }

    /**
     * @return void
     */
    public function testBasketWithPromotionAssignedInRunTime(): void {

        $this->basket->getUser()->getContract()->setPeriod(12);

        $this->basket->applyClientPromotion();

        $this->assertTrue($this->basket->hasPromotion());

    }

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBasketWithPromotionAssignedInBasketInitialize(): void {

        $contract = $this->container->get(Contract::class);

        $contract->setPeriod(12);

        $basket = $this->container->get(Basket::class);

        $this->assertTrue($basket->hasPromotion());

    }

    /**
     * @return void
     */
    public function testBasketWithPromotionAssignedInBasketInitializeWithMocks(): void {

        $contract = Mockery::Mock(Contract::class);
        $contract->shouldReceive('setPeriod')
            ->with(12)
            ->andReturn($contract)
            ->once();
        $contract->shouldReceive('getPeriod')
            ->andReturn(12)
            ->once();


        $user = Mockery::Mock(User::class, [$contract]);
        $user->shouldReceive('getContract')
            ->andReturn($contract)
            ->once();

        $promotionService = new PromotionService();

        $basket = new Basket($user, $promotionService);


        $this->assertTrue($basket->hasPromotion());


    }


    /**
     * @dataProvider providePhotographyProduct
     * @param $product
     * @return void
     * @throws Exception
     */
    public function testCalculateTotalWithoutPromotion($product): void {

        $this->basket->getUser()->getContract()->setPeriod(12);
        $this->basket->applyClientPromotion();
        $this->basket->addProduct($product);

        $this->assertIsInt($this->basket->getTotal());
        $this->assertEquals(18000, $this->basket->getTotal());
    }


    /**
     * @dataProvider provideMultipleProductsLastDuplicated
     * @param array $products
     * @param int $productsInBasket
     * @param bool $expectsException
     * @param int $grossTotal
     * @return void
     * @throws Exception
     */
    public function testCalculateTotalWithoutPromotionAndDuplicatedProduct(array $products, int $productsInBasket, bool $expectsException, int $grossTotal): void {

        if ($expectsException) {
            $this->expectException(DuplicationException::class);
        }

        try {
            $this->basket->getUser()->getContract()->setPeriod(6);
            $this->basket->applyClientPromotion();


            foreach ($products as $product) {
                $this->basket->addProduct($product);
            }
        } finally {
            $this->assertIsInt($this->basket->getTotal());
            $this->assertEquals($grossTotal, $this->basket->getTotal());
        }
    }


    /**
     * @dataProvider providePhotographyProduct
     * @param $product
     * @return void
     * @throws Exception
     */
    public function testCalculateTotalWithPromotion($product): void {

        $this->basket->getUser()->getContract()->setPeriod(6);
        $this->basket->applyClientPromotion();
        $this->basket->addProduct($product);

        $this->assertIsInt($this->basket->getTotal());
        $this->assertEquals(20000, $this->basket->getTotal());
    }


    /**
     * @dataProvider provideMultipleProductsLastDuplicated
     * @param array $products
     * @param int $productsInBasket
     * @param bool $expectsException
     * @param int $grossTotal
     * @param int $total
     * @return void
     * @throws Exception
     */
    public function testCalculateTotalWithPromotionAndDuplicatedProduct(array $products, int $productsInBasket, bool $expectsException, int $grossTotal, int $total): void {

        if ($expectsException) {
            $this->expectException(DuplicationException::class);
        }

        try {
            $this->basket->getUser()->getContract()->setPeriod(12);
            $this->basket->applyClientPromotion();


            foreach ($products as $product) {
                $this->basket->addProduct($product);
            }
        } finally {
            $this->assertIsInt($this->basket->getTotal());
            $this->assertEquals($total, $this->basket->getTotal());
        }
    }


    public function providePhotographyProduct(): array {
        return [
            'Photography' => [
                new Product('P001', 'Photography', 20000)
            ]
        ];
    }

    public function provideMultipleProductsLastDuplicated(): array {
        return [
            '1 Product' => [
                [new Product('P001', 'Photograpy', 20000)],
                1,
                false,
                20000,
                18000
            ],
            '2 Products' => [
                [
                    new Product('P001', 'Photograpy', 20000),
                    new Product('P002', 'Floorplan', 10000),
                ],
                2,
                false,
                30000,
                27000
            ],
            '3 Products' => [
                [
                    new Product('P001', 'Photograpy', 20000),
                    new Product('P002', 'Floorplan', 10000),
                    new Product('P003', 'Gas Certificate', 8350),
                ],
                3,
                false,
                38350,
                34515

            ],
            '4 Products' => [
                [
                    new Product('P001', 'Photograpy', 20000),
                    new Product('P002', 'Floorplan', 10000),
                    new Product('P003', 'Gas Certificate', 8350),
                    new Product('P004', 'EICR Certificate', 5100),
                ],
                4,
                false,
                43450,
                39105
            ],
            '5 Products (one duplicated)' => [
                [
                    new Product('P001', 'Photograpy', 20000),
                    new Product('P002', 'Floorplan', 10000),
                    new Product('P003', 'Gas Certificate', 8350),
                    new Product('P004', 'EICR Certificate', 5100),
                    new Product('P004', 'EICR Certificate', 5100),
                ],
                4,
                true,
                43450,
                39105
            ],
        ];
    }


}
