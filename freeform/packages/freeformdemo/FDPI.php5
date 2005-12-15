<?

/**
 * This class is the PHP Processing Instructions Demo. It will present a page, part
 * of which will be produced by the PHP code embedded into the template. 
 * @author Dennis Popel
 */
class FDPI extends FDDemo {
  /**
   * This is the method to execute the demo
   */
  function process() {
    // We always call the parent's method
    parent::process();

    $d = $this->getDocument();
    $values = array('Snoopy', 'Sally', 'Chuck', 'Linus');
    $d->setVariable('values', new IterableArray($values));
  }
    
  
  // These methods are internal to the demos system
  function getTitle() {
    return 'Processing Instructions Demo';
  }
  
  function getNext() {
    // return new Location('FDLinks');
    return null;
  }
  
  function getPrev() {
    return new Location('FDCache');
  }
  
  static function getDescription() {
    return 'Processing Instructions';
  }
}

?>