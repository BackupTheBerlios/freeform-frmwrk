<?

/**
 * Action is the base class for all MVC controller classes of every
 * web application. Actions get execuped (processed) with every user
 * gesture like clicking on a link or submitting a form.
 * Action are responsible for processing parameters supplied with
 * each user request and to update model and to prepare appropriate
 * view (result page, an implementation of the Document) suitable 
 * for the requesting client or its user agent.
 *
 * The <class>Request</class> and <class>Response</class> objects are created by the 
 * <class>ActionController</class> and supplied to every Action through the call to the constructor of the
 * Action. You can execute other actions from your action using <method>ActionController::process</method>().
 * @author Dennis Popel  
 * @since 1.0.0
 */
abstract class Action {
  private $request = null;
  private $response = null;
  private $requested = null;
  
  /**
   * Create an Action. The front controller will supply the default Request and Responce and mark
   * the action as requested. You can create Actions with default constructor or
   * supply your own Request and Responce to call the process() method from other action
   * and grap its output. Note that request might be needed by AccessController of
   * this action. 
   * @param  Request $request  the request object associated with this action
   * @param  Response $response  the responce object associated with this action
   * @param  bool $isRequested  flag to indicate that this action has been requested by a remote client rather than invoked locally in a chain
   */ 
  function __construct($request = null, $response = null, $isRequested = false) {
    $this->request = is_null($request) ? new Request() : $request;
    $this->response = is_null($response) ? new Response($this->request) : $response;
    $this->requested = $isRequested;
  }
  
  /**
   * Perform action initialization. This is called after the constructor, but 
   * before the access check and the process() or onAccessDenied() method.
   */
  function onInit() {}
  
  /**
   * Return an AccessController for this action.
   * @return  AccessController  the AccessController for this Action
   */
  function getAccessController() {
    return null;
  }
  
  /**
   * Returns true if the action has been requested by user
   * @return  bool  True if the action has been requested by the user
   */
  function isRequested() {
    return $this->requested;
  }
  
  /**
   * Return the request associated with this action
   * @return  Request  Request object for this Action
   */
  function getRequest() {
    return $this->request;
  }
  
  /**
   * Return the responce associated with this action
   * @return  Response  Responce object associated with this action
   * @since 1.2.0.RC
   */
  function getResponse() {
    return $this->response;
  }
  
  /**
   * Get the response. This method is deprecated. Use <method>Response::getResponse</method>
   * @return  Response  Response object associated with this action
   */
  function getResponce() {
    return $this->response;
  }
  
  /**
   * Will be called if the action's AccessController denies access to this action
   */
  function onAccessDenied() {}
  
  
  /**
   * Override this method to define the behaviour of this action
   */
  abstract function process();
  
  /**
   * A shortcut to <tt>Package::getPackage($this)</tt>
   * @return  Package   the package where this action was defined
   */
  function getPackage() {
    return Package::getPackage($this);
  }
}

?>