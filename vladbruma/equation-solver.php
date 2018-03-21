<?php

class QuadraticSolver
{

    private $equation;
    private $a = 0;
    private $b = 0;
    private $c = 0;
    private $x1 = null;
    private $x2 = null;

    public function __construct($equation = null)
    {
        $this->equation = $equation;
    }

    public function filter()
    {
        $this->equation = strtolower(str_replace([' ', '*'], '', $this->equation));
        $this->equation = str_replace('-x', '-1x', $this->equation);
        $this->equation = str_replace('+x', '+1x', $this->equation);

        return $this;
    }

    public function validateSymbols()
    {
        $matches = array();
        preg_match_all('/[^0-9xX\+\-=\^]+/', $this->equation, $matches);

        if (count($matches[0])) {
            echo "\nInvalid characters detected: " . implode(' ', $matches[0]) . "\n\n";
            exit(0);
        }

        return $this;
    }

    public function equateToZero()
    {
        $elements = explode('=', $this->equation);

        if (count($elements) != 2) {
            echo "\nInvalid Equation format supplied (must be 1 count of '=' symbol)\n\n";
            exit(0);
        }

        $inversedPart = $elements[1][0] != '-' ? '+' . $elements[1] : $elements[1];
        $inversedPart = str_replace('-', '$', $inversedPart);
        $inversedPart = str_replace('+', '-', $inversedPart);
        $inversedPart = str_replace('$', '+', $inversedPart);

        $this->equation = $elements[0] . $inversedPart;
        $this->equation = $this->equation[0] != '-' ? '+' . $this->equation : $this->equation;

        return $this;
    }

    public function combineElements()
    {
        $matchA = [];
        preg_match_all('/([\-\+]{1}[\d]+)x\^2/', $this->equation, $matchA);

        if (count($matchA[1])) {
            foreach($matchA[1] as $key => $el) {
                $this->a += intval($el);
                // clear match from equation
                $this->equation = str_replace($matchA[0][$key], '', $this->equation);
            }
        }

        $matchB = [];
        preg_match_all('/([\-\+]{1}[\d]+)x/', $this->equation, $matchB);

        if (count($matchB[1])) {
            foreach($matchB[1] as $key => $el) {
                $this->b += intval($el);
                // clear match from equation
                $this->equation = str_replace($matchB[0][$key], '', $this->equation);
            }
        }

        $matchC = [];
        preg_match_all('/([\-\+]{1}[\d]+)/', $this->equation, $matchC);

        if (count($matchC[1])) {
            foreach($matchC[1] as $key => $el) {
                $this->c += intval($el);
                // clear match from equation
                $this->equation = str_replace($matchC[0][$key], '', $this->equation);
            }
        }

        return $this;
    }

    public function findPossibleSolutions()
    {

        if ($this->a === 0) {
            // x^2 was not given or equals to 0
            $this->x1 = round(-$this->c / $this->b, 2);
        } elseif ($this->b === 0) {
            // x was not given or equals to 0
            $this->x1 = round(sqrt(-$this->c / $this->a), 2);
        } else {
            $d = pow($this->b, 2) - 4 * $this->a * $this->c;

            if ($d < 0) {
                // no possible solutions
            } elseif ($d == 0) {
                $this->x1 = round((-$this->b / 2 * $this->a), 2);
            } else {
                $this->x1 = round(((-$this->b + sqrt($d)) / (2 * $this->a)), 2);
                $this->x2 = round(((-$this->b - sqrt($d)) / (2 * $this->a)), 2);
            }
        }

        return $this;
    }

    public function outputResult()
    {
        if ($this->x1 === null && $this->x2 === null) {
            echo "\nEquation is invalid or does not have possible solutions\n\n";
        } else if ($this->x2 === null) {
            echo "\nx = {$this->x1}\n\n";
        } else {
            echo "\nx1 = {$this->x1}\n";
            echo "x2 = {$this->x2}\n";
        }

        return $this;
    }
}


// main procedure
if ($_SERVER['argc'] != 2) {
    echo "\nInvalid number of arguments supplied\n\n";
    exit(0);
}

$solver = new QuadraticSolver($_SERVER['argv'][1]);
$solver->filter()
    ->validateSymbols()
    ->equateToZero()
    ->combineElements()
    ->findPossibleSolutions()
    ->outputResult();
