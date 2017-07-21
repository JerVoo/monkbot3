#!/usr/bin/php
<?php
echo "\e[1;31m";
echo <<<TXT


    ███╗   ███╗ ██████╗ ███╗   ██╗██╗  ██╗██████╗  ██████╗ ████████╗
    ████╗ ████║██╔═══██╗████╗  ██║██║ ██╔╝██╔══██╗██╔═══██╗╚══██╔══╝
    ██╔████╔██║██║   ██║██╔██╗ ██║█████╔╝ ██████╔╝██║   ██║   ██║   
    ██║╚██╔╝██║██║   ██║██║╚██╗██║██╔═██╗ ██╔══██╗██║   ██║   ██║   
    ██║ ╚═╝ ██║╚██████╔╝██║ ╚████║██║  ██╗██████╔╝╚██████╔╝   ██║   
    ╚═╝     ╚═╝ ╚═════╝ ╚═╝  ╚═══╝╚═╝  ╚═╝╚═════╝  ╚═════╝    ╚═╝   
  
  -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
  
  Version 3 | development edition
  
  -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --


TXT;

if(!version_compare('7.0', PHP_VERSION, '<=')) {
    echo <<<TXT
  Sorry, your PHP version does not meet the requirements for Monkbot 3.
  Please upgrade PHP to at least version 7 in order to continue.
  
  -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
TXT;
    exit;
}

$bootstrapFile = 'lib/Monkbot/Framework/Bootstrap.php';
if(!file_exists($bootstrapFile)) {
    echo <<<TXT
  It seems like Monkbot has not been installed correctly. Please fix
  your installation in order to continue.
  
  -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
TXT;
    exit;
}

require_once 'lib/Monkbot/Framework/Bootstrap.php';

$bootstrap = new \Monkbot\Framework\Bootstrap();
$bootstrap->init()
          ->start();