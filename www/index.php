<?php
require_once('src/init.php');
require_once('src/functions.php');

require_once('src/database_functions.php');
require_once('../php/functions.php');


$countryData = getPublisherTotals();

$templateData = array();
$templateData['init'] = $initSettings;


if(isset($_GET['path']) && $_GET['path'] == 'donors') {
  
  if(isset($_GET['donor'])) {
  
    $templateData += array('path' => 'donors', 'name' => $_GET['donor']);
    
    
    
    $currentCountry = array();
    foreach($countryData['countries'] as $country) {
      
      if($country['group_id'] == $_GET['donor']) {
        $currentCountry = $country;
      }
      
    }

    $templateData['current'] = $currentCountry;
  
    $templateData['currentData'] = getSinglePublisher(strval($_GET['donor']));
  
    
    //var_dump($templateData['currentData']['history']);
  
    echo $twig->render('donor.html', $templateData);

  } else {
    $templateData += array('path' => 'donors', 'countries' => $countryData['countries'], 'max_count' => $countryData['max_count']);
    
    echo $twig->render('donors.html', $templateData);

    
  }
  
  
} elseif(isset($_GET['path']) && $_GET['path'] == 'recipients') {

  $templateData += array('path' => 'recipients', 'countries' => $countryData);

  echo $twig->render('recipients.html', $templateData);

} else {

  $templateData += array('path' => 'index', 'countries' => $countryData['countries']);

  echo $twig->render('index.html', $templateData);
  
}




?>
