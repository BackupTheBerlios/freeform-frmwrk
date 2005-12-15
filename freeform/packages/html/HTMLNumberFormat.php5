<?

/**
 * This tag is used to format numbers and prices in locale-specific manner.
 * It tries to get the document's locale and use it to format the number. The number
 * to format is denoted by the <tt>key</tt> attribute that should hold the name of
 * the template variable containing the value. Immediate value can be specified by the
 * <tt>value</tt> attribute. By setting the <tt>mode</tt> attribute to <i>price</i>, this
 * tag will format the value as if it were price.
 * @since Freeform Framework 1.2.0.Alpha
 * @author Dennis Popel
 */
class HTMLNumberFormat extends HTMLTag {
  /**
   * This tag is never exposed
   * @return  bool  false, as this tag is never exposed
   */
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    $this->removeAll();
    $d = $this->getDocument();
    $l = $d->getLocale();
    $k = $this->getAttribute('key');
    $value = $d->getVariable($k, $this->getAttribute('value'));
    
    if($l) {
      if($this->getAttribute('mode') == 'price') {
        $rv = $l->formatCurrency($value);
      } else {
        $rv = $l->formatNumber($value);
      }
    } else {
      $rv = $value;
    }
      
    
    $tn = new HTMLTextNode($this);
    $tn->setContent($rv);
    $this->addNode($tn);
    return self::PROCESS_BODY;
  }
  
  function onClose() {
    
  }
}

?>