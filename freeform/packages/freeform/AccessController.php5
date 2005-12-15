<?

/**
 * This is the interface to access controllers that control access to each action. 
 * An action returns an AccessController instance appropriate for it. The front controller
 * then invokes its isAccessible() method to see if this action is accessible to user.
 * It is up to the application to define access policies according to the business logic. 
 *
 * @author    Dennis Popel  
 * @since   1.0.0
 */
interface AccessController {
  /**
   * Return true if the action can be processed (executed) in the current environment
   * (user, its role, session state, apllication state etc)
   * @return  bool  true if the action can be processed
   */
  function isAccessible();
}

?>