<?php

namespace App;

class Product {

    /**
     * @param string $code
     * @param string $name
     * @param int $price
     */
    public function __construct(
        /** @var $code string */
        private readonly string $code,
        /** @var $name string */
        private readonly string $name,
        /** @var $price integer */
        private readonly int    $price,
    ) {

    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPrice(): int {
        return $this->price;
    }

}