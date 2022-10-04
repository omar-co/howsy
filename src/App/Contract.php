<?php

namespace App;

class Contract {

    /**
     * @param int $period
     */
    public function __construct(private int $period = 0) {

    }

    /**
     * @return int|null
     */
    public function getPeriod(): ?int {
        return $this->period;
    }

    /**
     * @param integer $period
     * @return Contract
     */
    public function setPeriod(int $period): static {
        $this->period = $period;

        return $this;
    }

}