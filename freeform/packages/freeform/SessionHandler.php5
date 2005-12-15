<?
/**
 * SessionHandler interface allows to create different session implementations in a standard way.
 * Each session implementation must be able to carry session variables between requests
 * and to maintain a logged user.
 *
 * The Session API does not define how to handle users, but it guarantees that each session
 * implementation will be able to carry user between requests.
 * It is up to the application which user model to choose.
 *
 * All accesses to session impelmentation, or session handler, is done via static methods of a 
 * special Session class. Each session implementation must provide a static method load() that
 * returns the instance of session implementation class and will take necessary actions to
 * create that instance if the session has not yet been created.
 *
 * The loader will call the appropriate handler class' static method load() and it will return
 * the instance of the session implementation class. That instance will be passed to the Session
 * class and further all session access will be passed to it. The session implementation
 * must define the isExpired() method to track session expiration. In case of expired session
 * the load() method must return a new, valid session but it must return true from a call to
 * isExpired() method.
 *
 * If the session has expired, a new handler should be created but return true 
 * in a call to its isExpired() method.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
interface SessionHandler {
  /**
   * Set a session variable that will be carried across requests under name $name. 
   *
   * @param  string $name  name of the session variable
   * @param  mixed $value  value of the variable
   */
  function setVariable($name, $value);
  
  /**
   * Get a session variable $name. If no variable $name was stored in session, it must return
   * null.
   *
   * @return  User  session variable with name $name or null
   */
  function getVariable($name);
  
  /**
   * Get current user. This should return an instance of User interface and it will be meaninful to
   * application business logic.
   *
   * @return  mixed  current session user
   */
  function getUser();
  
  /**
   * Set current session user.
   *
   * @param  User $user  The user to set or null
   */
  function setUser($user);
  
  /**
   * Returns true if the session has expired (i.e., the session handler discovered that session
   * was created but it has become stale.
   *
   * @return  bool  true if the session has expired.
   */
  function isExpired();
  
  /**
   * Sets the private mode
   * @param  string $hash  the hash for the private mode 
   */
  function setPrivateMode($hash);
  
  /**
   * Return the private mode hash or null
   * @return  string  the private mode hash
   */
  function getPrivateMode();
  
  /**
   * This is invoked by the loader to construct the instance of session impelemnation class
   *
   * @return  SessionHandler  session implementation class instance capable of handling current session
   */
  static function load($request);
}

?>