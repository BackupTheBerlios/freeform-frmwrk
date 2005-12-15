<?

/**
 * This class is the Cache Demo. It will display a page with the time of the request.
 * The page will have the lifetime of 5 seconds during which it will be stored in the
 * browser cache. It will also contain a link to refresh itself; clicking on it will
 * NOT force the browser to fetch the page from the server - the generation time will
 * not change during the 5 second period. If you enable server-side cache then
 * you can open the page with another browser to see that Freeform will return
 * the cached page for this another request too.
 * @author Dennis Popel
 */
class FDCache extends FDDemo {
  /**
   * This is the method to execute the demo
   */
  function process() {
    // We always call the parent's method
    parent::process();

    $d = $this->getDocument();
    $r = $this->getResponce();
    
    // Here we set the lifetime of the responce to 5 seconds
    $r->setLifeTime(10);
    
    // Some variables for the template
    $d->setVariable('time', time());  // time of the responce creation
  }
  
  // These methods are internal to the demos system
  function getTitle() {
    return 'Cache Demo';
  }
  
  function getNext() {
    return new Location('FDPI');
  }
  
  function getPrev() {
    return new Location('FDI18N');
  }
  
  static function getDescription() {
    return 'Caching';
  }
}

?>