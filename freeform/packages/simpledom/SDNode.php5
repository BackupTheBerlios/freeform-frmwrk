<?

/**
 * This is a base class for all elements in SimpleDOM API.
 * @since 1.0.0.Beta
 * @author Dennis Popel
 */
abstract class SDNode {
  private $parent;
  
  /**
   * Construct a node
   *
   * @param  SDElement $parent  the parent of this node
   */
  function __construct($parent) {
    $this->setParent($parent);
  }
  
  /**
   * Get the parent of this node
   *
   * @return  SDElement  parent of this node or null if no parent
   */
  function getParent() {
    return $this->parent;
  }
  
  /**
   * Set the parent of this node. Only SDElement or descendants can be parents
   * for nodes.
   *
   * @param  SDElement $parent  parent of this node or null
   */
  function setParent($parent) {
    $this->parent = $parent;
  }
  
  /**
   * This abstract method must be overridden by subclasses to return the
   * xml representation of the node after the parsing
   *
   * @return  string  the xml representation of this node
   * @since 1.1.0
   */
  abstract function toString();
}

?>