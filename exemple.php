<?php

/*provenant du formulaire*/
$licence = array(	
  'software'=> 'Iriven MVC System', // Application Name,
  'version'=> 1.04, // Application Version
  'copyright'=> '&copy; Iriven France', // you can limit the key to per domain
  'username'=> 'mon client', // you can limit the key to per user name or compagny
  'uniqid'=> 1025, // add if any (user id)
  'domain'=> 'monclient.com', // you can limit the key to per domain
  'expiration'=> '1390389563', // [time()+(30*24*3600)]; (30 days) you can limit the key to per expiration time
  'algorithm'=>'md5',
  'serial'=>'736CC-AFB84-9B6C0-65252-6C107',//by key generator
  'salt'=>pack('H*', md5(time())),
  'lastvalidation' => 98745612 // default: time(), execution du script pour la 1ere fois
);
$ini = new Iriven\ConfigManager('./licence.ini');
$ini->set('licence',$licence); //with section
$test = array('test1'=>'my tester1','test2'=>'my tester2','test3'=>'my tester3');			
$ini->set($test);	//no section	
$ini->set('repo','centos');	//no section	
$ini->set('linux','repo','fedora');	//with section

echo $ini->get('test1').PHP_EOL;

echo $ini->get('test2').PHP_EOL;

echo $ini->get('test3').PHP_EOL;



echo '<pre>';
print_r($ini->get('licence'));
echo '</pre>';


echo '<pre>';
print_r($ini->get());
echo '</pre>';
