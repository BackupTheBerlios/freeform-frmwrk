<?

/**
 * This is an interface to documents in Freeform Framework. Each action generates an 
 * instance of Document and passes it to the Responce object it is assotiated with. 
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
interface Document {
  /**
   * Get HTTP headers to send with this document, including Content-Type
   *
   * @return  array  array of HTTP headers this Document produced
   */
  function getHeaders();
  
  /**
   * Get the body of this template (ready for client)
   *
   * @return  string  the body of the document as it will be passed to the client. 
   */
  function getBody();
}

?>