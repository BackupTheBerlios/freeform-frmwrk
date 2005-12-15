<?

/**
 * ActionController is the front controller for the Freeform framework.
 * It is responsible for instantiating the proper action, getting its 
 * <class>AccessController</class> and processing it.
 * The steps taken are:
 * <ol>
 * <li>
 *   a call to <method>Request::parseHTTP</method> is made to create initial 
 *   <class>Request</class> from <class>RequestAdaptor</class> that handles incoming 
 *   HTTP data based on their content type
 * </li>
 * <li>
 *   the request parameter <tt>action</tt> is analyzed and corresponding Action is
 *   instantiated. If the action cannot be instantiated (e.g., invalid action class
 *   name was supplied), the <tt>action.error</tt> configuration option is queried to
 *   construct the error-handling Action. If that fails, a <class>ConfigurationException</class>
 *   will be thrown, resulting in the fatal error of the application.
 * </li>
 * <li>
 *   the requested action is passed to the <method>ActionController::process</method>(), 
 *   the result is returned to client. If the Action's <class>AccessController</class>
 *   forbids access to it, the <method>Action::onAccessDenied</method>() method is called;
 *   otherwise the <method>Action::process</method>() is invoked.
 * </li>
 * </ol>
 *
 * Starting with 1.2.0.Beta you can use this class to call another action from you action
 * by calling its static <method>ActionController::process</method>() method, passing it
 * the <class>Action</class> object to be processed.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
class ActionController {
  /**
   * Execute a single Action. The Action's <method>Action::onInit</method> will be called,
   * then its <class>AccessController</class> will be queried if the action can be executed,
   * and, if allowed, its <method>Action::process</method> will be called. If the action cannot
   * be executed in the current environment, then an <class>AccessDeniedException</class>
   * will be thrown
   * @param  Action $action  the action to execute
   * @throws  AccessDeniedException  if the action cannot be executed
   */
  static function process(Action $action) {
    $action->onInit();
    $ac = $action->getAccessController();
    if(is_null($ac) || ($ac->isAccessible())) {
      $action->process();
    } else {
      throw new AccessDeniedException('Action access controller (' . get_class($ac) . ') forbids this action', 0, $action);
    }
  }
  
  /**
   * This method is called by the launcher (index.php5)
   */
  static function processHTTP() {
    ob_start();
    
    $request = Request::parseHTTP();
    $responce = new Responce($request);
    
    $pkg = Package::getPackageByName('freeform');
      
    // Initialize session
    $sessionImpl = $pkg->getProperty('session.impl');
    $class = new ReflectionClass($sessionImpl);
    $method = $class->getMethod('load');
    Session::setHandler($method->invoke(null, $request));
    
    // See if we have the server cache hit here
    if(!$responce->getFromCache()) {
      // Initialize action
      $action = $request->getParameter('action', $da = $pkg->getProperty('action.default'));
      try {
        $class = new ReflectionClass($action);
        if(!$class->isSubclassOf('Action') || $class->isAbstract()) {
          throw new Exception('Can not execute action ' . $class->getName());
        }
      } catch(Exception $e) {
        $class = new ReflectionClass($pkg->getProperty('action.error', $da));
        if($class->isSubclassOf('Action') && !$class->isAbstract()) {
          $responce->relocate(new Location($class->getName()));
          $responce->setStatusCode(Response::STATUS_ERR_ACTION);
        } else {
          throw new ConfigurationException('freeform', 'action.error, action.default', 'do not denote a valid instantiable Action subclass');
        }
      }
      
      if(!$responce->isRelocation()) {
        // Initialize, secure and execute
        $action = $class->newInstance($request, $responce, true);
        Package::setEntryPackage(Package::getPackage($action));
        try {
          self::process($action);
        } catch(AccessDeniedException $ade) {
          $responce->setStatusCode(Responce::STATUS_ERR_ACCESS);
          $action->onAccessDenied();
        }
      }
    }
    
    // Send back the document
    foreach($responce->getHeaders() as $header) {
      header($header, false);
    }
    if($output = trim(ob_get_contents())) {
      error_log('Executed Action ' . $class->getName() . ' produced output: ' . $output);
    }
    ob_end_clean();
    echo $responce->getBody();
  }
}

?>