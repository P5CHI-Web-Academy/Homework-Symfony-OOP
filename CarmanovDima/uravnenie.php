<?php

Class Polynomial
{

    public $data = [];
    public $undefined_values = null;
    public $degree = null;

    public function __construct($expression)
    {
        if(!preg_match_all('/(((?<symbol>[\+\-]))((?<coefficient>\d+))?(?<var_name>[a-z]+)(\^(?<power>\d+))?)|(?<constant>[\+\-]\d+)/isx',
            $this->prepareExpression($expression), $members, PREG_SET_ORDER)) {
            throw new \Exception("Incorrect expression.");
        }
        foreach ($members as $member) {
            if(isset($member['constant'])) {
                $this->data[0]['constant'][] = (int) $member['constant'];
            }else {
                $power = isset($member['power']) && $member['power'] !== '' ? (int) $member['power'] : 1;
                $coefficient = isset($member['coefficient']) && $member['coefficient'] !== '' ? (int) $member['coefficient'] : 1;
                if($member['symbol'] === '-') {
                    $coefficient *= -1;
                }
                $this->data[$power][$member['var_name']][] = $coefficient;
            }
        }
        $this->order();
    }

    public function merge(Polynomial $other): Polynomial
    {
        foreach ($other->ExpressionData() as $key => $value) {
            $this->data[$key] = array_merge_recursive($this->data[$key] ?? [], $value);
        }
        $this->order();
        $this->degree = $this->undefined_values = null;

        return $this;
    }

    public function switch(): Polynomial
    {
        foreach ($this->data as &$members) {
            foreach ($members as $member_name => &$value) {
                $value *= -1;
            }
        }

        return $this;
    }

    public function Coefficient($power, $member_name = 'constant'): int
    {
        return $this->data[$power][$member_name] ?? 0;
    }

    public function Degree(): int
    {
        return $this->degree ?? $this->degree = max(array_keys($this->data));
    }

    public function UndefinedValues(): array
    {
        if($this->undefined_values=== null) {
            $names = [];
            foreach ($this->data as $power => $members) {
                if(!$power) continue;
                $names = array_merge($names, array_keys($members));
            }
            $this->undefined_values = array_unique($names);
        }

        return $this->undefined_values;
    }

    protected function ExpressionData(): array
    {
        return $this->data;
    }

    private function order(): void
    {
        foreach ($this->data as &$members) {
            foreach ($members as $member_name => &$value) {
                $value = is_array($value) ? array_sum($value) : $value;
            }
        }
    }

    private function prepareExpression($expression): string
    {
        $expression = preg_replace(['/\s+/', '/\*/'], '', $expression);
        if(!preg_match('/^[\+-]/', $expression)) {
            $expression = '+' . $expression;
        }

        return $expression;
    }
}


class Equation
{
    public const SOLVED_SUCCESS = 8;
    public const SOLVED_TWO_ROOTS = 4;
    public const SOLVED_ONE_ROOT = 2;
    public const SOLVED_NO_ROOTS = 1;
    public const ERROR_NOT_QUADRATIC = 2;
    public const ERROR_NOT_SINGLE_IND = 1;


    public function __construct(){}


    public static function resolve(string $expression) : array
    {
        $polynomial = self::PolynomialExpression($expression);

        $status = self::check($polynomial);
        if($status['errors']) {
            throw new \Exception(self::error($status['errors']));
        }

        return self::resolvePolynomial($polynomial);
    }


    protected static function resolvePolynomial(Polynomial $polynomial) : array
    {
        $ind_name = $polynomial->UndefinedValues()[0];
        $ax = $polynomial->Coefficient(2, $ind_name);
        $bx = $polynomial->Coefficient(1, $ind_name);
        $c = $polynomial->Coefficient(0);

        $result = ['status' => self::SOLVED_SUCCESS];

        if(($d = $bx * $bx - 4 * $ax * $c) > 0 && $d = \sqrt($d)) {
            $result['status'] |= self::SOLVED_TWO_ROOTS;
            $result[$ind_name] = [(- $bx + $d) / (2 * $ax), (- $bx - $d) / (2 * $ax)];
        }elseif($d == 0) {
            $result['status'] |= self::SOLVED_ONE_ROOT;
            $result[$ind_name] = [(- $bx) / (2 * $ax)];
        }else{
            $result['status'] |= self::SOLVED_NO_ROOTS;
            $result[$ind_name] = [];
        }

        return $result;
    }


    protected static function PolynomialExpression(string $expression) : Polynomial
    {
        $parts = explode('=', $expression);

        return (new Polynomial($parts[0]))->merge(
            (new Polynomial($parts[1]))->switch()
        );
    }


    protected static function check(Polynomial $polynomial) : array
    {
        $status = ['errors' => 0];
        if($polynomial->Degree() !== 2) {
            $status['errors'] |= self::ERROR_NOT_QUADRATIC;
        }
        if(count($polynomial->UndefinedValues()) !== 1) {
            $status['errors'] |= self::ERROR_NOT_SINGLE_IND;
        }

        return $status;
    }


    private static function error($errors) : string
    {

        $msg = '';
        if($errors & self::ERROR_NOT_QUADRATIC) {
            $msg .= 'This expression is not quadratic polynomial.' . PHP_EOL;
        }
        if($errors & self::ERROR_NOT_SINGLE_IND) {
            $msg .= 'This expression is not a polynomial of a single indeterminate.' . PHP_EOL;
        }

        return $msg;
    }
}

if (isset($_SERVER['argv'][1])) {
    try {
        print_r(equation::resolve($_SERVER['argv'][1]));
    } catch(Exception $e) {
        echo 'Exception: ' . $e->getMessage() . PHP_EOL;
    }
} else {
    echo 'No arguments provided.' . PHP_EOL;
}
