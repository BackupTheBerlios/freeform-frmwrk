<?

/**
 * This tag is used to extract key-value pairs of an array or properties of an object into the current
 * variable stack so that you can access them atomically by the {%varName} construct. Its only
 * attribute, <tt>key</tt>, keeps the name of the template variable that holds the actual 
 * array or object to display.
 * @since 1.0.0
 * @author Dennis Popel
 */
class HTMLShowObject extends HTMLTag {
  
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    $key = $this->getAttribute('key');
    $obj = $this->getDocument()->getVariable($key);
    if(is_object($obj) || is_array($obj)) {
      foreach($obj as $k=>$v) {
        $this->getDocument()->setVariable($k, $v);
      }
      if(is_object($obj)) {
        // Loop thru each method to detect it's a getter and include its ret value 
        foreach(get_class_methods($obj) as $mn) {
	  if(preg_match('/^get(.+)/', $mn, $m)) {
	    $m[1][0] = strToLower($m[1][0]);
	    $this->getDocument()->setVariable($m[1], $x = $obj->$mn());
	    // error_log( "$m[1]=$x");
	  }
	}
      }	  
      return self::PROCESS_BODY;
    } 
    return self::SKIP_BODY;
  }
}

?>