<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 16:09
 */
$a="open_door";
$b=str_replace("_"," ",$a);
echo $b."<br>";
$c=ucwords($b);
echo $c."<br>";
$end=str_replace(' ','',"$c");
echo $end."<br>";
echo "<hr color='red'>";

$aa="make_by_id";
$bb=str_replace("_"," ",$aa);
echo $bb."<br>";
$cc=ucwords($bb);
echo $cc."<br>";
$end2=str_replace(" ",'',$cc);
echo $end2;
