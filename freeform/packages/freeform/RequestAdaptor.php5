<?

/**
 * Request adaptors serve to properly decode data exchanged over HTTP for
 * use by actions. Actions do not directly query RequestAdaptors, they 
 * call methods of a Request associated with them. The front controller will
 * analyze content-type of incoming data and populate initial Request object
 * with passed parameters (headers, variables, and cookies). Actions 
 * may simulate requests by manually creating Request instances and passing
 * them to other actions.
 *
 * NOTE: most nonstandard (i.e., FDF) adaptors may require that 
 * always_populate_raw_post_data is on (either in php.ini or .htaccess).
 * @author Dennis Popel
 * @since 1.0.0
 */
abstract class RequestAdaptor {
  /**
   * Decode the parameters from the body of the request
   *
   * @param  array $headers  headers of the request
   * @return  array   array of name=>value pairs of decoded parameters
   */
  abstract function getParameters($headers);
  
  /**
   * Return true if the specified content-type is supported by this adaptor
   *
   * @param   string $contentType   MIME content type
   * @return  bool                  true if the content type is supported
   */
  abstract static function isSupportedContentType($contentType);
}

?>