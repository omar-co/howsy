<?php

namespace App;

use Exception;
use Exceptions\DuplicationException;
use Promotions\StrategyPromotionInterface;
use Services\PromotionService;

class Basket {

    /**
     * @var array
     */
    private array $products = [];

    /**
     * @var int
     */
    private int $grossTotal = 0;

    /**
     * @var StrategyPromotionInterface|null
     */
    private ?StrategyPromotionInterface $promotion;

    /**
     * @param User $user
     * @param PromotionService $promotionService
     */
    public function __construct(
        private readonly User $user,
        private readonly PromotionService $promotionService
    ) {
        $this->applyClientPromotion();
    }

    /**
     * @param StrategyPromotionInterface $strategyPromotion
     * @return $this
     */
    public function setPromotion(StrategyPromotionInterface $strategyPromotion): static {
        $this->promotion = $strategyPromotion;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @throws Exception
     */
    public function addProduct(Product $product): string|static {
        $this->isProductDuplicate($product);
        $this->products[$product->getCode()] = $product;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPromotion(): bool {
        return isset($this->promotion);
    }


    /**
     * @return int
     */
    public function getTotal(): int {
        $discount = 0;
        if ($this->hasPromotion()) {
            $discount = $this->promotion->discount($this->getGrossTotal());
        }

        return ($this->getGrossTotal() - $discount);

    }

    /**
     * @return array
     */
    public function getProducts(): array {
        return $this->products;
    }

    /**
     * @return void
     */
    public function applyClientPromotion(): void {
        $this->promotionService->applyClientPromotion($this->getUser()->getContract(), $this);
    }

    /**
     * @throws Exception
     */
    private function isProductDuplicate(Product $product): void {

        if (array_key_exists($product->getCode(), $this->products)) {
            throw new DuplicationException('Product already exist');
        }
    }

    /**
     * @return int
     */
    private function getGrossTotal(): int {

        if (!$this->grossTotal) {
            /** @var Product $product */
            foreach ($this->products as $product) {
                $this->grossTotal += $product->getPrice();
            }
        }

        return $this->grossTotal;
    }

}