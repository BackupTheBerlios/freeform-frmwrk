<?

/**
 * The URL class represents an URL and provides a way to relocate 
 * it to new places taking into account absolute/relative URLs,
 * paths etc.
 *
 * This class provides support for things like ports, subprotocols,
 * user name and password, query strings and references.
 * This class gives an easy access to different parts of an URL,
 * so relocation is really straighforward.
 *
 * When constucting an URL, keep in mind that protocol and host will be converted to lower case;
 * everything else will retain case. So, when comparing URLs, www.somehost.com/myFolder doesn't equal
 * www.somehost.com/myfolder, however, it equals Www.SomeHost.Com/myFolder
 *
 * @since 1.0.0
 * @author Dennis Popel
 * @author Mariyan Gracon (aka marik)
 */  
class URL {
  public $protocol;
  public $host;
  public $user;
  public $password;
  private $haspassword;
  public $port;
  public $path;
  public $pathFQ; // fully qualified path
  public $resource;
  public $extension;
  public $query = array();
  private $hasquery;
  public $ref;
  private $location;

  //
  //private static $regexURL = "(.*://)?(([^@:]*)(:[^@]*)?@)?([^?:/]+)(:[[:alnum:]]*)?(/[^?]*)?(\\?.*)?";
  //
  //private static $regexURL = '~(\b(?P<proto>\w+)\b(?:://))?((?P<user>[^@:]+)((?::)(?P<pass>[^@]*))?(?:@))?(\b(?P<host>([\w\d-]+\.?)+)\b((?::)\b(?P<port>\d+)\b)?)?(?P<path>((?P<drive>\w):)?[/\][^@]*$)?~';
  //2005.08.19
  //private static $regexURL = '~(\b(?P<proto>\w+)\b(?:://))?((?P<user>[^@:]+)((?::)(?P<pass>[^@]*))?(?:@))?(\b(?P<host>([\w\d-]+\.?)+)\b((?::)\b(?P<port>\d+)\b)?)?(?P<path>/.*$)?~';
  //2005.08.20
  //private static $regexURL = '~(\b(?P<proto>\w+)\b(?:://))?((?P<user>[^@:?#&]+)(?P<haspass>:(?P<pass>[^@]*)?)?@)?(\b(?P<host>([\w\d-]+\.?)+)\b((?::)\b(?P<port>\d+)\b)?)?(?P<path>/[^:@?&#]*)?((?:\?)(?P<query>[^#]*))?((?:#)(?P<ref>.*)$)?~';
  //2005.08.20.1
  //private static $regexURL = '~(\b(?P<proto>\w+)\b(?:://))?((?P<user>[^@:?#&]+)(?P<haspass>:(?P<pass>[^@]*)?)?@)?(\b(?P<host>([\w\d-]+\.?)+)\b((?::)\b(?P<port>\d+)\b)?)?(?P<path>/[^:@?&#]*)?((?:\?)(?P<query>[^#]*))?((?:#)(?P<ref>.*)$)?~';
  private static $regexURL = '~(\b(?P<proto>\w+)\b(?:://))?((?P<user>[^@:?#&]+)(?P<haspass>:(?P<pass>[^:@]*)?)?@)?(\b(?P<host>([\w\d-]+\.?)+)\b((?::)\b(?P<port>\d+)\b)?)?(?P<path>/[^:@?&#]*)?((?P<hasquery>\?(?P<query>[^#]*)))?((?:#)(?P<ref>.*)$)?~';

  /**
   * The sole constructor that creates a new URL from supplied location
   *
   * @param  string $location  an URL
   */
  function __construct($location) {

    $this->location = $location;

    // Regex the url into components
    preg_match(self::$regexURL, $location, $matches = array());

    $this->protocol   = (strLen(@$matches['proto']) > 0) ? strToLower(@$matches['proto']):null;
    $this->user       = (strLen(@$matches['user']) > 0) ? @$matches['user']:null;
    $this->password   = (strLen(@$matches['haspass']) > 0) ? @$matches['pass']:null;
    $this->host       = (strLen(@$matches['host']) > 0) ? strToLower(@$matches['host']):null;
    //Should we get concerned on default port?
    // $this->port 			= ( ! isSet($matches['port'])) ? getProtoByName($matches['proto']) : $matches['port'];
    $this->port       = (strLen(@$matches['port']) > 0) ? @$matches['port']:null;

    $this->path       = rtrim(@$matches['path'],($this->resource = baseName(@$matches['path'])));
    //$this->resource   = baseName(@$matches['path']);

    // $this->path				= (strLen(@$matches['path']) > 1) ? str_replace('\\', '/', dirName(@$matches['path'])) . '/' : '';
    // $this->resource   = baseName(@$matches['path']);

    preg_match('~(?P<ext>\.[^.]*)~',$this->resource,$extmatches = array());
    $this->extension  = @$extmatches['ext'];

    $this->ref	      = (strLen(@$matches['ref']) > 0) ? @$matches['ref']:null;

    //keep query as an array
    if (( strLen(@$matches['hasquery']) > 1) )
    foreach(explode('&', $matches['query']) as $q) {
      list($name,$value) = @explode('=', $q, 2);
      if (strLen($name) > 0) {
        $this->query[$name] = ($value = urlDecode($value)) ? $value:'';
      }
    }
    else
    $this->query = null;

  }

  private function hasQuery(){
    return (count($this->query) > 0);
  }

  public function getFullPath(){
    return ($this->path) .  ($this->resource);
  }

  /**
   * Returns a new URL to a new location which can be: 
   * <ol>
   * <li>Absolute (if protocol specified) - $newLocation</li>
   * <li>Absolute path (if no protocol, starts with "/") - will
   *    point to a new path and resource</li>
   * <li>Relative path (if starts with anything, is not a query, 
   *    but contains a path separator "/"</li> 
   * <li>Another resource if all above fails</li>
   * </ol>
   *
   * @param  string $newLocation  new URL string
   * @return  URL  the new URL object
   */
  function relocate($newLocation) {
    // The parsing is now straightforward:
    // See if we have an absolute URL, if so, return it.
    // Otherwise append or modify current path and copy over user, password and port.
    if(ereg("://[^/?#]+", $newLocation, $m = array())) {
      return new URL($newLocation);
    } else {
      if($newLocation[0] == "/")
        $newLocation = $this->protocol . "://" . $this->host . $newLocation;
      else
        $newLocation = $this->protocol . "://" . $this->host . $this->path . $newLocation;
        $result = new URL($newLocation);
        $result->port = $this->port;
        $result->user = $this->user;
        $result->password = $this->password;
      return $result;
    }
  }

  /**
   * This function returns path + resource + query string + ref
   *
   * @param bool $includeRef whether to include ref part of the url
   * @return  string  URL without the protocol, host, port, username, password
   */

  function getURI($includeRef = true) {

    if ($this->hasQuery()){
      $props = $this->query;
      // Sort query :)
      ksort($props);
      @$query = '';
      foreach ($props as $key => $value)
        $query .= $key . '=' . $value . '&';      
    }
    return (
      (($this->hasQuery() && $this->path)                 ? '?' . subStr($query,0,-1) :'') .
      (($includeRef && $this->ref && strLen($this->path)) ? '#' . ($this->ref) :'')
    );
  }


  /*function getURI() {
  $result = $this->path . $this->resource;
  // Sort query :)
  $props = $this->query;
  ksort($props);
  foreach($props as $k=>$v) {
  @$query .= "$k=$v&";
  }
  if(@$query) $result .= "?" . substr($query, 0, -1);
  if($this->ref) $result .= "#" . $this->ref;
  return $result;
  }*/

  /**
   * This method returns the properly formatted URL (the exact location used
   * to construct this URL or with modified parts if appropriate)
   *
   * @param bool $includeRef whether to include ref part of the url
   * @return  string  an URL string
   */

  function toString($includeRef = true){


    return (
    (($this->protocol)                        ? ($this->protocol) . '://':'') .
    (($this->user)                            ? $this->user :'') .
    (($this->user && isSet($this->password))  ? ':' . ($this->password):'') .
    (($this->user || $this->password)         ? '@':'') .
    (($this->host ||  $this->host)            ? $this->host :'') .
    (($this->host &&  $this->port)            ? ':' . ($this->port) :'') .
    (($this->getFullPath() )                  ? $this->getFullPath() :'') .
    (($this->getFullPath() )                  ? ($this->getURI($includeRef)): '')
    );

  }

  /**
   * Tests whether two URLs are equal.
   * Two URLs are equal if they have all fields equal, possibly except for ref
   *
   * @param  mixed $url the url to compare to
   * @return  bool  true if two locations are equal
   */
  function equals($url) {
    if (! ( $url instanceof URL))
    $url = new URL($url);
    return  $url->isWellformed() && $this->isWellformed() && ($this->toString(false) === $url->toString(false));
  }

  function getDefaultPort($protocol) {
    $ports = array(
    "http" => 80,
    "ftp"  => 21,
    "news" => 144);
    return @$ports[strToLower($protocol)];
  }

  /**
   * Tests whether two URLs are equal.
   * Two URLs are equal if they have all fields equal, possibly except for ref
   *
   * @return  bool  true if two locations are equal
   */

  function isWellformed(){
    return ($this->toString() === $this->location);
  }

}

?>

