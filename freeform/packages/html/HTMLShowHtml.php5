<?

/**
 * This tag allows you to display variables that contain markup which you don't want to get escaped. Normally, when you
 * display variable contents in a template with {%varName} notation, content of that variable is passed through 
 * <tt>htmlSpecialChars()</tt> function. If, however, that variable contains HTML that you do want to display, you can
 * use this tag like:
 * <source>
 * ...
 * <HTMLShowHtml key="varName"/>
 * ...
 * </source>
 * Please note, however, that if the variable contains invalid HTML, the validity of the resulting document can change.
 * @since 1.2.0.RC
 * @author Dennis Popel
 */
class HTMLShowHtml extends HTMLTag {
  private $tn = null;
  
  function onInit() {
    $this->addNode($this->tn = new HTMLTextNode($this));
    $this->tn->setRaw(true);
    $this->setExposed(false);
  }
  
  function onOpen() {
    $this->tn->setContent($this->getDocument()->getVariable($this->getAttribute('key')));
    return self::PROCESS_BODY;
  }
}

?>