<?

/**
 * This is a conditional tag that will display its body only if the specified form input field
 * did not validate (was filled incorrectly). It can be used only within the <class>HTMLShowForm</class>
 * tag. Its sole attribute, <tt>key</tt> denotes the name of the input filed in the form.
 * @since 1.1.0
 * @author Dennis Popel
 */
class HTMLIfInputFieldError extends HTMLTag {
  function onInit() {
    $this->setExposed(false);
  }
  
  function onOpen() {
    $fld = $this->getDocument()->getVariable($this->getAttribute('key'));
    if($fld instanceof InputField && $fld->getForm()->isSubmitted() && !$fld->getForm()->isValid()) {
      return $fld->isValid() ? self::SKIP_BODY : self::PROCESS_BODY;
    }
    return self::SKIP_BODY;
  }
}

?>