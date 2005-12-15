<?

/**
 * This is the Links demo to show the simplicity of making links to pages generated by
 * other actions
 * @author Dennis Popel
 */
class FDLinks extends FDDemo {
  function getTitle() {
    return 'Links Demo';
  }
  
  function getPrev() {
    return new Location('FDWelcome');
  }
  
  function getNext() {
    return new Location('FDForms');
  }
  
  /**
   * Here we prepare several links
   */
  function process() {
    parent::process();
    $d = $this->getDocument();
    
    // The first sample link
    $l = new Location('MyAction', array(
      'one'=>1, 
      'two'=>2,
      'three'=>3));
    $d->setVariable('myLink', $l);
    
    // Ten cloned links
    $l = new Location('ViewProduct', array('productid'=>0));
    for($i = 1; $i != 11; $i++) {
      $l2 = clone $l;
      $l2->setParameter('productid', $i);
      $links[] = $l2;
    }
    $d->setVariable('links', new IterableArray($links));
  }

  static function getDescription() {
    return 'Links';
  }
}

?>