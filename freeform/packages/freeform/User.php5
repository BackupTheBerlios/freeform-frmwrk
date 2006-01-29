<?

/**
 * This is a general User interface in Freeform framework. Sessions will accept 
 * and return instances of this interface in calls to getUser() and setUser().
 *
 * Freeform framework allows developers create their own or incorporate existing
 * user management systems - this is the main reason why this interface and the <class>UserManager</class> class exist.
 * It declares the general contract of quering particular user details,
 * such as the user name, role and possible other properties (via the call to
 * <method>User::getProperty</method>() method).
 *
 * The framework does not impose any limitations nor gives any guidelines to creating
 * user management systems. The only requirement being the user entity must be 
 * represented by a class that implements this interface. Instances of this class
 * can be passed to the <method>Session::getUser</method>() and <method>Session::setUser</method>()
 * methods. The instrances must be serializable by the standard session handlers, however
 * a particular session handler may be used in combination with the user management system to
 * treat <class>User</class> instances in a more particular way (for example, the
 * <class>PersistentSession</class> session handler is used in conjunction with the
 * <class>PersistentUser</class> class to correctly serialize its instances into the
 * session).
 * 
 * The user ID is the only property that cannot be modified after the user object has been
 * created. This property (accessed via the <method>User::getID</method>() method) is
 * used as the unique user ID. 
 *
 * This interface also declares a method <method>User::isRole</method>, which should return true if the user matches a role.
 * Users can have multiple roles. It is up to the implementation how these roles
 * are managed. The Security API reserves one role, <i>root</i>, to mark the root
 * user for its control programs. Packages may use the fact that a user has the root role
 * to guard access to critical actions. Implementations may return false if they do not
 * support user roles.
 * @author Dennis Popel
 * @since 1.0.0
 */ 
interface User {
  /**
   * Get the user ID of this user. This ID must be unique and the underlying user management system must 
   * guarantee that this ID will be ever changed for this particluar user.
   * @return  string  user name
   * @see <method>User::getLogin</method>()
   * @since 1.2.0.RC
   */
  function getID();
  /**
   * Get the login name of this user. The login name is the string of symbols that this user enters
   * to login into the application. The underlying user management system must guarantee that there are
   * no duplicate login names at the same time; however, this login name may change (unlike the user ID)
   * @see <method>User::getID</method>()
   * @return  string  login name
   * @since 1.2.0.RC
   */
  function getLogin();
  
  /**
   * Check if the user has the specified role
   * @param  string $role  the role
   * @return  bool  true if the user has the role $role
   */
  function isRole($role);
  
  /**
   * Get a particular property of the user (or the user's profile).
   * This method is designed to be the general contract of quering different user details
   * for different possible user system implementations.
   * @since 1.2.0.Beta
   * @param  string $key  the property name
   * @param  mixed $defValue  the value to return if no such property exist
   * @return  mixed  the property value $defValue
   */
  function getProperty($key, $defValue = null);
  
  /**
   * This method should return true if the user has logged in by the currently executing
   * action (i.e., is "logging in"). The implementations that do not support this
   * may always return true.
   *
   * The return value of this method is recommendative; it must not be relied on when
   * checking if the user is actually logged in.
   * @return  bool  true if the user is being logged in by the currently executing action
   * @since 1.2.0.Beta
   */
  function isLoggingIn();
  
  /**
   * Assign a role to the user. Implementations that do not support roles may ignore calls to this method
   * @param  string  the role
   * @return  bool  true on success, false on failure
   * @since 1.2.0.RC
   */
  function assignRole($role);
  
  /**
   * Revoke a role form the user. Implementations that do not support roles may ignore calls to this method
   * @param  string  the role
   * @return  bool  true on success, false on failure
   * @since 1.2.0.RC
   */
  function revokeRole($role); 
  
  /**
   * Return all the roles of this user. Implementations that do not support roles must return empty arrays
   * @return  array  the list of roles
   * @since 1.2.0.RC
   */
  function getRoles();
}

?>