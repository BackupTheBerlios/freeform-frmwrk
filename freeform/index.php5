<?

// This is the front controller for all requests to your web application.
// This file is responsible for loading class files, instantiating ActionController
// and handling fatal errors. The lower-level configuration is stored here too.

// COPY THIS FILE TO A WEB SERVER DOC ROOT DIRECTORY

// SET THIS TO WHEREEVER YOU INSTALLED FREEFORM
$FREEFORM_HOME = './';

// Here we prepare the whole framework to be UTF-8 only
mb_regex_encoding('UTF-8');
mb_internal_encoding('UTF-8');
//setlocale(LC_ALL, 'C');

// UNCOMMENT THE FOLLOWING LINES TO MANUALLY SET ERROR LOGGING (normally this is done in php.ini)
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', 'phperror.log');









// ----------------------------------------
// DO _NOT_ CHANGE ANYTHING BELOW THIS LINE
// ----------------------------------------
// Sorry, no more comments in this file
require_once($FREEFORM_HOME . '/rtl.php5');
error_reporting(E_ALL);
ob_start();
$pr = $FREEFORM_HOME . '/packages/';

// Class autoload stub
function __autoload($className) {
  global $FREEFORM_CLASS_FILES;
  if($path = @$FREEFORM_CLASS_FILES[$className]) {
    require_once($path);
  }
}

$FREEFORM_PACKAGES = array();
$spis = array();
foreach(glob($pr . '/{*,*.*}', GLOB_BRACE | GLOB_ONLYDIR) as $dirName) {
  $incPaths[] = $dirName;
  // !!! GLOB() issue - return multiple entries for abc.def-like names !!!
  if(!in_array($bn = baseName($dirName), $FREEFORM_PACKAGES)) {
    $FREEFORM_PACKAGES[] = baseName($dirName);
    if(is_file($spiName = $dirName . '/.spi')) {
      $spis[] = $spiName;
    }  
  }
  foreach(glob($dirName . '/*.php5') as $fileName) {
    $FREEFORM_CLASS_FILES[baseName($fileName, '.php5')] = $fileName;
  }
}

// Load all SPIs
try {
  foreach($spis as $spi) {
    $classes = file($spi);
    foreach($classes as $className) {
      if($c = trim($className)) {
        __autoload($c);
      }
    }  
  }
  
  ob_end_clean();

  //$acc = new ReflectionClass('ActionController');
  //$ac = $acc->newInstance();
  //$ac->process();
  ActionController::processHTTP();
} catch(Exception $e) {
  header('Content-Type: text/plain');
  header('X-Freeform-Service-Status: 210');
  echo "FATAL ERROR\r\n";
  echo "-----------\r\n\r\n";
  echo "Exception:     " . get_class($e) . "\r\n";
  echo "Error message: " . $e->getMessage() . "\r\n";
  echo "Error code:    " . $e->getCode() . "\r\n";
  echo "In:\r\n";
  echo $e->getTraceAsString();
  error_log($e->__toString(), 0);
}

?>