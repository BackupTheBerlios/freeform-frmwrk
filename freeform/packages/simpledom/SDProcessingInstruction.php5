<?

/**
 * This class encapsulates a single processing instruction encountered in the source
 * XML document. <class>SDParser</class> implementations may want to extend this class
 * to provide useful functionality of PIs.
 * @author Dennis Popel
 * @since 1.1.0
 */
class SDProcessingInstruction extends SDNode {
  private $target;
  private $data;

  /**
   * Construct a PI node with specified target and data
   *
   * @param  SDElement $parent  the owning element
   * @param  string $target  the PI target
   * @param  string $data  the data of the PI
   */
  function __construct($parent, $target, $data) {
    parent::__construct($parent);
    $this->setTarget($target);
    $this->setData($data);
  }

  /**
   * Get the target of this PI
   *
   * @return  string  target of this PI
   */
  function getTarget() {
    return $this->target;
  }

  /**
   * Set the target of this PI
   *
   * @param  string $target  target of this PI to set
   */
  function setTarget($target) {
    $this->target = $target;
  }

  /**
   * Get the data of this PI
   *
   * @return  string  data of this PI
   */
  function getData() {
    return $this->data;
  }

  /**
   * Set the data of this PI
   *
   * @param  string $data  data of this PI to set
   */
  function setData($data) {
    $this->data = $data;
  }
  
  function toString() {
    return '<?' . $this->getTarget() . ' ' . $this->getData() . '?>';
  }                    

}

?>
