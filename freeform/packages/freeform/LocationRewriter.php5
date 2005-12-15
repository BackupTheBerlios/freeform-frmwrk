<?

/**
 * This interface is used to rewrite the URLs generated by the <method>Location::toURL</method>()
 * method calls. More specifically, the <method>Location::toURL</method> will look up the
 * config option <tt>locationRewriter</tt> of the <package>freeform<package> package and will
 * call its <method>LocationRewriter::rewrite</method>() passing itself as the parameter. If this
 * config option is unspecified, then <class>Location</class> objects will return standard URL.
 *
 * This feature is useful when used in combination with your web server URL rewrite engine
 * (like Apache's mod_rewrite). You can create rules that will map rewritten URLs to real ones
 * and write your own LocationRewriter to force Locations to create rewritten URLs.
 *
 * Please note that while the <method>LocationRewriter::rewrite</method>() method must return the absolute URL
 * (including the protocol, host, port and absolute path), the <method>LocationRewriter::decode</method>() method acts on the
 * query string only, which is passed as a single parameter.
 * @since 1.2.0.Beta
 * @author Dennis Popel
 */
interface LocationRewriter {
  /**
   * This method will be called by the <method>Location::toURL</method>() to return the
   * rewritten URL, as it will appear in the browser. This method should return 
   * the fully-qualified URL, including protocol, host port and path of the rewritten URL.<br/>
   * <b>Note</b>: this method cannot call the <method>Location::toURL</method>() method;
   * to retrieve the default URL of a location, the <method>Location::rewrite</method>() static method
   * should be called instead.
   * @param  Location $l  the Location object to rewrite
   * @return  string  the rewritten URL string
   */
  static function rewrite(Location $l);
  
  /**
   * This method is called from <method>Request::parseHTTP</method>() method to decode the rewritten
   * URLs as received from the remote user. This method should decode URLs only if the
   * web server doesn't do this itself; owherwise this method can safely return the $_GET array.
   * 
   * <b>Note</b>: this method is called for GET requests only as there is no way to rewrite
   * POST requests
   * @param  string $queryString  the query string to rewrite
   * @return  array  the decoded GET parameters of the request
   */
  static function decode($queryString);
}

?>