<?php
require_once('src/init.php');
require_once('src/functions.php');

require_once('src/database_functions.php');
require_once('../php/functions.php');




$templateData = array();
$initSettings['orgs'] = $org_types;
$templateData['init'] = $initSettings;


if(isset($_GET['path']) && $_GET['path'] == 'donors') {

  $countryData = getPublisherTotals();
  
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
  
    $sortedCountries = array();
  
    foreach($countryData['countries'] as $country) {
      
      $sortedCountries[$country['total']] = $country;
      
    }
    krsort($sortedCountries);
    
    //var_dump($sortedCountries);
  
  
    $templateData += array('path' => 'donors', 'countries' => $sortedCountries, 'max_count' => $countryData['max_count']);
    
    echo $twig->render('donors.html', $templateData);

    
  }
  
  
} elseif(isset($_GET['path']) && $_GET['path'] == 'recipients') {

  $countryData = getPublisherTotals();

  $templateData += array('path' => 'recipients', 'countries' => $countryData);

  echo $twig->render('recipients.html', $templateData);

} else {

  

  if(isset($_GET['filter']) && intval($_GET['filter']) != 0) {
  
    $templateData['filterNumber'] = intval($_GET['filter']);
    
    $templateData['filterName'] = $templateData['init']['orgs'][intval($_GET['filter'])];
    
    $countryData = getPublisherTotals(intval($_GET['filter']));
    
  }
  else {
    $templateData['filterNumber'] = 0;
      
    $countryData = getPublisherTotals();
  }

  $templateData += array('path' => 'index', 'countries' => $countryData['countries']);

  echo $twig->render('index.html', $templateData);
  
}




?>
