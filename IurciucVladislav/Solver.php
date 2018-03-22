<?php
/**
 * Created by PhpStorm.
 * User: god
 * Date: 3/17/18
 * Time: 6:06 PM
 */
Class quadraticEquation
{
    private $equation;
    private $a;
    private $b;
    private $c;
    private $d;
    private $x1;
    private $x2;
    private $parts;

    public function __construct($eq = 0)
    {
        $this->equation = $eq;
    }

    private function makeEq($equation, $powerX)
    {
        $equation = str_replace(" ", "", $equation);
        $equation = str_replace("*", "", $equation);

//    добавляю "+" перед "-", что бы потом использовать "+", как разделитель
        $equation = str_replace("-", "+-", $equation);
        $equation = strtolower($equation);

//    x^1 - добавляет первую степень иксам
        if ($powerX == 1) {
            $equation = preg_replace('/x-/', 'x^1-', $equation);
            $equation = preg_replace('/x\+/', 'x^1+', $equation);
            $equation = preg_replace('/x\=/', 'x^1=', $equation);
            $equation = preg_replace('/x$/', 'x^1', $equation); //"х" в конце строки
        }

//    x^0 - добавляет к числам икс в нулевой степени
        if ($powerX == 0) {
            $equation = preg_replace('/\A(\d+)(\W)/', '$1x^0$2', $equation); //число в начале строки
            $equation = preg_replace('/(\+|=|-)(\d+)(\+|=|-)/', '$1$2x^0$3', $equation); //число в середине ур.
            $equation = preg_replace('/(\+|-|=)\d+\z/', '$0x^0', $equation); //число в конце строки
        }
//    добавляю "+" в начало, если его нет
        if (preg_match("/\A(x|[0-9])/", $equation)) {
            $equation = '+' . $equation;
        }
        $this->parts = explode('=', $equation);
        $this->parts[0] = str_replace("+x", "+1x", $this->parts[0]);
        $this->parts[0] = str_replace("-x", "-1x", $this->parts[0]);

//    добавляю "+" в правую часть уравнения, если его нет
        if (preg_match("/\A(x|[0-9])/", $this->parts[1])) {
            $this->parts[1] = '+' . $this->parts[1];
        }

    }

    private function getCoefficient($equation, $powerX = 0): int
    {
        $this->makeEq($equation, $powerX);
        preg_match_all('/-?\d*?x\^' . $powerX . '/', $this->parts[0], $k_matches); // нахожу все х^
        $k = 0;

        // суммирую коэфиценты "а" левой части ур.
        for ($i = 0; $i < count($k_matches[0]); $i++) {
            $k_replaced[$i] = preg_replace('/x\^' . $powerX . '/', '', $k_matches[0][$i]);
            $k += $k_replaced[$i];
        }


        //правая часть уравнения

        $this->parts[1] = str_replace("+x", "+1x", $this->parts[1]);
        $this->parts[1] = str_replace("-x", "-1x", $this->parts[1]);
        preg_match_all('/(\+|-)\d*?x\^' . $powerX . '/', $this->parts[1], $k_matches); //нахожу все x^
        // суммирую коэфиценты "а" правой части ур.
        for ($i = 0; $i < count($k_matches[0]); $i++) {
            $k_replaced[$i] = preg_replace('/x\^' . $powerX . '/', '', $k_matches[0][$i]);
            $k -= $k_replaced[$i];
        }
        return $k;
    }

    public function solve() : quadraticEquation
    {
        $this->a = $this->getCoefficient($this->equation, 2);
        $this->b = $this->getCoefficient($this->equation, 1);
        $this->c = $this->getCoefficient($this->equation, 0);
        $this->d = $this->b * $this->b - 4 * $this->a * $this->c;

        if ($this->d >= 0) {
            $this->x1 = round((-$this->b - sqrt($this->d)) / (2 * $this->a), 2);

            if ($this->d > 0)
            $this->x2 = round((-$this->b + sqrt($this->d)) / (2 * $this->a), 2);
        }

        return $this;
    }

    public function show() : quadraticEquation
    {
        echo 'a = ' . $this->a . "\n";
        echo 'b = ' . $this->b . "\n";
        echo 'c = ' . $this->c . "\n";
        echo 'd = ' . $this->d . "\n";

        if ($this->d >= 0) {
            echo 'X1 = ' . $this->x1 . "\n";

            if ($this->d > 0) {
                echo 'X2 = ' . $this->x2 . "\n";
            }
        }
        return $this;
    }
}

$eq = new quadraticEquation(htmlspecialchars($_SERVER['argv'][1]));
$eq->solve()->show();