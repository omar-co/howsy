<?php

namespace App;

class User {

    /**
     * @param Contract $contract
     */
    public function __construct(
        private readonly Contract $contract
    ) {

    }

    /**
     * @return Contract
     */
    public function getContract(): Contract {
        return $this->contract;
    }



}