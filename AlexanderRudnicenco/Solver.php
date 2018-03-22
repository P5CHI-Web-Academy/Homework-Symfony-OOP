<form action="Solver.php" method="post">
    <h2>Вычисление квадратного уравнения</h2>
    <p><input type="text" name="data"/></p>
    <p><input type="submit" name="subbut" value="Вычислить!"/></p>
</form>

<?php
if (isset($_POST['subbut'])) {
    if (!empty($_POST['data'])) {

        Class Solver {
            protected $equation;

            # Конструктор с уравнением в качестве параметра
            function __construct($equation) {
                $this->equation = $equation;
            }

            # Функция для удаления пробелов и знака '*'
            protected static function reduction($equation) : string {
                $equation = strtolower($equation);
                $equation = str_replace(" ", "", $equation);
                $equation = str_replace("*", "", $equation);
                return $equation;
            }

            # Функция, которая преобразовывает введенные пользователем данные в полноценное уравнение
            protected static function makeEquationAvailable($equation, $xDegree) : int {
                $equation = str_replace("-", "+-", $equation);

                switch ($xDegree) {
                    # Если степень икса равна нулю, выполняется замена значения согласно правилам алгебры
                    case 0 : {
                        $equation = preg_replace('/(\+|=|-)(\d+)(\+|=|-)/', '$1$2x^0$3', $equation);
                        $equation = preg_replace('/\A(\d+)(\W)/', '$1x^0$2', $equation);
                        $equation = preg_replace('/(\+|-|=)\d+\z/', '$0x^0', $equation);
                        break;
                    }
                    # Аналогично с иксом в степени один
                    case 1 : {
                        $equation = preg_replace('/x\+/', 'x^1+', $equation);
                        $equation = preg_replace('/x\=/', 'x^1=', $equation);
                        $equation = preg_replace('/x-/', 'x^1-', $equation);
                        break;
                    }
            }

                if (preg_match("/\A(x|[0-9])/", $equation)) {
                    $equation = '+' . $equation;
                }

                # Разделение уравнения на две части (левую и правую)
                $equationParts = explode('=', $equation);

                $equationParts[0] = str_replace("+x", "+1x", $equationParts[0]);
                $equationParts[0] = str_replace("-x", "-1x", $equationParts[0]);

                if (preg_match("/\A(x|[0-9])/", $equationParts[1])) {
                    $equationParts[1] = '+' . $equationParts[1];
                }
                preg_match_all('/-?\d*?x\^' . $xDegree . '/', $equationParts[0], $matches);
                $a = 0;

                for ($i = 0; $i < count($matches[0]); $i++) {
                    $replacedOnA[$i] = preg_replace('/x\^' . $xDegree . '/', '', $matches[0][$i]);
                    $a += $replacedOnA[$i];
                }

                # Замена икса в правой части уравнения
                $equationParts[1] = str_replace("+x", "+1x", $equationParts[1]);
                $equationParts[1] = str_replace("-x", "-1x", $equationParts[1]);
                preg_match_all('/(\+|-)\d*?x\^' . $xDegree . '/', $equationParts[1], $matches);

                # Цикл для поиска всех иксв со степенью
                for ($i = 0; $i < count($matches[0]); $i++) {
                    $replacedOnA[$i] = preg_replace('/x\^' . $xDegree . '/', '', $matches[0][$i]);
                    $a -= $replacedOnA[$i];
                }
                return $a;
            }

            # Функция, которая вычисляет дискриминант и корни уравнения и выводит результат на экран
            function calculateAndShow() {

                $multiplicity = [];

                $multiplicity[0] = self::reduction($this->equation);
                $multiplicity[1] = self::reduction($this->equation);
                $multiplicity[2] = self::reduction($this->equation);

                $multiplicity[0] = self::makeEquationAvailable($this->equation, 2);
                $multiplicity[1] = self::makeEquationAvailable($this->equation, 1);
                $multiplicity[2] = self::makeEquationAvailable($this->equation, 0);

                echo '<hr>';

                echo 'Множество a = ' . $multiplicity[0] . '</br>';
                echo 'Множество b = ' . $multiplicity[1] . '</br>';
                echo 'Множество c = ' . $multiplicity[2] . '</br>' . '<hr>';

                $discriminant = pow($multiplicity[1], 2) - 4 * $multiplicity[0] * $multiplicity[2];
                echo 'Дискриминант равен : ' . $discriminant . '</br>' . '<hr>';

                $x1 = (-$multiplicity[1] - sqrt($discriminant)) / (2 * $multiplicity[0]);
                $x2 = (-$multiplicity[1] + sqrt($discriminant)) / (2 * $multiplicity[0]);

                echo 'Корень X1 равен : ' . $x1 . '</br>';
                echo 'Корень X2 равен : ' . $x2 . '</br>' . '<hr>';
            }
        }

        $solver = new Solver($_POST['data']);
        $solver->calculateAndShow();
    } else
        echo "Заполните поле!";
}
?>
