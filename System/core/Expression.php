<?php

class Expression
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Magic method agar saat object ini di-implode, 
     * dia mengembalikan string aslinya.
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    public function getValue()
    {
        return $this->value;
    }
}