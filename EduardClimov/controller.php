<?php
declare(strict_types=1);
error_reporting(E_ALL);

class Nominal {
    /**
    *@var int|float
    */
    private $coeficient=0;

    /**
     * @var string
     */
    private $nominal;

    /**
     * @var int
     */
    private $order = 0; //Possible values: 0, 1, 2

    public function __construct(string $str)
    {
        //echo($str."<br>");
        $this->nominal = $str;

        if(preg_match("/[a-z]{1}[\^][2]|[a-z]\*[a-z]/", $str)){
            $this->order = 2;
            $this->coeficient=1; //At least 1
        } elseif (preg_match("/[a-zA-Z]{1}/", $str)) {
            $this->order = 1;
            $this->coeficient=1; //At least 1
        }

        if(preg_match("/[-]?[0-9]+([.][0-9]+)?/", preg_replace("/[\^][0-9]?/", '', $str), $matches)){
            $this->coeficient = $matches[0];
            //echo($this->coeficient."<br>");
        } elseif(preg_match("/^[-][\w]/", preg_replace("/[\^][0-9]?/", '', $str), $matches)){
            $this->coeficient *= -1;
        }
    }

    public function __toString(): string
    {
        return sprintf($this->nominal.' is a nominal with coeficient "%s" and order "%s"', $this->get_coeficient(), $this->get_order()) . PHP_EOL;
    }

    public function get_coeficient()
    {
        return $this->coeficient;
    }

    public function get_order(): int
    {
        return $this->order;
    }

    public function combine(Nominal $nominal): Nominal
    {
        if($this->order == $nominal->order){
            preg_replace("/[-]?[0-9]+/", $this->coeficient+$nominal->coeficient, $this->coeficient);
            $this->coeficient += $nominal->coeficient;
            if(!$this->coeficient){
                $this->order = 0;
            }
        }

        return $this;
    }
}

class Polynominal
{
    /**
    *@var int|float
    */
    private $a=0; //('3' from '3x^2+4x-1')

    /**
    *@var int|float
    */
    private $b=0; //('4' from '3x^2+4x-1')

    /**
    *@var int|float
    */
    private $c=0; //('-1' from '3x^2+4x-1')

    function __construct(string $str)
    {
        $exploded1 = explode('+', $str); //Split by '+'
        foreach ($exploded1 as $e1)
        {
            $exploded2 = preg_split('@(?=-)@', $e1); //Split by '-', but keep the delimiter
            foreach($exploded2 as $e2)
            {
                $nominal = new Nominal($e2);
                if($nominal->get_order() == 2){
                    $this->a += $nominal->get_coeficient();
                } elseif ($nominal->get_order() == 1) {
                    $this->b += $nominal->get_coeficient();
                } else{
                    $this->c += $nominal->get_coeficient();
                }
            }
        }
    }

    public function __toString(): string
    {
        return sprintf('(%s) + (%s) + (%s)', $this->get_a(), $this->get_b(), $this->get_c()) . PHP_EOL;
    }

    public function combine(Polynominal $poly): Polynominal
    {
        $this->a -= $poly->get_a();
        $this->b -= $poly->get_b();
        $this->c -= $poly->get_c();

        return $this;
    }

    public function get_a()
    {
        return $this->a;
    }

    public function get_b()
    {
        return $this->b;
    }

    public function get_c()
    {
        return $this->c;
    }

}

class Equation{
    /**
     * @var string
     */
    private $polystring;

    /**
     * @var string
     */
    private $variable='x';

    /**
     * @var Polynominal
     */
    private $poly1;

    /**
     * @var Polynominal
     */
    private $poly2;

    function __construct($str) //Any input, not necessary string
    {
        $this->polystring = $this->process_equation((string)$str);

        if($this->validate())
        {
            preg_match("/[a-zA-Z]/", $str, $matches);
            $this->variable = $matches[0];

            $parts = explode('=', $this->polystring);
            $this->poly1 = new Polynominal($parts[0]);
            $this->poly2 = new Polynominal($parts[1]);
            $this->poly1->combine($this->poly2);
        }
    }

    public function process_equation(string $str): string
    {
        return strtolower( //To lowercase
            str_replace(',', '.', //Fix decimal format
                preg_replace("/(\^[1])(?=[^0-9]|$)/", '', //Clear 1st degree
                    preg_replace("/(?<=[0-9])([\*])(?=[A-Za-z])/", '', //Clear unnecessary asterisks(*)
                        str_replace(' ', '', //Clear spaces
                            $str
                        )
                    )
                )
            )
        );
    }

    public function validate(): bool
    {
        $code = 0;
        if(
            //1. There should be only available characters in string
            $code=1 && preg_match("/[^0-9a-z\.\*\+\-\=\^]/", $this->polystring) ||
            //2. There should always be one operator '=' with both operands (3+ chars in total)
            $code=2 && (count(explode('=', $this->polystring))!=2 || !preg_match("/[a-z0-9]=-?[a-z0-9]/", $this->polystring)) ||
            //3. Only 2nd degree is available
            $code=3 && preg_match("/[\^][^2]|[\^][0-9a-z]{2,}/", $this->polystring) ||
            //4. There should never be two operators in a row, except '=-'
            $code=4 && preg_match("/[\.\*\+\-\^]{2,}|[\.\*\+\=\^]{2,}/", $this->polystring) ||
            //5. There should never be two letters in a row
            $code=5 && preg_match("/[a-z]{2,}/", $this->polystring) ||
            //6. There should never be two DIFFERENT letters in equation (our case)
            $code=6 && count(count_chars(preg_replace('/[0-9\.\^\*\+\-\=]/', '', $this->polystring), 1)) > 1
        )
        {
            //echo 'Error'.$code.': '.$this->polystring.PHP_EOL; //Output of error codes for debug
        }

        return !$code;
    }

    public function get_polynominal()
    {
        return $this->poly1;
    }

    public function get_variable(): string
    {
        return $this->variable;
    }
}


abstract class Solver {
    public static function resolve($str=''): string
    {
        $solution = '';
        $equation = new Equation($str);

        if(!$equation->get_polynominal()){
            $solution = "Invalid equation";
        } else{
            $variable = $equation->get_variable();

            $a = $equation->get_polynominal()->get_a();
            $b = $equation->get_polynominal()->get_b();
            $c = $equation->get_polynominal()->get_c();
            if($a != 0){
                if($b != 0){
                    $x1 = ((-$b)-sqrt($b*$b-4*$a*$c))/2*$a;
                    $x2 = ((-$b)+sqrt($b*$b-4*$a*$c))/2*$a;
                    $solution = $variable.'1='.$x1.', '.$variable.'2='.$x2;
                } else{
                    $x1 = -sqrt((-$c)/$a);
                    $x2 = sqrt((-$c)/$a);
                    $solution = $variable.'1='.$x1.', '.$variable.'2='.$x2;
                }
            } elseif($b != 0){
                $x = (-$c)/$b;
                $solution = $variable.'='.$x;
            }
        }

        return $solution;
    }
}
