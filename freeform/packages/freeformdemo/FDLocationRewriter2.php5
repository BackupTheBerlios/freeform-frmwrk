<?

/**
 * This is a sample <class>LocationRewriter</class> implementation that will
 * rewrite the URLs of the <class>Location</class>s using the following scheme:<br/> 
 * <tt>http://localhost:80/index.php5?action=ActionName&amp;param=value</tt> into <br/>
 * <tt>http://localhost:80/index.php5/ActionName/param=value</tt>.
 *
 * To enable this rewriter, you'll have to modify the .htaccess or httpd.conf file
 * and add these lines:
 *
 * <pre>
 * RewriteEngine on
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteCond %{REQUEST_FILENAME} !-d
 * RewriteRule ([^/]*)/?(.*) /index\.php5\?action=$1&$2 [QSA]
 * </pre>
 *
 * Then open the <tt>.config</tt> file of the freeform package (located at {FREEFORM_HOME}/packages/freeform) and add this line:
 * <tt>locationRewriter=FDLocationRewriter2</tt>. To disable the rewriter, simply undo the above
 * changes.
 * @since 1.2.0.Beta
 * @author Dennis Popel
 * @see <doc file="locationRewriter.txt" package="freeform">Location rewriting</doc>
 * @see <class>LocationRewriter</class> 
 */
class FDLocationRewriter2 implements LocationRewriter {
  static function rewrite(Location $l) {
    $rv = Location::rewrite($l);
    $u = new URL($rv);
    
    $action = $u->query['action'];
    unSet($u->query['action']);
    $u->resource = $action;
    return str_replace('?', '/', $u->toString());
  }
  
  /**
   * With this rewriter the Apache does decoding automatically with mod_rewrite; so we return $_GET here
   * @param  string $queryString  the query string to rewrite
   * @return  array  the GET parameters
   */
  static function decode($queryString) {
    return $_GET;
  }
}

?>