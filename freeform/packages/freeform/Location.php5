<?

/**
 * This class is used to embed links to other actions into responce documents.
 * It saves the document template designers from various quirks of creating
 * complex hyperlinks within, for expample, the <tt>&amp;lt;a&amp;gt;</tt> tag.
 *
 * The document implementation tag serving as a link placeholder will simply call 
 * <method>Location::toURL</method> method and will get correct URL of the link.
 *
 * Freeform reserves the following parameter names so that you cannot use them
 * for custom parameter names: <tt>action</tt>, <tt>hash</tt>, <tt>id</tt> and <tt>page</tt>.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
class Location implements LocationRewriter {
  // Made public to allow serialize referer
  public $action;
  public $parameters;
  public $anchor;
  
  // We will cache rewriter method here (like a method pointer!)
  private static $rewriter = null;
  
  /**
   * Construct a Location object from action (either given as a string or an instance) 
   * and a set of parameters. You can omit the $action parameter (setting it to null)
   * if you want to create link to default action.
   * @param  mixed $action      either an instance of Action or action class name. If null, the location will point to the default action as specified in the <tt>action.default</tt> .config option.
   * @param  array $parameters  parameters that will be passed to the action
   */
  function __construct($action = null, $parameters = array(), $anchor = null) {
    if(is_null($this->action = ($action instanceof Action) ? get_class($action) : $action)) {
      $this->action = Package::getPackageByName('freeform')->getProperty('action.default');
    }
    $this->anchor = $anchor;
    $this->parameters = $parameters;
  }
  
  /**
   * Return the class name of action of this Location object
   * @return  string  class name of action of this location
   */
  function getAction() {
    return $this->action;
  }
  
  /**
   * It is possible to add parameters to existing Location instance so it is
   * easy to generate links to same action with different parameters (like
   * result set paginator). Values of already existing parameters will
   * be overwritten with the new values.
   *
   * @param  string $name    name of the parameter to set
   * @param  scalar $value   the value of the peremeter
   */
  function setParameter($name, $value) {
    $this->parameters[$name] = $value;
  }
  
  /**
   * Return a specified parameter 
   *
   * @param    string $name  name of the parameter to return
   * @return   mixed         the parameter requested
   */
  function getParameter($name) {
    return @$this->parameters[$name];
  }
  
  /**
   * Return parameters of this location
   *
   * @return  array  array of key=>value pairs that denote parameters of this location
   */
  function getParameters() {
    return $this->parameters;
  }
  
  /**
   * Set the anchor for this location. On the page this location refers to
   * the browser will try to scroll to this anchor (refers to the html documents
   * only).
   * @param  string $anchor  anchor on the target page
   * @since 1.1.1
   */
  function setAnchor($anchor) {
    $this->anchor = $anchor;
  }
  
  /**
   * Create an URL to access an action with a given set of parameters.
   * This will use the class specified in the <tt>locationRewriter</tt> config option 
   * of the <package>freeform</package> package to rewrite the URLs of other actions.
   * @param  bool $disableRewrite  flag to disable the rewriting of the URL
   * @return  string  the URL of the action and set of parameters contained in this Location object
   */
  function toURL() {
    if(is_null(self::$rewriter)) {
      $lrc = Package::getPackage($this)->getProperty('locationRewriter', 'Location');
      try {
        $rc = new ReflectionClass($lrc);
        self::$rewriter = $rc->getMethod('rewrite');
      } catch(ReflectionException $re) {
        throw new ConfigurationException('freeform', 'locationRewriter', 'denotes non-existent class ' . $lrc);
      }
    } 
    return self::$rewriter->invoke(null, $this);    
  }
  
  /**
   * Returns true if this location points to the same action as that specified by $l.
   * Two location are equal if they point to same action and have same set of parameters.
   * @return  bool  true if both locations are equal, false otherwise.
   */
  function equals(Location $l) {
    if($this->getAction() == $l->getAction()) {
      return count(array_diff_assoc($this->getParameters(), $l->getParameters())) == 0;
    } else {
      return false;
    }
  }
  
  /**
   * This static method acts as the default location rewriter. It is inherited from the <class>LocationRewriter</class>
   * interface and returns the URL of a location in default format.
   * @param  Location $l  the location to return URL for
   * @return  string  the URL of a location
   */
  static function rewrite(Location $l) {
    $rv = '';
    $query = '';
    if($a = $l->getAction()) {
      $rv = 'action=' . $a;
    }
    
    if($hash = Session::getPrivateMode()) {
      $l->setParameter['hash'] = $hash;
    }
    
    unSet($l->parameters['action']);
    foreach($l->parameters as $k=>$v) {
      $query .= ($query ? '&' : '') . $k .'=' . urlEncode($v);
    }
    unSet($l->parameters['hash']);
    
    $rv = $rv . ($query ? '&' : '') . $query;
    if($rv) {
      $rv = '?' . $rv . ($l->anchor ? '#' . $l->anchor : '');
    } else {
      $rv = '';
    }
    return Request::$URL . $rv;
  }  
  
  /**
   * This method simply returns the $_GET array as there is no URL rewriting performed by this
   * LocationRewriter
   * @param  string $queryString  the query string
   * @return  array  the $_GET array
   */
  static function decode($queryString) {
    return $_GET;
  }
  
  // Buggy.
  static function parseURL($url) {
    if(!($url instanceof URL)) {
      $url = new URL($url);
    }
    return new Location($url->query['action'], $url->query);
  }
}

?>