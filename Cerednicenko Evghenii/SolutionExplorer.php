<?php declare(strict_types=1);

class SolutionExplorer11
{

    private $a = 0.0;
    private $b = 0.0;
    private $c = 0.0;

    public function __construct(string $equation)
    {
        $equationParts = explode('=', $equation);

        $leftPart = explode(' ', $equationParts[0]);
        $rightPart = explode(' ', $equationParts[1]);

        $leftPart = array_diff($leftPart, ['', '+']);
        $rightPart = array_diff($rightPart, ['', '+']);

        $leftPart = $this->attachMinusesToElements($leftPart);
        $this->setFactors($leftPart);

        if (current($rightPart) !== '0') {
            $rightPart = $this->attachMinusesToElements($rightPart);
            $rightPart = $this->negateRightPart($rightPart);
            $this->setFactors($rightPart);
        }

        $this->chooseRule();
    }


    private function attachMinusesToElements(array $part) : array
    {
        foreach ($part as $key => $value) {
            if ($value == '-') {
                $part[$key + 1] = '-' . $part[$key + 1];
                unset($part[$key]);
            }
        }

        return $part;
    }


    private function negateRightPart(array $rightPart) : array
    {
        foreach ($rightPart as $key => $right) {
            // Если первый символ строки не является минусом, добавляем минус в начало строки
            if (substr($right, 0, 1) != '-') {
                $rightPart[$key] = '-' . $rightPart[$key];
            } else {
                // Иначе - перезаписываем элемент строкой без первого символа
                $rightPart[$key] = substr($right, 1, strlen($right));
            }
        }
        return $rightPart;
    }


    private function findFactors(string $part, string $reg, string $factor) : void
    {
        // Начальная позиция в строке, в которой найдено совпадение
        $pos = stripos($part, $reg);
        $num = substr($part, 0, $pos);

        // Если часть строки перед совпадением пуста или равна минусу,
        // добавляем или вычитаем единицу
        if ($num == '-' || $num == '') {
            $this->{$factor} += floatval($num . '1');

        } else {
            // Иначе - добавляем всю строку
            $this->{$factor} += floatval($num);
        }
    }


    private function setFactors(array $partElements) : void
    {
        foreach ($partElements as $part) {
            if (stripos($part, 'x^2') !== false) {
                $this->findFactors($part, 'x^2', 'a');

            } elseif (stripos($part, 'x') !== false) {
                $this->findFactors($part, 'x', 'b');

                // Regex ищет цифры (одну или более) с необязательным минусом перед ними
            } elseif (preg_match('/(?:-?)(?:\d+)/', $part, $num)) {
                $this->c += floatval(current($num));
            }
        }

    }


    private function chooseRule() : void
    {
        if (!$this->b && !$this->c) {
            echo 'x равен 0';
        } elseif (!$this->b) {
            $this->equationWithoutB();
        } elseif (!$this->c) {
            $this->equationWithoutC();
        } else {
            $this->fullEquation();
        }
    }


    private function fullEquation() : void
    {
        $t = 4 * $this->a * $this->c;
        if ($t < 0) {
            $d = pow($this->b, 2) + abs($t);
        } else {
            $d = pow($this->b, 2) - abs($t);
        }

        if ($d < 0) {
            echo 'Дискриминант = ' . $d . ', корней нет, уравнение в области действительных чисел не решается';
        } elseif ($d === 0) {
            $x = round(($this->b) / (2 * $this->a), 2);
            echo 'Корень всего один, х = ' . $x;
        } else {
            $x1 = round(((-$this->b) + sqrt($d)) / (2 * $this->a), 2);
            $x2 = round(((-$this->b) - sqrt($d)) / (2 * $this->a), 2);
            echo "Два корня, х1 = $x1, а х2 = $x2";
        }
    }


    private function equationWithoutC() : void
    {
        echo 'х1 равен 0, а х2 равен ' . round(-$this->b / $this->a, 2);
    }


    private function equationWithoutB() : void
    {
        if ($this->a > 0 && $this->c < 0 || $this->a < 0 && $this->c > 0) {
            $x = round(sqrt(abs($this->c / $this->a)), 2);
            echo "x1 равен $x, а х2 равен -$x";
        } else {
            echo "Коэффициенты \"а\" и \"с\" одного знака, корней нет, уравнение в области действительных чисел не решается";
        }
    }
}


$eq = 'x^2 - 6x + 9 + 5x = 9';

$tmp = new SolutionExplorer11($eq);


