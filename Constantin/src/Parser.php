<?php

/**
 * Parse polynom from string expression
 */
class Parser
{
    const POLYNOM_REGEX = '/[+-]?(?:(?:\w+\^\d+)|(?:\d+\w+\^\d+)|(?:\d+\w+)|(?:\d+)|(?:\w+))/';
    const COEFFICIENT_REGEX = '/[-+]?(\d+)(?=\D)/';
    const POWER_REGEX = '/\d+(?!\w+)/';
    const VARIABLE_REGEX = '/[a-z]/i';

    /**
     * @param $expression
     * @return Polynom
     * @throws Exception
     */
    public static function parse($expression)
    {
        $expression = str_replace([' ', '*'], '', $expression);
        $parts = explode('=', $expression);

        if (count($parts) !== 2) {
            throw new \InvalidArgumentException(sprintf('Invalid equation: %s', $expression));
        }

        $parts1 = self::parseExpression($parts[0]);
        if (empty($parts1)) {
            throw new \Exception(sprintf('Could not parse expression: %s', $parts[0]));
        }

        $parts2 = self::parseExpression($parts[1]);
        if (empty($parts2)) {
            throw new \Exception(sprintf('Could not parse expression: %s', $parts[1]));
        }

        return new Polynom($parts1, $parts2);
    }

    /**
     * @param string $expression
     * @return array
     * @throws Exception
     */
    public static function parseExpression(string $expression)
    {
        $matchResult = self::pregMatchAll(self::POLYNOM_REGEX, $expression);
        $result = [];

        if (0 === count($matchResult)) {
            return $result;
        }

        foreach ($matchResult as $item) {

            $variable = '';
            $variableResult = self::pregMatchAll(self::VARIABLE_REGEX, $item);
            if (isset($variableResult[0])) {
                $variable = $variableResult[0];
            }

            $power = 1;

            if (empty($variable)) {
                $coefficient = floatval($item);
            } else {
                $powerResult = self::pregMatchAll(self::POWER_REGEX, $item);
                if (isset($powerResult[0])) {
                    $power = $powerResult[0];
                }

                $coefficient = 1;
                $coefficientResult = self::pregMatchAll(self::COEFFICIENT_REGEX, $item);
                if (isset($coefficientResult[0])) {
                    $coefficient = $coefficientResult[0];
                }
            }

            $result[] = new Monom($coefficient, $variable, $power);
        }

        return $result;
    }

    /**
     * @param string $regex
     * @param string $expression
     * @return array
     */
    protected static function pregMatchAll(string $regex, string $expression)
    {
        $matches = [];
        if (!preg_match_all($regex, $expression, $matches)) {
            return [];
        }

        return $matches[0];
    }
}
