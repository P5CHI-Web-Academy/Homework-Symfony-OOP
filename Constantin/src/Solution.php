<?php

class Solution
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var float
     */
    protected $value;

    /**
     * Solution constructor.
     *
     * @param string $name
     * @param float $value
     */
    public function __construct(string $name, float $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s = %s', $this->name, $this->value);
    }
}
