<?

/**
 * A generic text (CData) node. <class>SDParser</class> will return instances of this class
 * if it encounters character data sections in input document. In custom SDParser
 * implementations it is possible to return subclasses of SDTextNode
 * @author Dennis Popel
 * @since 1.1.0.Beta
 */ 
class SDTextNode extends SDNode {
  private $content;
  
  /**
   * Create a text node with specified content
   *
   * @param  SDElement $parent  the parent of this node
   * @param  string $data  the content
   */
  function __construct($parent, $content = '') {
    parent::__construct($parent);
    $this->setContent($content);
  }
  
  /**
   * Get the content of this text node
   *
   * @return   string   the content
   */
  function getContent() {
    return $this->content;
  }
  
  /**
   * Set the content of this node
   *
   * @param  string $content   the content to set
   */
  function setContent($content) {
    $this->content = $content;
  }
  
  /**
   * Return the xml representation of this text node. <tt>SDTextNode</tt> returns
   * its content that was set via the call to the <method>SDTextNode::setContent</method>().
   *
   * @return  string  the content of this text node
   */
  function toString() {
    return $this->getContent();
  }
}

?>
