<?

class FDRouter extends Action {
  function process() {
    $rf = new FDRouterForm($this->getRequest());
    if($rf->isValidSubmission()) {
      $this->getResponce()->relocate(new Location($rf->getField('demos')->getValue()));
    } else {
      $this->getResponce()->relocate(new Location('FDWelcome'));
    }
  }
}

?>