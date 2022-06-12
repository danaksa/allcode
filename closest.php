<?php
function closest($arr,$num){
  $tmp = array();
  foreach($arr as $val){
    $tmp[$val] = abs($val - $num);
  }
  asort($tmp);
  return key($tmp);
}
