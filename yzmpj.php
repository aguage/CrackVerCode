<?php

include ('Valite.php');

$valite = new Valite();
//$yzm = file_get_contents('http://ht.gqtp.com/index.php/Login/getcode/rnd/0.26073557664365077');
//$yzm2 = file_get_contents('http://52.68.32.109:8202/getVcode/.auth?t=c9054622163408&systemversion=4_6&.auth');
//file_put_contents('yzm.png', $yzm);
//file_put_contents('yzm2.png', $yzm2);
$valite->setImage('yzm.png');
//http://ht.gqtp.com/index.php/Login/getcode/rnd/0.26073557664365077
$valite->getHec();
$ert = $valite->run();
//$ert = "1234";
print_r($ert);
echo '<br><img src="yzm.png"><br>';
