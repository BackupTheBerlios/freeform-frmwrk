<?

// This file is a collection of emulated functions that are needed by the
// framework but are not PHP standart functions. 

function strip_slashes_gpc(&$value, $key) {
  $value = stripSlashes($value);
}

// This is a hack that creates an instance of class without calling the
// class constructor 
function create_class_instance($className) {
  __autoload($className);
  return unSerialize('O:' . strLen($className) . ':"' . $className . '":0:{}');
}

function get_instances_of($classOrInterfaceName, $includeTopName = false) {
  $ct = get_declared_classes();
  $rv = array();
  try {
    $rc = new ReflectionClass($classOrInterfaceName);
  } catch(ReflectionException $re) {
    return array();
  }
  if($includeTopName) {
    $rv[] = $classOrInterfaceName;
  }
  foreach($ct as $name) {
    if(strToLower($name) != strToLower($classOrInterfaceName)) {
      $rcc = new ReflectionClass($name);
      if($rcc->isSubclassOf($rc)) {
        $rv[] = $name;
      }
    }
  }
  return $rv;
}

?>