<?php

namespace Promotions;


class StrategyFixedDiscountByContract implements StrategyPromotionInterface {

    /**
     * @var int
     */
    private int $percentage = 10;


    /**
     * @param $grossTotal
     * @return int
     */
    public function discount($grossTotal): int {

        return $grossTotal * ($this->percentage / 100);

    }

}