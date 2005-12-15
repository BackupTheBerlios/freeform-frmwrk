<?

/**
 * This is a conditional tag that will display its body only if the specified form is 
 * invalid. The form is denoted by the <tt>key</tt> attribute, which holds the template 
 * variable name of the actual form.
 * @since 1.1.0
 * @author Dennis Popel
 */
class HTMLIfInputFieldErrors extends HTMLTag {
  function onInit() {
    $this->setExposed(false);
  }
  
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    $f = $this->getDocument()->getVariable($this->getAttribute('key'));
    if($f && $f->isSubmitted() && !$f->isValid()) {
      return self::PROCESS_BODY;
    }
    return self::SKIP_BODY;
  }
}

?>