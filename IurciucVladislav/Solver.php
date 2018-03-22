<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Решатель кв. ур.</title>
    </head>
    <body>

<?php
/**
 * Created by PhpStorm.
 * User: god
 * Date: 3/17/18
 * Time: 6:06 PM
 */
Class quadraticEquation
{
    protected $equation;

    public function __construct($eq)
    {
        $this->equation = $eq;
    }

    protected static function getCoefficient($equation, $powerX = 0): int
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
        $parts = explode('=', $equation);
        $parts[0] = str_replace("+x", "+1x", $parts[0]);
        $parts[0] = str_replace("-x", "-1x", $parts[0]);

//    добавляю "+" в правую часть уравнения, если его нет
        if (preg_match("/\A(x|[0-9])/", $parts[1])) {
            $parts[1] = '+' . $parts[1];
        }

        preg_match_all('/-?\d*?x\^' . $powerX . '/', $parts[0], $a_matches); // нахожу все х^
        $a = 0;

        // суммирую коэфиценты "а" левой части ур.
        for ($i = 0; $i < count($a_matches[0]); $i++) {
            $a_replaced[$i] = preg_replace('/x\^' . $powerX . '/', '', $a_matches[0][$i]);
            $a += $a_replaced[$i];
        }


        //правая часть уравнения

        $parts[1] = str_replace("+x", "+1x", $parts[1]);
        $parts[1] = str_replace("-x", "-1x", $parts[1]);
        preg_match_all('/(\+|-)\d*?x\^' . $powerX . '/', $parts[1], $a_matches); //нахожу все x^
        // суммирую коэфиценты "а" правой части ур.
        for ($i = 0; $i < count($a_matches[0]); $i++) {
            $a_replaced[$i] = preg_replace('/x\^' . $powerX . '/', '', $a_matches[0][$i]);
            $a -= $a_replaced[$i];
        }
        return $a;
    }

    public function solveAndShow() {
        $a = self::getCoefficient($this->equation, 2);
        $b = self::getCoefficient($this->equation, 1);
        $c = self::getCoefficient($this->equation, 0);
        echo 'a = '.$a.'</br>';
        echo 'b = '.$b.'</br>';
        echo 'c = '.$c.'</br>';
        $d = $b*$b-4*$a*$c;
        echo 'd = '.$d.'</br>';
        $x1 = (-$b - sqrt($d)) / (2*$a);
        $x2 = (-$b + sqrt($d)) / (2*$a);
        echo 'X1 = '.$x1.'</br>';
        echo 'X2 = '.$x2.'</br>';
    }
}

echo '

    <form action="Solver.php" method="post">
        <p><input type="text" name="eq" size="40" value="'.$_POST['eq'].'"/></p>
        <p><input type="submit" /></p>
    </form>
';
$eq = new quadraticEquation(htmlspecialchars($_POST['eq']));
$eq->solveAndShow();

?>


    </body>
</html>