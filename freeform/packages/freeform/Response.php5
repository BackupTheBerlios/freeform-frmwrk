<?

/**
 * Response of current action. Instances of Response class are passed to each action.
 * They get created by front controller or other actions and passed to action instances
 * to collect response information in form of response document, additional headers or
 * relocations. 
 *
 * Besides that, Response is responsible for returning service status codes
 * that indicate success or failure of requested action execution. As opposed
 * to HTTP status codes that indicate status of the whole HTTP request, 
 * service status codes indicate how given action performed. Normally
 * this status code will be analyzed by network applications rather than by
 * regular browsers.
 *
 * In case of regular browsers, Response can be used to send redirects to other
 * actions (pages), especially after submission of POST forms. In such case,
 * the document will be sent along with corresponding Location: header (by 
 * default) so that client application will still be able to process response
 * document (if any).
 *
 * Most web applications that are designed for browsers only, may ignore
 * all these extra options. The developers should be familiar with how to
 * return document body or to relocate to other actions. This is preferred
 * practice when a POST form responds with a relocation rather with a document
 * since if the user hits the 'Reload' button in browser, he will be asked 
 * whether the data should be resubmitted.
 *
 * Since 1.2.0.Alpha responses support caching via simple API. By default, every
 * response document sent to the client will be accompanied by a set of cache-controlling
 * headers that will prevent any caching. You can fine-tune the client-side cache behaviour
 * by setting lifetime and last modification time of every response so that clients will
 * use cached versions of a particular response. Correct use of the last-modification time
 * can also save server's CPU time as in some cases the invoked action will inform
 * the response that the data in the document have not changed, and the response won't
 * call the <method>Document::getBody</method>() method. Normally, the actions that
 * process POST forms will not use caching at all as they will use redirection.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 * @see <doc file="caching.txt">Caching</doc>
 */
class Response {
  /**
   * Request succeeded, response document is sent back
	 */ 
  const STATUS_OK = 100;
  
  /**
   * General action failure
   */
  const STATUS_ERR = 200;
  
  /**
   * Request failed, access denied
   */
	const STATUS_ERR_ACCESS = 201;
	
	/**
	 * Request failed, bad parameters
	 */
  const STATUS_ERR_PARAM = 202;
  
  /**
   * Request failed, wrong Action class name (redirecting to action.error action)
   */
  const STATUS_ERR_ACTION = 203;
  
  const STATUS_ERR_FATAL = 299;
  
  private $request = null;
  private $document = null;
  private $headers = array();
  private $relocation = null;
  private $statusCode = null;
  
  private $fullHeaders = null;
  private $body = null; 
  
  private $lastModified = null;
  private $lifeTime = null;
  private $modified = true;
  // The response may be forced to not to be cached on server manually
  private $cacheOnServer = false;
  
  /**
   * Create the response object and associate it with the given request
   * @param  Request $r  Request object to associate this response with
   */
  function __construct(Request $r) {
    $this->request = $r;
    $this->statusCode = self::STATUS_OK;
  }
  
  /**
   * Return request associated with this response. Documents may express interest in
   * request headers etc.
   * @return  Request   the request that created this response
   */
  function getRequest() {
    return $this->request;
  }
  
  /**
   * Set the document for this response. 
   * @param  Document $doc  document for this response
   */
  function setDocument(Document $doc) {
    $this->document = $doc;
  }
  
  /**
   * Return the document associated with this response
   * @return  Document   document associated with this response
   */
  function getDocument() {
    return $this->document;
  }
  
  /**
   * Set a header for this response. It will be merged with the document headers.
   * @param  string $header  a HTTP header for this response
   */
  function setHeader($header) {
    $this->headers[] = $header;
  }
  
  /**
   * Get an array of all headers
   * @return  array  headers
   */
  function getHeaders() {
    // We cache the possibly already prepared headers in the $fullHeaders
    if(is_null($this->fullHeaders)) { 
      if($this->modified && !is_null($this->document)) {
        $rv = array_merge($this->headers, $this->document->getHeaders());
      } else {
        $rv = $this->headers;
      }
    
      $rv[] = 'X-Freeform-Service-Status: ' . $this->statusCode;
    
      if($this->isRelocation()) {
        $rv[] = 'Location: ' . $this->getRelocation()->toURL();
      }
    
      $private = Session::getPrivateMode();
      
      // Prepare cache-control header
      $cc = array();
      if($this->lifeTime) {
        $cc[] = 'max-age=' . $this->lifeTime;
        $cc[] = 'must-revalidate';
        $cc[] = 'proxy-revalidate';
        $cc[] = 's-maxage=' . ($private ? 0 : $this->lifeTime);
        $rv[] = 'Expires: ' . gmDate('D, d M Y H:i:s', time() + $this->lifeTime) . ' GMT';
      } else {
        // Prevent any caching
        $cc[] = 'no-cache';
        $cc[] = 'must-revalidate';
        $cc[] = 'proxy-revalidate';
        $cc[] = 'max-age=0'; 
        $cc[] = 's-maxage=0';
        $rv[] = 'Expires: ' . gmDate('D, d M Y H:i:s', 1) . ' GMT';
      }
      if($private) {
        $cc[] = 'private';
      }
      foreach($cc as $c) {
        $rv[] = 'Cache-Control: ' . $c;
      }
      
      // See if we can reply with 304 Not Modified and skip body
      if(!$this->modified) {
        $rv[] = 'HTTP/1.1 304 Not Modified';
      } elseif($this->lastModified) {
        $rv[] = 'Last-Modified: ' . $this->lastModified . ' GMT';
      }
      $this->fullHeaders = $rv;
    } 
    return $this->fullHeaders;
  }
  
  /**
   * Get the body of the document associated with this response. Will return empty
   * string if the response detects that the document was not modified.
   * @return  string  body of the response document or null if no document was set
   */
  function getBody() {
    // We cache the possibly retrieved document body in $body. 
    if(is_null($this->body)) {
      // If the document has modified, get its body and possibly create cache entry
      if($this->modified) {
        $this->body = (is_null($this->document) ? '' : $this->document->getBody());
        
        $pkg = Package::getPackageByName('freeform');
        $hash = md5($_SERVER['QUERY_STRING']);
        
        // See if we can cache
        if($this->getRequest()->getMethod() != Request::METHOD_POST && $pkg->getProperty('cache.enable') && $this->lifeTime > 0 && $this->cacheOnServer) {
          if($hash1 = Session::getPrivateMode()) {
            if($pkg->getProperty('cache.private')) {
              $hash .= $hash1;
            } else {
              // Private mode caching disabled
              return $this->body;
            }
          } 
          file_put_contents($fn = $pkg->getProperty('cache.path') . '/' . $hash, $this->body);
          file_put_contents($fn . '.headers', join("\n", $this->getHeaders()));
          touch($fn, time() + $this->lifeTime);
          touch($fn . '.headers', time() + $this->lifeTime);
        }
      } else {
        $this->body = '';
      }
    }
    return $this->body;
  }
  
  /**
   * Returns true if the response is cached on the server. This is called by the
   * <class>ActionController</class> just after the request has been received so
   * that if the cached version is fresh, it will be sent back immediately, even 
   * before the security check. 
   * @return  bool  true if the response is cached on the server
   * @see <doc file="caching.txt">Caching</doc>
   */
  function getFromCache() {
    $pkg = Package::getPackageByName('freeform');
    $hash = md5($_SERVER['QUERY_STRING']);
    if($hash1 = Session::getPrivateMode()) {
      $hash .= $hash1;
    }
    $fn = $pkg->getProperty('cache.path') . '/' . $hash;
    if(file_exists($fn) && filemtime($fn) > time()) {
      $this->body = file_get_contents($fn);
      $this->fullHeaders = file($fn . '.headers');
      return true;
    } else {
      @unlink($fn);
      @unlink($fn . '.headers');
      return false;
    }
  }
  
  /**
   * Relocate to another action. Will cause relocation AFTER the Action::process() returns.
   * @param  Location $loc  location of the action to relocate to
   */
  function relocate(Location $location) {
    $this->relocation = $location;
  }
  
  /**
   * Check if the response has been relocated
   * @return  bool  true if the response relocates to another action
   */
  function isRelocation() {
    return !is_null($this->relocation);
  }
   
  /**
   * Retutn the location of action this response relocates to
   * @return  Location  the location of action this response relocates to
   */
  function getRelocation() {
    return $this->relocation;
  }
  
  /**
   * Set the last-modified time for the response document. It is the responsibility
   * of the action to correctly evaluate the time based on the model
   * @param  int $time  the timestamp of the last modification time of the document
   * @see <doc file="caching.txt">Caching</doc>
   */
  function setLastModified($time) {
    $this->lastModified = gmDate('D, d M Y H:i:s', $time) . ' GMT';
    $ims = $this->getRequest()->getHeader('If-Modified-Since');
    $this->modified = ($ims != $this->lastModified);
  }
  
  /**
   * Set the life time of the document, in seconds. Setting this property will prevent
   * clients from retrieving the document from the server for every identical request
   * during this period. Also, if the server-side caching is enabled, the response will
   * be cached on the server, so that similar requests from other clients and locations
   * will be responded with this cached version. For secure pages you can explicitly
   * disable server side caching.
   * @see <doc file="caching.txt">Caching</doc> 
   * @param  int $time  the life time of the document
   * @param  bool $cacheOnServer  flag to enable caching of the page on the server
   */ 
  function setLifeTime($time, $cacheOnServer = true) {
    $this->lifeTime = $time;
    $this->cacheOnServer = $cacheOnServer;
  }
  
  /**
   * Set the expiration time of this response document. Note that this defines an absolute
   * point in time after which the response will be considered stale, and how this
   * differs from the <method>Response::setLifeTime</method>().
   * @param  int $time  the time in the future after which the response will be considered stale
   * @param  bool $cacheOnServer  flag to enable caching of the page on the server
   * @see <doc file="caching.txt">Caching</doc>
   */
  function setExpires($time, $cacheOnServer = true) {
    $this->setLifeTime($time - time(), $cacheOnServer);
  }
  
  /**
   * Set the status code of this Response object. Normally you will
   * use it with <tt>STATUS_ERR_PARAM</tt> code only as all other codes
   * will be set automatically.
   * @param  int $code  status code
   */
  function setStatusCode($code) {
    $this->statusCode = $code;
  }
}  

?>