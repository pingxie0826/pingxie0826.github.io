<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 16:20
 */
$str="abcd<script>alert(1)</script>";
echo $str."<br>";
$pattern="/<script>.*?<\/script>/i";
$end=preg_replace($pattern,'',$str);
//echo $end;