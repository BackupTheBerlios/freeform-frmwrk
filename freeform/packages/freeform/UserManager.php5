<?

/**
 * This abstract class declares general contract of accessing user management systems. Its few methods will
 * allow third-party software to easily communicate with whatever user system installed. It is quite easy
 * to subclass UserManager on top of popular forum/blog software and use their user
 * management systems from Freeform applications.
 * @author Dennis Popel
 * @since 1.2.0.RC
 * @see <class>User</class>
 */
abstract class UserManager {
  private static $instance;
  
  /**
   * Returns true if a user with given credentials exists
   * @param  string $login  the login name
   * @param  string $password  the password
   * @return  bool  true if such user exists; false otherwise
   */
  abstract function isValidUser($login, $password);
  
  /**
   * Return an instance of <class>User</class> from the user management system by its unique ID
   * @param  int $id  the unique user ID
   * @return  User  the instance of this user or null
   */
  abstract function getUserByID($id);
  
  /**
   * Return an instance of <class>User</class> from the user management system by its unique login name
   * @param  string $login  the unique login name
   * @return  User  the instance of this user or null
   */
  abstract function getUserByLogin($login);
  
  /**
   * Return an instance of the currently installed user management system
   * @return  UserManager  the currently installed user management system instance
   * @throws  ConfigurationException  if the 
   */
  static function getInstance() {
    if(!self::$instance) {
      $cn = Package::getPackageByName('freeform')->getProperty('userManager');
      try {
        $rc = new ReflectionClass($cn);
        if(!$rc->isSubclassOf('UserManager')) {
          throw new Exception();
        }
        self::$instance = $rc->newInstance();
      } catch(Exception $e) {  
        throw new ConfigurationException('freeform', 'userManager', 'not specified or denotes non-existing class');
      }
    }
    return self::$instance;
  }
}

?>