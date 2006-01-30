<?

/**
 * This tag is used to extract key-value pairs of an array or properties of an object into the current
 * variable stack so that you can access them atomically by the {%varName} construct. Its only
 * attribute, <tt>key</tt>, keeps the name of the template variable that holds the actual 
 * array or object to display.
 * 
 * Since 1.2.0.RC this tag will also call all public getXXXX() methods of the object which do not accept parameters
 * and expose their values thru the corresponding XXXX template variables (for example, the return value of
 * the <tt>$obj::getProperty()</tt> method will be available as the <tt>property</tt> template variable). Please note that the first
 * letter of the template variable will be lowercased.
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
      
      if(is_object($obj)){
        // Loop thru each method to detect it's a getter and include its ret value 
        $rClass = new ReflectionClass($obj);
        foreach($rClass->getMethods() as $rMethod){
          ($mn = $rMethod->getName());
          if($rMethod->isPublic() 
           && ('get' === substr($mn,0,3))
           && (0 == count($rMethod->getParameters()))) {
                $var = subStr($mn,3); //extract the variable name
                $var[0] = strToLower($var[0]); //lower first letter case
        	      $this->getDocument()->setVariable($var, $rMethod->invoke($obj) );
              }
        }      
      }
      return self::PROCESS_BODY;
    } 
    return self::SKIP_BODY;
  }
}

?>