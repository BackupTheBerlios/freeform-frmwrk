<?

/**
 * This is the 'splash' demo that will display some introductory information
 * @author Dennis Popel
 */
class FDWelcome extends FDDemo {
  function getTitle() {
    return 'Welcome';
  }
  
  /**
   * Here we return null as we have no previous demo
   * @return  Location  null, as we have no 'prev' demo
   */
  function getPrev() {
    return null;
  }
  
  function getNext() {
    return new Location('FDLinks');
  }
  
  static function getDescription() {
    return 'Welcome to demo';
  }
}

?>