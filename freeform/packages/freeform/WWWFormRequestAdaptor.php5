<?

/**
 * This RequestAdaptor handles normal HTML/WML forms of types 
 * application/x-www-form-urlencoded or multipart/form-data. It accepts
 * file uploads and returns them with request variables in form of <class>UploadedFileInfo</class>
 * objects.
 * 
 * Since 1.1.1.Stable moved from the html package to the freeform package.
 *
 * This adapter expects that all incoming data are UTF-8 encoded (as Freeform is purely
 * UTF-8 based framework). All document implementations must instruct the clients
 * to submit data in the UTF-8 encoding (as HTTP does not leave a way to detect it,
 * browsers will send data back in the encoding that matches the page's encoding).
 * If you design your own document implementation for non-HTML apps, you can also
 * design your own RequestAdaptor that may change the encoding of submitted data to
 * UTF-8. The framework stipulates that all data retrieved from the calls to the
 * Request::getXXX() methods are UTF-8.
 * @since 1.0.0
 * @author Dennis Popel
 */
class WWWFormRequestAdaptor extends RequestAdaptor {
  /**
   * Return the request parameters
   *
   * @return  array  request parameters combined of GET, POST and uploaded files
   */
  function getParameters($headers) {
    $rv = array_merge($_GET, $_POST);
    if(get_magic_quotes_gpc()) {
      array_walk_recursive($rv, 'strip_slashes_gpc');
    }
    foreach($_FILES as $k=>$v) {
      if(is_uploaded_file($v['tmp_name'])) {
        $rv[$k] = new UploadedFile($v);
      } else {
        $rv[$k] = '';
      }
    }
    return $rv;
  }
  
  /**
   * Supported content type checker
   *
   * @return  bool  true if the content type of the request is either <tt>application/x-www-form-urlencoded</tt> or <tt>multipart/form-data</tt>.
   */
  static function isSupportedContentType($contentType) {
    return 
      strPos(strtolower($contentType), 'application/x-www-form-urlencoded') !== false ||
      strPos(strtolower($contentType), 'multipart/form-data') !== false;
  }
}

?>