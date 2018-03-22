<?php
    require "controller.php";

    if(isset($_SERVER['argv'])) {
        $eq_str = implode('', array_slice($_SERVER['argv'], 1));
        echo(Solver::resolve($eq_str)."\n");
    } else {
        $eq_str = 'x^2+10x+21=0'; //Input
        ob_start();
        echo(Solver::resolve($eq_str));
        echo nl2br(ob_get_clean());
    }
