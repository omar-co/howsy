<?php

namespace Services;

use App\Basket;
use App\Contract;
use Promotions\StrategyFixedDiscountByContract;

class PromotionService {

    const CONTRACT_AGREEMENT_TO_DISCOUNT = 12;

    /**
     * @param Contract $contract
     * @param Basket $basket
     * @return void
     */
    public function applyClientPromotion(Contract $contract, Basket $basket): void {
        if ($contract->getPeriod() === self::CONTRACT_AGREEMENT_TO_DISCOUNT) {
            $basket->setPromotion(new StrategyFixedDiscountByContract());
        }
    }

}