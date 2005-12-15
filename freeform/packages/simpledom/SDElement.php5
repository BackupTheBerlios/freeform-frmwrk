<?

/**
 * This class represent a container element in an XML document. It supports
 * adding and removing children as well as getting all elements with
 * specified tag name or nodes that are instances of particular class.
 * You can subclass <class>SDElement</class> and <class>SDParser</class> to provide your custom 
 * functionality beyond simple tree-like management of containers and character
 * data nodes.
 * @author Dennis Popel
 * @since 1.1.0.Beta
 */
class SDElement extends SDNode {
  protected $children = array();
  private $name = '';
  protected $attributes = array();
  
  /**
   * Create new SDElement with given namespace, tag name and attributes. Please
   * note that at time of construction the element will NOT have its children
   * available. Also it is possible that in some circumstances SDElements are
   * constructed without parent, so it is not advisable to do computations
   * in the constructors of child classes.
   * @param  SDElement $parent  the parent element for this node, if any
   * @param  string $tagName    the tag name
   * @param  array $attrs       the attributes
   */
  function __construct($parent, $name, $attrs = array()) {
    parent::__construct($parent);
    $this->name = $name;
    $this->attributes = $attrs;
  }
  
  /**
   * Add a child node. This method will set the child's parent to this object.
   * @param  SDNode $child      the child node to add
   */ 
  function addNode(SDNode $child) {
    $child->setParent($this);
    $this->children[] = $child;
  }
  
  /**
   * Remove a child node
   * <b>Note:</b> Since 1.2.0.Alpha this method does not set the parent of the removed node to null
   * @param  SDNode $child      the child to remove
   */
  function removeNode($child) {
    if(!is_null($child)) {
      foreach($this->children as $k=>$c) {
        if($c === $child) {
          unSet($this->children[$k]);
        }
      }
    }
  }
  
  /**
   * Remove all children from this tag
   */
  function removeAll() {
    foreach($this->children as $v) {
      $v->setParent(null);
    }
    $this->children = array();
  }     
  
  /**
   * Get all children of this node
   * @return  array            the children of this element
   */
  function getChildren() {
    return $this->children;
  }
  
  /**
   * Set all element's children, removing already contained children
   * @param  array $children  array of SDNode items that contains children
   */
  function setChildren($children) {
    $this->removeAll();
    foreach($children as $c) {
      $this->addNode($c);
    }
  }
  
  /**
   * Get the tag name of this element
   *
   * @return  string          the tag name of this element
   */
  function getName() {
    return $this->name;
  }
  
  /**
   * Set the tag name of this element. Sometimes it is necessary that an element
   * changes its name.
   * @param  string $name     the new tag name
   */
  function setName($name) {
    $this->name = $name;
  }
  
  /**
   * Get a specific attribute of this element or default value if no attribute 
   * defined
   * @param  string $name     the name of the attribute
   * @param  mixed $defValue  the default value to return if no such attribute
   * @return  mixed           the value of the attribute. It is declared mixed so that
   *                          implementations may return non-strings
   */                             
  function getAttribute($name, $defValue = null) {
    if(array_key_exists($name, $this->attributes)) {
      return $this->attributes[$name];
    } else {
      return $defValue;
    }
  }
  
  /**
   * Set an attribute
   * @param  string $name     the name of the attribute to set
   * @param  mixed $value     the value to set
   */
  function setAttribute($name, $value) {
    $this->attributes[$name] = $value;
  }
  
  /**
   * Return all attributes of this tag as an array. This method calls 
   * <method>SDElement::setAttribute</method>() gor each attribute name so that
   * the results will be correct if you override setAttribute() in subclasses
   *
   * @return  array  all atributes of this tag
   */
  function getAttributes() {
    $rv = array();
    foreach(array_keys($this->attributes) as $key) {
      $rv[$key] = $this->getAttribute($key);
    }
    return $rv;
  }
  
  /**
   * Remove an attribute. Since 1.1.0 this method returns the value of the removed attribute.
   * @param  string $name     the name of the attribute to remove
   * @param  mixed $defValue  the default value that will be passed to the <method>SDElement::getAttribute</method> (since 1.1.0)
   * @return  mixed           the value of the removed attribute
   */
  function removeAttribute($name, $defValue = null) {
    $rv = $this->getAttribute($name, $defValue);
    unSet($this->attributes[$name]);
    return $rv;
  }

  /**
   * Get elements of this element that have specified tag name
   * @param   string $tagName   the tag name to match
   * @return  array             the elements
   */
  function getElementsByTagName($tagName) {
    $rv = array();
    foreach($this->children as $c) {
      if($c instanceof SDElement && $c->getName() == $tagName) {
        $rv[] = $c;
      }
    }
    return $rv;
  }
  
  /**
   * Return all child nodes that are instances of particular class
   * @param   string $className  the class name
   * @return  array              the elements
   */
  function getNodesByClass($className) {
    $rv = array();
    foreach($this->children as $c) {
      if($c instanceof $className) {
        $rv[] = $c;
      }
    }
    return $rv;
  }
  
  /**
   * Return true if this element has child nodes
   * @return  bool  true if the element has child nodes
   */
  function hasChildren() {
    return count($this->children) > 0;
  }
  
  /**
   * Return the xml representation of this element, including its
   * children, if any (will reconstruct the XML contained in the source document)
   * @return  string  the xml representation of this element
   * @since  1.1.0
   */
  function toString() {
    $rv = '<';
    $rv .= $this->getName();
    foreach($this->getAttributes() as $k=>$v) {
      $rv .= ' ' . $k . '="' . htmlSpecialChars($v) . '"';
    }
    if($this->hasChildren()) {
      $rv .= '>';
      foreach($this->getChildren() as $c) {
        $rv .= $c->toString();
      }
      $rv .= '</' . $this->getName() . '>';
    } else {
      $rv .= ' />';
    }
    return $rv;
  }
}

?>