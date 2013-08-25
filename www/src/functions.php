<?php

function getCountryTotals() {
  
  
  $countries = array(
    array(
      'name' => 'Finland',
      'iso' => 'fi',
      'updated' => '2013-04-01',
      'added' => '2',
      'total' => '203'
      ),
    array(
      'name' => 'Canada',
      'iso' => 'ca',
      'updated' => '2013-01-11',
      'added' => '43',
      'total' => '1013'
      ),
  );
  $max_count = '1013';
  
  
  return array(
    'countries' => $countries,
    'max_count' => $max_count
  );
  
}







function toAscii($str, $replace=array(), $delimiter='-') {
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
}


?>