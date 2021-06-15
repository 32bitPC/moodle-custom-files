<?php
$array = array(1,2,3,4);
$odds = array();
$even = array();
foreach($array as $val) {
    if($val % 2 == 0) {
        $even[] = $val;
    } else {
        $odds[] = $val;
    }
}

sort($even);
echo "even is ".count($even)."\n";
rsort($odds);
echo "odd is ".count($odds);
$array = array();
foreach($even as $key => $val) {
    $array[] = $val;
    if(isset($odds[$key])) {
        $array[] = $odds[$key];
    }
}
