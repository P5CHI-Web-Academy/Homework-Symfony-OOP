<?php

declare(strict_types=1);

if (isset($_SERVER['argv'][1])) {
    $equation = $_SERVER['argv'][1];
} else {
    echo 'No argument provided'.PHP_EOL;
    exit();
}

require_once 'src/Autoloader.php';

Autoloader::register();

try {

    $polynom = Parser::parse($equation);
    $polynom->normalize();

    $solver = new Solver($polynom);
    $result = $solver->solve()->getResult();

    echo $polynom.PHP_EOL;
    echo $result->getDescription().PHP_EOL;
    foreach ($result->getSolutions() as $solution) {
        echo $solution.PHP_EOL;
    }

} catch (\Exception $exception) {
    echo $exception->getMessage().PHP_EOL;
}
