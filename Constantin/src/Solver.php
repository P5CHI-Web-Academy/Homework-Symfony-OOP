<?php

/**
 * Class Solver
 */
class Solver
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * @var float
     */
    protected $discriminant;

    /**
     * @var Polynom
     */
    private $polynom;

    /**
     * Solver constructor.
     * @param Polynom $polynom
     */
    public function __construct(Polynom $polynom)
    {
        $this->result = new Result();
        $this->polynom = $polynom;
    }

    /**
     * @return $this
     */
    public function solve()
    {
        if ($this->polynom->getType() === Polynom::DEGREE_2) {
            $this->solveDegree2();
        }

        if ($this->polynom->getType() === Polynom::DEGREE_1) {
            $this->solveDegree1();
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function solveDegree1()
    {
        $solution = $this->polynom->getC()->switch()->getCoefficient() / $this->polynom->getB()->getCoefficient();

        $name = sprintf('%s', $this->polynom->getB()->getVariable());
        $this->result->addSolution(new Solution($name, $solution));

        return $this;
    }

    /**
     * @return $this
     */
    protected function solveDegree2()
    {
        $this->calculateDiscriminant();
        if ($this->discriminant < 0) {
            $this->result->setDescription('The equation has no real solutions.');
        } elseif ($this->discriminant >= 0) {
            $this->calculateSolutions();
            $this->result->setDescription('The equation has solutions');
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function calculateSolutions()
    {
        $minusB = -1 * $this->polynom->getB()->getCoefficient();
        $root = sqrt($this->discriminant);
        $_2a = 2 * $this->polynom->getA()->getCoefficient();

        $solution1 = ($minusB - $root) / $_2a;
        $solution2 = ($minusB + $root) / $_2a;

        $variableName = $this->polynom->getA()->getVariable();
        $name = sprintf('%s1', $variableName);
        $this->result->addSolution(new Solution($name, $solution1));

        $name = sprintf('%s2', $variableName);
        $this->result->addSolution(new Solution($name, $solution2));

        return $this;
    }

    /**
     * @return $this
     */
    protected function calculateDiscriminant()
    {
        $this->discriminant = pow($this->polynom->getB()->getCoefficient(), 2)
            - 4
            * $this->polynom->getA()->getCoefficient()
            * $this->polynom->getC()->getCoefficient();

        return $this;
    }

    /**
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }
}
