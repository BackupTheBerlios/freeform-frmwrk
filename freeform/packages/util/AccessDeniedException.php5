<?

/**
 * This is a general-purpose "Access Denied" exception. It can be used to convey a reason
 * code and message and the object access to which was denied
 * @author Dennis Popel
 * @since 1.2.0.Beta
 */
class AccessDeniedException extends Exception {
  private $reason = '';
  private $reasonCode = 0;
  private $object = null;
  
  /**
   * Construct an AccessDeniedException with the given reason message, code and object
   * @param  string $reason  the reason message
   * @param  int $reasonCode  the reason error code
   * @param  object $object  the object operation on which caused this exception
   */
  function __construct($reason = '', $reasonCode = '', $object = null) {
    parent::__construct('Access Denied: ' . $reason);
    $this->reason = $reason;
    $this->reasonCode = $reasonCode;
    $this->object = $object;
  }
  
  /**
   * Get the reason message
   * @return  string  the message
   */
  function getReasonMessage() {
    return $this->reason;
  }
  
  /**
   * Get the reason error code
   * @return  int  the error code
   */
  function getReasonCode() {
    return $this->reasonCode;
  }
  
  /**
   * Get the object access to which caused this exception
   * @return  object  the object
   */
  function getObject() {
    return $this->object;
  }
}

?>