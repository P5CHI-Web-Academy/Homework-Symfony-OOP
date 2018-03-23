<?php
/**
 * Copyright (c) 2018.
 *
 *  @author    Alexander Sterpu <alexander.sterpu@gmail.com>
 */

declare(strict_types=1);

require_once('QuadraticEquationSolver.php');

if (isset($_SERVER['argv'][1])) {
    try {
        print_r(\Sterpu\QuadraticEquationSolver::resolve($_SERVER['argv'][1]));
    } catch(Exception $e){
        echo 'Exception: ' . $e->getMessage() . PHP_EOL;
    }
} else {
    echo 'No arguments provided.' . PHP_EOL;
}
