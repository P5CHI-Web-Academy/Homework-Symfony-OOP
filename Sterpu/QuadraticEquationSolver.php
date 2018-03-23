<?php
/**
 * Copyright (c) 2018.
 *
 *  @author    Alexander Sterpu <alexander.sterpu@gmail.com>
 */

declare(strict_types=1);

namespace Sterpu;

require_once('PolynomialExpression.php');


class QuadraticEquationSolver
{
    public const SOLVED_SUCCESS = 8;
    public const SOLVED_TWO_ROOTS = 4;
    public const SOLVED_ONE_ROOT = 2;
    public const SOLVED_NO_ROOTS = 1;

    public const ERROR_NOT_QUADRATIC = 2;
    public const ERROR_NOT_SINGLE_IND = 1;

    /**
     * Prevent class instances from being created.
     */
    private function __construct(){}
    
    /**
     * Solves the quadratic expression
     *
     * @param string $expression
     * @return array
     * @throws \Exception
     */
    public static function resolve(string $expression) : array
    {
        $polynomial = self::getPolynomialExpression($expression);

        $status = self::checkIfQuadraticSinglePolynomial($polynomial);
        if($status['errors']) { // TODO: Create different types of exceptions
            throw new \Exception(self::interpretErrorMsg($status['errors']));
        }
        // TODO: Create abstract status msg interpreter
        return self::solveQuadraticSinglePolynomial($polynomial);
    }

    /**
     * @param PolynomialExpression $polynomial
     * @return array
     */
    protected static function solveQuadraticSinglePolynomial(PolynomialExpression $polynomial) : array
    {
        $ind_name = $polynomial->getIndeterminateNames()[0];
        // Get a, b and c
        $ax = $polynomial->getMemberCoefficient(2, $ind_name);
        $bx = $polynomial->getMemberCoefficient(1, $ind_name);
        $c = $polynomial->getMemberCoefficient(0);

        $result = ['status' => self::SOLVED_SUCCESS];
        // Find all possible solutions
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

    /**
     * @param string $expression
     * @return PolynomialExpression
     */
    protected static function getPolynomialExpression(string $expression) : PolynomialExpression
    {
        $parts = explode('=', $expression);
        // Combines two parts of an expression
        return (new PolynomialExpression($parts[0]))->combine(
            (new PolynomialExpression($parts[1]))->switch()
        );
    }

    /**
     * @param PolynomialExpression $polynomial
     * @return array
     */
    protected static function checkIfQuadraticSinglePolynomial(PolynomialExpression $polynomial) : array
    {
        $status = ['errors' => 0];
        if($polynomial->getDegree() !== 2) {
            $status['errors'] |= self::ERROR_NOT_QUADRATIC;
        }
        // Check if we have just one indeterminate variable
        if(count($polynomial->getIndeterminateNames()) !== 1) {
            $status['errors'] |= self::ERROR_NOT_SINGLE_IND;
        }

        return $status;
    }

    /**
     * @param $errors
     * @return string
     */
    private static function interpretErrorMsg($errors) : string
    {
        // TODO: Create abstract error msg interpreter; different types of exception
        $msg = '';
        if($errors & self::ERROR_NOT_QUADRATIC) {
            $msg .= 'Provided expression isn\'t quadratic polynomial.' . PHP_EOL;
        }
        if($errors & self::ERROR_NOT_SINGLE_IND) {
            $msg .= 'Provided expression isn\'t a polynomial of a single indeterminate.' . PHP_EOL;
        }

        return $msg;
    }
}
