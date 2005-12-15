<?

/**
 * This is an example action that will get executed if there is no
 * default action configured on the server. It displays a welcome screen.
 */
class WelcomeToFreeform extends Action {
  function process() {
    $d = new HTMLDocument($this->getResponce());
    $d->setTemplate($this->getPackage()->getResourcePath('WelcomeToFreeform.html'));
    if($p = Package::getPackageByName('freddy')) {
      $d->setVariable('freddy', new Location('Freddy'));
    }
    if($p = Package::getPackageByName('freeformdemo')) {
      $d->setVariable('demo', new Location('FDWelcome'));
    }
    $this->getResponce()->setDocument($d);
  }
}

?>