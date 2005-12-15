<?

/**
 * This is the base class for every demo action in this package. It will take care of 
 * setting up the router (quick jump) form and preparing the 'next' and 'prev' links
 * @author Dennis Popel
 */
abstract class FDDemo extends FDBaseAction {
  function onInit() {
    parent::onInit();
    $demos = FDRouterForm::getDemos();
    $d = $this->getDocument();
    $d->setVariable('prev', $prev = $this->getPrev());
    if($prev) {
      $d->setVariable('prevTitle', $demos[$prev->getAction()]);
    }
    $d->setVariable('next', $next = $this->getNext());
    if($next) {
      $d->setVariable('nextTitle', $demos[$next->getAction()]);
    }
    $d->setVariable('routerForm', new FDRouterForm($this->getRequest()));
    $d->setVariable('viewSource', new Location('FDViewSource', array('demo'=>get_class($this))));
    $d->setVariable('viewTemplate', new Location('FDViewSource', array('demo'=>get_class($this), 'template'=>'1')));
  }
  
  /**
   * This is called to get the 'next' demo in the wizard-like style
   * @return  Location  the 'next' demo
   */
  abstract function getNext();
  
  /**
   * This is called to get the 'previous' demo in the wizard-like style
   * @return  Location  the 'previous' demo
   */
  abstract function getPrev();
  
  /**
   * This method will be called statically via reflection to get the descrtiption
   * of the demo for the drop down list
   * @return  string  the description of the demo
   */
  static function getDescription() {}
}

?>