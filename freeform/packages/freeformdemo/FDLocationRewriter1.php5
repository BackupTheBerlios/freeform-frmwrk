<?

/**
 * This is a sample <class>LocationRewriter</class> implementation that will
 * rewrite the URLs of the <class>Location</class>s using the base64 recode
 * functions, so that un URL like <tt>http://localhost:80/index.php5?action=FDWelcome</tt>
 * will be rewritten to <tt>http://localhost:80/YWN0aW9uPUZEV2VsY29tZQ==</tt>.
 *
 * To enable this rewriter, you'll have to modify the .htaccess or httpd.conf file
 * and add this line: <tt>ErrorDocument 404 /error.php5</tt>. This will redirect all
 * erroneous requests to a special wrapper launcher (error.php5, you can find it in the
 * <tt>resources</tt> folder of this package. It has to be placed in the same directory
 * where you placed the index.php5, Freeform loader file). Then open the <tt>.config</tt> file of the
 * freeform package (located at {FREEFORM_HOME}/packages/freeform) and add this line:
 * <tt>locationRewriter=FDLocationRewriter1</tt>. To disable the rewriter, simply undo the above
 * changes.
 *
 * <b>Note</b>: if you employ this rewriter, please see the comments in the error.php5 file
 * to learn about possible modes of using that file and about possible side-effects of such use.
 * Also note that this rewriter cannot be used with GET forms as there is no way to
 * force the user agent to rewrite the query string. Either you will have to provide a way 
 * of detecting whether the submitted URL is not encrypted or you should not use the
 * GET forms with this rewriter.
 * @since 1.2.0.Beta
 * @author Dennis Popel
 * @see <doc file="locationRewriter.txt" package="freeform">Location rewriting</doc>
 * @see <class>LocationRewriter</class> 
 */
class FDLocationRewriter1 implements LocationRewriter {
  static function rewrite(Location $l) {
    $rv = Location::rewrite($l);
    $u = new URL($rv);
    
    list($path, $res) = explode('?', $rv);
    $res = base64_encode($res); 
    $u->query = array();
    $u->resource = '';
    return $u->toString() . $res;
  }
  
  static function decode($queryString) {
    $res = base64_decode($queryString);
    foreach(explode('&', $res) as $pair) { 
      @list($k, $l) = explode('=', $pair);
      $rv[urldecode($k)] = urldecode($l);
    }
    return $rv;
  }
}

?>