<?php
/**
 * Copyright (c) 2018.
 *
 *  @author    Alexander Sterpu <alexander.sterpu@gmail.com>
 */

declare(strict_types=1);

namespace Sterpu;


class PolynomialExpression
{
    /**
     * Internal data
     */
    private $data = [];

    /**
     * All indeterminate names
     */
    private $indeterminate_names = null;

    /**
     * The degree of the polynomial expression
     */
    private $degree = null;

    /**
     * PolynomialExpression constructor.
     * @param string $expression
     */
    public function __construct(string $expression)
    {
        $this->initExpression($expression);
        $this->normalize();
    }

    /**
     * Combines two polynomial expressions
     *
     * @param PolynomialExpression $other
     * @return PolynomialExpression
     */
    public function combine(PolynomialExpression $other): PolynomialExpression
    {
        foreach ($other->getExpressionData() as $key => $value) {
            $this->data[$key] = array_merge_recursive($this->data[$key] ?? [], $value);
        }
        $this->normalize();
        $this->degree = $this->indeterminate_names = null;

        return $this;
    }

    /**
     * Multiplies current expression by -1
     *
     * @return PolynomialExpression
     */
    public function switch(): PolynomialExpression
    {
        foreach ($this->data as &$members) {
            foreach ($members as $member_name => &$value) {
                $value *= -1;
            }
        }

        return $this;
    }

    /**
     * Returns a related to a specific member coefficient
     *
     * @param $power
     * @param string $member_name
     * @return int
     */
    public function getMemberCoefficient($power, $member_name = 'const'): int
    {
        return $this->data[$power][$member_name] ?? 0;
    }

    /**
     * Returns the degree of the polynomial expression
     *
     * @return int
     */
    public function getDegree(): int
    {
        return $this->degree ?? $this->degree = max(array_keys($this->data));
    }

    /**
     * Returns all indeterminate names
     *
     * @return array
     */
    public function getIndeterminateNames(): array
    {
        if($this->indeterminate_names === null) {
            $names = [];
            foreach ($this->data as $power => $members) {
                if(!$power) continue;
                $names = array_merge($names, array_keys($members));
            }
            $this->indeterminate_names = array_unique($names);
        }

        return $this->indeterminate_names;
    }

    /**
     * @return array
     */
    protected function getExpressionData(): array
    {
        return $this->data;
    }

    /**
     *  Normalizes the data of the polynomial
     */
    private function normalize(): void
    {
        foreach ($this->data as &$members) {    
            foreach ($members as $member_name => &$value) {
                $value = is_array($value) ? array_sum($value) : $value;
            }
        }
    }

    /**
     * @param $expression
     * @throws \Exception
     */
    private function initExpression($expression): void
    {
        if(!preg_match_all('/
            (
                ((?<sign>[\+\-]))
                ((?<coef>\d+))?
                (?<var_name>[a-z]+)
                (\^(?<power>\d+))?
            )|(?<const>[\+\-]\d+)/isx', 
            $this->prepareExpression($expression), $members, PREG_SET_ORDER)) {
            throw new \Exception("Incorrect expression.");
        }
        foreach ($members as $member) {
            if(isset($member['const'])) {
                $this->data[0]['const'][] = (int) $member['const'];
            }else {
                $power = isset($member['power']) && $member['power'] !== '' ? (int) $member['power'] : 1;
                $coef = isset($member['coef']) && $member['coef'] !== '' ? (int) $member['coef'] : 1;
                if($member['sign'] === '-') {
                    $coef *= -1;
                }
                $this->data[$power][$member['var_name']][] = $coef;
            }           
        }
    }

    /**
     * Prepares expression for parsing
     *
     * @param $expression
     * @return string
     */
    private function prepareExpression($expression): string
    {
        $expression = preg_replace(['/\s+/', '/\*/'], '', $expression);
        if(!preg_match('/^[\+-]/', $expression)) {
            $expression = '+' . $expression;
        }

        return $expression;
    }
}
