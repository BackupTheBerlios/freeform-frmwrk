<?

/**
 * This is the tag to show input form in the <class>HTMLDocument</class>. In the template, open
 * the form like:
 *
 * <html>
 * <HTMLShowForm key="(name of the template variable that holds Form object)">
 *   <HTMLInput name="(name of the template variable that holds InputField object)"/>
 *   ...
 * </HTMLShowForm>
 * </html>
 *
 * Note that you will have to prepare template variable and declare the 
 * HTMLShowForm's key attribute to be the name of the variable. You can also include the
 * styling and scripting attributes, but not the <tt>method</tt>, <tt>enctype</tt> or
 * <tt>action</tt> attributes.
 *
 * You use the <class>HTMLInput</class> tags to display input fields. Note that the only required
 * attribute is "name" that must match the name of the field as it was added to
 * the form. The HTMLInput will detect the InputField's type and supply corresponding
 * html code to display textares, passwords, check boxes, file upload and select fields.
 *
 * @since 1.0.0
 * @author Dennis Popel
 */
class HTMLShowForm extends HTMLTag {
  private $key = NULL;
  private $form = NULL;
  
  // We will keep here all hidden fields added by us so we can silently remove them
  // in onClose() call.
  private $parameters;
  
  // Just shortcuts to these tags
  private $ifError = NULL;
  private $ifInputError = NULL;
  
  private $wml = false;
  
  function onOpen() {
    $this->wml = $this->getDocument() instanceof WMLDocument;
    
    if(!$this->key) {
      $this->key = $this->getAttribute('key');
      $this->removeAttribute('key');
    }
    
    $this->form = $this->getDocument()->getVariable($this->key);
    if(is_null($this->form)) {
      return self::SKIP_BODY;
    }
    
    $this->parameters = new HTMLTag($this, 'div', array());
    $this->parameters->setExposed(false);
    
    if($this->ifError = $this->getTagByName('iferror')) {
      $this->ifError->setExposed(false);
    }
    if($this->ifInputErrors = $this->getTagByName('ifinputerrors')) {
      $this->ifInputErrors->setExposed(false);
    }
    
    if($this->wml) {
      $this->setName('fieldset');
      $this->action->name = 'postfield';
      $do = new HTMLTag($this, 'do', array());
      $do->setAttribute('type', 'Accept');
      $go = new HTMLTag($this, 'go', array());
      $go->setAttribute('href', '?');
      $go->setAttribute('method', $this->form->getMethod() == Request::METHOD_POST ? 'post' : 'get');
      $go->addNode($this->parameters);
      $do->addNode($go);
      $this->addNode($do);
    } else {
      $this->setAttribute('method', $this->form->getMethod() == Request::METHOD_POST ? 'post' : 'get');
      if($this->getAttribute('method') == 'post') {
	       $this->setAttribute('enctype', 'multipart/form-data');
      }
      $this->setName('form');
      $this->setAttribute('action', Request::$URL); 
      // Set the HTML4.01 attribute accept-encoding if we generate 4.01 HTML pages
      // !!! Must use version_compare !!!
      if(Package::getPackage($this)->getProperty('html.version') == '4.01') {
        $this->setAttribute('accept-charset', 'UTF-8');
      }
      $this->addNode($this->parameters);
    }
		
    // Pass over parameters
    $pars = $this->form->getParameters();
    
    if($this->wml) {
      foreach(array_keys($this->form->getFields()) as $fname) {
        $pars[$fname] = '$(' . $fname . ')';
      }
    }
    
    $pars['action'] = $this->form->getLocation()->getAction();
    foreach($pars as $k=>$v) {
      $a = new HTMLTag($this, $this->wml ? 'postfield' : 'input', array());
      $a->setAttribute('name', $k);
      $a->setAttribute('value', htmlSpecialChars($v));
        
      if(!$this->wml) {
        $a->setAttribute('type', 'hidden');
      }
      $this->parameters->addNode($a);
    }
      
    if($this->ifInputErrors) {
      $this->ifInputErrors->setEnabled(
        $this->form->isSubmitted() && !$this->form->isValid());
    }
      
    return self::PROCESS_BODY;
  } 
  
  function onBeforeBody() {
    $fields = $this->form->getFields();
    foreach($fields as $k=>$v) {
      $this->getDocument()->setVariable($k, $v);
    }
  }
  
  function onClose() {
    $this->removeNode($this->parameters);
  }
}

?>