<?php
    require "controller.php";

    $tests = array(
        //Positive normal
        'x=1' => 'x=1',
        'x-1=0' => 'x=1',
        '2x=4' => 'x=2',
        '2x=3' => 'x=1.5',
        '0.2x=2' => 'x=10',
        '0,2x=2' => 'x=10',
        '2x-4=0' => 'x=2',
        '2x+3x=10' => 'x=2',
        '2x+3x-10=0' => 'x=2',
        '4x^2=16' => 'x1=-2, x2=2',
        'x^2+10x+21=0' => 'x1=-7, x2=-3',
        '10x+21=-x^2' => 'x1=-7, x2=-3',
        '6x+x^2+4x+3x^2-3x^2=-21' => 'x1=-7, x2=-3',
        '2x*x=8' => 'x1=-2, x2=2',

        //Positive unformatted
        '1 =    X' => 'x=1',
        '0    = -X-1' => 'x=-1',
        'y*2=4' => 'y=2',
        '4 x ^   2 = 16  ' => 'x1=-2, x2=2',
        '2x^1 = 8' => 'x=4',
        '8=2x^1' => 'x=4',

        //Negative
        'x' => 'Invalid equation',
        'xx=1' => 'Invalid equation',
        'x^^2=16' => 'Invalid equation',
        'x^11=16' => 'Invalid equation',
        '0..2x=2' => 'Invalid equation',
        '1' => 'Invalid equation',
        'qwerty = x' => 'Invalid equation',
        'x+y=2' => 'Invalid equation',
        '4x^3=16' => 'Invalid equation',
        'x--1=0' => 'Invalid equation',
        'x++1=0' => 'Invalid equation',
        'x=2=x' => 'Invalid equation'
    );

    if(isset($_SERVER['argv']))
    {
        $mask = "|%25s  |%-20s  |  %-20s  |\n";
        printf($mask, 'Case','Result','Expected');
        foreach ($tests as $test => $expected_result)
        {
            printf($mask,$test,Solver::resolve($test),$expected_result);
        }
        die();
    }
?>

<table border="1" style="border-collapse: collapse;">
    <tr>
        <th>Case</th>
        <th>Result</th>
        <th>Expected</th>
    </tr>
    <?php
        ob_start();
        foreach ($tests as $test => $expected_result) {
            echo("<tr>");
                echo("<td>".$test."</td><td>".Solver::resolve($test)."</td><td>".$expected_result."</td>");
            echo("</tr>");
        }
        echo nl2br(ob_get_clean());
    ?>
</table>
