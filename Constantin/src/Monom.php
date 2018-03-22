<?php

/**
 * Class Monom
 */
class Monom
{
    /**
     * Simple number
     */
    const TYPE_1 = 0;
    /**
     * Ex: 2x
     */
    const TYPE_2 = 1;
    /**
     * Ex: 3x^2
     */
    const TYPE_3 = 2;

    /**
     * @var int
     */
    protected $type;
    /**
     * @var int
     */
    protected $power = 1;
    /**
     * @var string
     */
    protected $variable = '';
    /**
     * @var float
     */
    protected $coefficient;

    /**
     * Monom constructor.
     * @param float $coefficient
     * @param string $variable
     * @param int $power
     * @throws Exception
     */
    public function __construct(float $coefficient, string $variable = '', int $power = 1)
    {
        $this->coefficient = $coefficient;
        $this->variable = $variable;
        $this->power = $power;
        $this->setType();
    }

    /**
     * @throws Exception
     */
    protected function setType()
    {
        $this->type = self::TYPE_1;
        if (empty($this->variable)) {
            return;
        }

        switch ($this->power) {
            case 1:
                $this->type = self::TYPE_2;
                break;
            case 2:
                $this->type = self::TYPE_3;
                break;
            default:
                throw new \Exception(sprintf('Unsupported: %s', $this));
        }
    }

    /**
     * @param Monom $monom
     */
    public function combine(Monom $monom)
    {
        if ($this->type === $monom->type) {
            $this->coefficient += $monom->coefficient;
        }
    }

    /**
     * @return $this
     */
    public function switch()
    {
        $this->coefficient *= -1;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @return float
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            Monom::TYPE_1,
            Monom::TYPE_2,
            Monom::TYPE_3,
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (0 == (int)$this->coefficient) {
            return '0';
        }

        $result = '';
        if (!empty($this->variable)) {
            if (1 != abs($this->coefficient)) {
                $result = sprintf('%s', $this->coefficient);
            } elseif (-1 == $this->coefficient) {
                $result = '-';
            }

            $result = sprintf('%s%s', $result, $this->variable);
            if ($this->power > 1) {
                $result = sprintf('%s^%s', $result, $this->power);
            }
        } else {
            $result = sprintf('%s', $this->coefficient);
        }

        return $result;
    }
}
