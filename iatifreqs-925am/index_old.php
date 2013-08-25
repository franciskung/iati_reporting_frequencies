<?php
header('Content-Type: text/html; charset=utf-8');
mb_language('uni'); mb_internal_encoding('UTF-8');

echo '<pre>';

/*

http://www.iatiregistry.org/api/2/rest/group/dfid





*/

$output = array();

$agencies = array(
  array(
    'src'=>'dfid',
    )
  );

foreach($agencies as $agency) {
  
  $input = json_decode(file_get_contents('http://www.iatiregistry.org/api/2/rest/group/'.$agency['src']),1);

  $collect = array();
  $count = 0;
  $latestDate = 0;
  
  
  
  $packages = $input['packages'];
  
  $i = 0;
  foreach($packages as $packageID) {
    
    $packageDetail = json_decode(file_get_contents('http://www.iatiregistry.org/api/2/rest/package/'.$packageID),1);
    
    //var_dump();
    $date = strtotime($packageDetail['metadata_modified']);
    

    
    $collect[] = array(
      'title'=>$packageDetail['title'],
      'count' => $packageDetail['extras']['activity_count'],
      'date' => date('Y-m-d',$date),
      
    );
    $count += $packageDetail['extras']['activity_count'];
    
    
    $i++;
    
    if($i > 3) {
      break;
    }
  }

  var_dump($count);  
  var_dump($collect);

  
}




?>