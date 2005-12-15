<?

/**
 * A generic session handler using php builtin session mechanism. You will have to properly
 * configure PHP sessions to use this class.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
class PHPSessionHandler implements SessionHandler {
  private $expired = false;
  
  static function load($request) {
    session_cache_limiter('');
    if(!session_id()) {
      session_start();
    }
    
    // Here we still can't use our methods
    if(isSet($_SESSION['phpsessionhandler.time'])) {
      $age = time() - $_SESSION['phpsessionhandler.time'];
      $expired = $age > $_SESSION['phpsessionhandler.duration'];
    } else {
      $age = 0;
      $expired = false;
    }
    
    if($expired) {
      session_destroy();
      session_regenerate_id();
    }
    
    // Kill stupid PHP headers
    // header('Pragma:', true);
    // header('Cache-Control: xxx', true);
    
    $impl = Package::getPackageByName('freeform')->getProperty('session.impl');
    $rv = new $impl($expired);
    $rv->setVariable('phpsessionhandler.time', time());
    $rv->setVariable('phpsessionhandler.duration', 1800);
    
    return $rv;
  }
  
  /**
   * Construct a new PHPSessionHandler. Can only be called from the static load() method.
   * @param  bool $expired  the flag to indicate whether the previous session has expired and that a new one has been created
   */
  function __construct($expired) {
    $this->expired = $expired;
  }
    
  /**
   * Check if this session was created after the last one has expired. It means that 
   * all data of the session has been lost.
   * @return  bool  true if the sesison was created after previous one had expired 
   */
  function isExpired() {
    return $this->expired;
  }
  
  /**
   * Set a session variable 
   * @param  string $name  name of the session variable
   * @param  mixed $value  value of the variable
   */
  function setVariable($name, $value) {
    $_SESSION[$name] = $value;
  }
  
  /**
   * Get session variable
   *
   * @param  $name  name of the sessoin variable
   * @return  mixed  value of the session variable
   */
  function getVariable($name) {
    if(isSet($_SESSION[$name])) {
      return $_SESSION[$name];
    } else {
      return null;
    }
  }
  
  /**
   * Get the current user
   * @return  User  current session user or null
   */
  function getUser() {
    return $this->getVariable('phpsessionhandler.user');
  }
  
  
  /**
   * Set the current user
   * @param  User $user  current session user or null to reset
   */
  function setUser($user) {
    $this->setVariable('phpsessionhandler.user', $user);
  }
  
  /**
   * Return the private mode hash or null
   * @return  string  the private mode hash
   */
  function getPrivateMode() {
    return $this->getVariable('phpsessionhandler.hash');
  }
  
  /**
   * Set the private mode hash.
   * @param  string $hash  the hash or null to reset
   */
  function setPrivateMode($hash) {
    $this->setVariable('phpsessionhandler.hash', $hash);
  }
    
}

?>