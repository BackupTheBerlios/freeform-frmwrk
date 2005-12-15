<?

/**
 * This class is the Repeatable and Conditional Content Demo. It shows how easy it is 
 * to show iterable content, show/hide certain parts of the template and reuse regions.
 * @author Dennis Popel
 */
class FDRCContent extends FDDemo {
  /**
   * This is the method to execute the demo
   */
  function process() {
    // We always call the parent's method
    parent::process();
    $d = $this->getDocument();
    
    // First define some data
    $widgets = array(
      array(
        'title'=>'Old Widget',
        'desc'=>'A plain old widget (good for nothing)',
        'price'=>0.95),
      array(
        'title'=>'Better Widget',
        'desc'=>'Well you could use it somehow after all',
        'price'=>1.95),
      array(
        'title'=>'Cool Widget',
        'desc'=>'Almost good for everything',
        'price'=>2.95),
      array(
        'title'=>'Ein Widgett',
        'desc'=>'A Germany-made widget, price is high because of import restrictions',
        'price'=>3.95),    
      array(
        'title'=>'SuperWidgett',
        'desc'=>'So super we don\'t know anything about it',
        'price'=>5.95));
        
    $gadgets = array(
      array(
        'title'=>'Old Gadget',
        'desc'=>'A plain old gadget (good for nothing)',
        'price'=>0.99),
      array(
        'title'=>'Better Gadget',
        'desc'=>'Well you could use it somehow after all',
        'price'=>1.99),
      array(
        'title'=>'Cool Gadget',
        'desc'=>'Almost good for everything',
        'price'=>2.99),
      array(
        'title'=>'El Gagette',
        'desc'=>'A Mexico-made gadget, price is high because of import restrictions',
        'price'=>3.99),    
      array(
        'title'=>'SuperGadget',
        'desc'=>'So super we don\'t know anything about it',
        'price'=>5.99));       
        
    // Now prepare the plain iterable array
    $d->setVariable('widgets', new IterableArray($widgets));
    
    // Now duplicate it as the above iterable will be forwarded after the first display
    // and will report it has no more elements on the second invokation
    $d->setVariable('widgets1', new IterableArray($widgets));
    
    // The second iterable array for showing the regions
    $d->setVariable('gadgets', new IterableArray($gadgets));
  }
  
  // These methods are internal to the demos system
  function getTitle() {
    return 'Repeatable and Conditional Content Demo';
  }
  
  function getPrev() {
    return new Location('FDForms');
  }
  
  function getNext() {
    return new Location('FDI18N');
  }
  
  static function getDescription() {
    return 'Repeatable and Conditional Content';
  }
}

?>