<?

/**
 * Generic exception to be utilized as notifier on feature isn't supported
 *
 * @author    Gracon Mariyan (aka marik) 
 * @since 1.2.0.Beta
 */ 
class NotSupportedException extends Exception {
  
  /**
   * Create a NotSupportedException with message
   *
   * @param   string $message   the error message
   */
  function __construct($message) {
    parent::__construct($message);    
  } 

}

?>