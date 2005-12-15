<?

class FDRouterForm extends Form {
  function __construct(Request $r) {
    parent::__construct($r, new Location('FDRouter'));
    $this->addField('demos', new SelectInputField(null, $this->getDemos(), '(please select a demo)'));
  }
  
  static function getDemos() {
    $da = get_instances_of('FDDemo');
    $rv = array();
    foreach($da as $className) {
      $rc = new ReflectionClass($className);
      $rm = $rc->getMethod('getDescription');
      $desc = $rm->invoke(null);
      $rv[$className] = $desc;
    }
    return $rv;
  }
}

?>