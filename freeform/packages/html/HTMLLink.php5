<?

/**
 * This class represents a tag that creates a hyperlink to another action. The only required 
 * attribute is the <tt>key</tt> that specifies the name of the template variable that holds
 * a <class>Location</class> object of the destination action. You may also include styling
 * and scripting attributes that will be preserved in the resulting <tt>a</tt> tag.
 * @since 1.1.0
 * @author Dennis Popel
 */ 
class HTMLLink extends HTMLTag {
  private $key = '';
  private $action = '';
  
  function onInit() {
    $this->setName('a');
  }
  
  /**
   * This method is called from the <method>onOpen</method> to retrieve the
   * location the link points to. By default, this is done by searching the
	 * <tt>key</tt> attribute to find a <class>Location</class> object, and, if
	 * it fails, by analyzing the <tt>action</tt> attribute to construct a
	 * location of given <class>Action</class> without parameters. You override 
	 * this in subclasses if the link should be generated in a different way
	 *
	 * @return  Location  the location to create link to
	 * @since  1.1.2
	 */
  function getLocation() {
    if($l = $this->getDocument()->getVariable($this->key)) {
      return $l;
    } elseif($this->action) {
      return new Location($this->action);
    } else {
      return null;
    }
  }
  
  function onOpen() {
    $this->key = $this->getAttribute('key');
    $this->action = $this->getAttribute('action');
    $this->removeAttribute('action');    
    $this->removeAttribute('key');
    
    $l = $this->getLocation();
    if(!is_null($l)) {
      $this->setAttribute('href', htmlSpecialChars($l->toURL(), ENT_QUOTES, 'UTF-8'));
      return self::PROCESS_BODY;
    } else {
      return self::SKIP_BODY;
    }
  }
  
  function onClose() {
    $this->setAttribute('key', $this->key);
    $this->setAttribute('action', $this->action);
  }
}

?>