<?

/**
 * This class shows source or template of the specified demo.
 *
 * @author Dennis Popel
 */
class FDViewSource extends FDBaseAction {
  function process() {
    parent::process();
    $d = $this->getDocument();
    
    // Here we manually create a region with a single text node that will keep
    // the highlighted source. Normally this should be done in a tag class!
    $r = new HTMLRegion(new HTMLRootNode(new HTMLDocument(new Responce(new Request())), '', 'x'), '', 'HTMLRegion', array('name'=>'source'));
    $r->addNode($n = new HTMLTextNode($r, null));
    
    $demo =  $this->getRequest()->getParameter('demo');
    
    // Here we will try to invoke the static method getDescription of the requested demo
    // If we fail, the description will be the name of the demo class
    try {
      $rc = new ReflectionClass($demo);
      $rm = $rc->getMethod('getDescription');
      $desc = $rm->invoke(null);
    } catch(ReflectionException $re) {
      $desc = $demo;
    }
    
    // See if the request was to show template
    if($this->getRequest()->getParameter('template')) {
      $p = $this->getPackage()->getResourcePath($demo) . '.html';
      $n->setRaw(true);
      $type = 'template';
    } else {
      $n->setRaw(true);
      $p = $this->getPackage()->getPath() . '/' . $demo . '.php5';
      $type = 'source';
    }
    
    // See if the file is accessible, if not, redirect to the Welcome to Demo page
    if(is_file($p)) {
      $n->setContent(highlight_file($p, true));
      $d->setVariable('demo', $demo);
      $d->setRegion('source', $r);
      $d->setVariable('type', $type);
      $d->setVariable('desc', $desc);
    } else {
      $this->getResponce()->relocate(new Location('FDWelcome'));
    }
  }
  
  
  // These methods are internal to the demos system
  function getTitle() {
    return 'View Source';
  }
}

?>