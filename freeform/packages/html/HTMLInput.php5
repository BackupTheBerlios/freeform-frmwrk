<?

/**
 * This tag displays a single input field of the form. The only required is
 * the <tt>name</tt> attribute that specifies the name of the template variable that holds the 
 * <class>InputField</class> object. Along this attribute you may specify styling and scripting
 * attributes that will be preserved in the resulting HTML tag. Note that you cannot specify
 * the <tt>type</tt> or <tt>value</tt> attributes in the tag. 
 * 
 * This tag can only be used within the <class>HTMLShowForm</class> tag.
 * @since 1.1.0
 * @author Dennis Popel
 */
class HTMLInput extends HTMLTag {
  function onOpen() {
    $this->removeAll();
    
    $key = $this->getAttribute('name');
    $fld = $this->getDocument()->getVariable($key);
    $this->setName('input');
    
    if($fld instanceof TextInputField) {
      if($fld->getType() == TextInputField::TEXTAREA) {
        $this->setName('textarea');
        $this->addNode(new HTMLTextNode($this, $fld->getValue()));
      } else {
        $this->setAttribute('type', $fld->getType() == TextInputField::TEXT ? 'text' : 'password');
        $this->setAttribute('value', htmlSpecialChars($fld->getValue()));
      }
    } elseif($fld instanceof CheckBoxInputField) {
      $this->setAttribute('type', 'checkbox');
      $this->setAttribute('value', '1');
      if($fld->getValue()) {
        $this->setAttribute('checked', 'true');
      }
      $t = new HTMLTag($this, 'input', array());
      $t->setAttribute('value', '');
      $t->setAttribute('type', 'hidden');
      $t->setAttribute('name', $key);
      $this->getDocument()->process($t);  
    } elseif($fld instanceof SelectInputField) {
      $this->setName('select');
      $this->setAttribute('size', '1');
      
      if($e = $fld->getEmpty()) {
        $t = new HTMLTag($this, 'option', array());
        $t->setAttribute('value', '');
        $t->addNode($tn = new HTMLTextNode($this, $e));
        $this->addNode($t);
      }
        
      foreach($fld->getData() as $k=>$v) {
        $t = new HTMLTag($this, 'option', array());
        $t->setAttribute('value', $k);
        if($fld->getValue() == $k) {
          $t->setAttribute('selected', 'true');
        }
        $t->addNode($tn = new HTMLTextNode($this, $v));
        $this->addNode($t);
      }
    } elseif($fld instanceof UploadFileInputField) {
      $this->setAttribute('type', 'file');
    } elseif($fld instanceof RadioButtonInputField) {
      $this->setAttribute('type', 'radio');
      $this->setAttribute('value', $v = $fld->getNextValue());
      if($v == $fld->getValue()) {
        $this->setAttribute('checked', 'true');
      }
    } else {
      return self::SKIP_BODY;
    }
        
    return self::PROCESS_BODY;
  }
}

?>