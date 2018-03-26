<?php

/**
 * Class Result
 */
class Result
{
    /**
     * @var Solution[]
     */
    protected $solutions;
    /**
     * @var string
     */
    protected $description;

    /**
     * Solutions constructor.
     */
    public function __construct()
    {
        $this->solutions = [];
    }

    /**
     * @param Solution $solution
     * @return $this
     */
    public function addSolution(Solution $solution)
    {
        $this->solutions[] = $solution;

        return $this;
    }

    /**
     * @return Solution[]
     */
    public function getSolutions()
    {
        return $this->solutions;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Result
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }
}
