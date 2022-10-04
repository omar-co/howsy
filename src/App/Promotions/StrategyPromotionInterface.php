<?php

namespace Promotions;


interface StrategyPromotionInterface {

    /**
     * @param int $grossTotal
     * @return int
     */
    public function discount(int $grossTotal): int;

}