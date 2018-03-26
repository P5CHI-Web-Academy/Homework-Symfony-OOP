<?php

/**
 * Class Polynom
 */
class Polynom
{
    const DEGREE_1 = 1;
    const DEGREE_2 = 2;

    /**
     * @var Monom[]
     */
    protected $part1 = [];
    /**
     * @var Monom[]
     */
    protected $part2 = [];
    /**
     * @var int
     */
    protected $type;
    /**
     * @var Monom
     */
    protected $a;
    /**
     * @var Monom
     */
    protected $b;
    /**
     * @var Monom
     */
    protected $c;
    /**
     * @var Monom
     */
    protected $d;

    /**
     * Polynom constructor.
     * @param Monom[] $part1
     * @param Monom[] $part2
     */
    public function __construct(array $part1, array $part2)
    {
        $this->part1 = $part1;
        $this->part2 = $part2;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function normalize()
    {
        $this->switchAll()->reduce();

        foreach ($this->part1 as $item) {
            if ($item->getType() === Monom::TYPE_3) {
                $this->a = $item;
            }
            if ($item->getType() === Monom::TYPE_2) {
                $this->b = $item;
            }
            if ($item->getType() === Monom::TYPE_1) {
                $this->c = $item;
            }
        }

        $this->d = new Monom(0);

        if (null !== $this->a && null !== $this->b) {
            $this->type = self::DEGREE_2;
        } elseif (null !== $this->b) {
            $this->type = self::DEGREE_1;
        } else {
            throw new  \Exception(sprintf('Unsupported type of equation: %s', $this));
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function switchAll()
    {
        foreach ($this->part2 as $item) {
            $this->part1[] = $item->switch();
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function reduce()
    {
        $result = [];
        foreach (Monom::getTypes() as $type) {
            $similar = array_filter(
                $this->part1,
                function ($item) use ($type) {
                    /** @var Monom $item */
                    if ($item->getType() === $type) {
                        return true;
                    }

                    return false;
                }
            );

            if (!empty($similar)) {
                $result[] = $this->reduceSimilar($similar);
            }
        }

        $this->part1 = $result;

        return $this;
    }

    /**
     * @param Monom[] $parts
     * @return Monom
     */
    protected function reduceSimilar(array $parts)
    {
        /** @var Monom $result */
        $result = null;
        foreach ($parts as $part) {
            if (null === $result) {
                $result = $part;
            } else {
                $result->combine($part);
            }
        }

        return $result;
    }

    /**
     * @return Monom
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @return Monom
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * @return Monom
     */
    public function getC()
    {
        return $this->c;
    }

    /**
     * @return Monom
     */
    public function getD()
    {
        return $this->d;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = '';
        if (null !== $this->a) {
            $result = sprintf('%s', $this->a);
        }

        if (null !== $this->b) {
            $sign = ($this->b->getCoefficient() > 0) ? '+' : '';
            $result = sprintf('%s%s%s', $result, $sign, $this->b);
        }

        if (null !== $this->c && $this->c->getCoefficient() != 0) {
            $sign = ($this->c->getCoefficient() > 0) ? '+' : '';
            $result = sprintf('%s%s%s', $result, $sign, $this->c);
        }

        if (null !== $this->d) {
            $result = sprintf('%s=%s', $result, $this->d);
        }

        return $result;
    }
}
