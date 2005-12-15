<?

/**
 * This class represents the root node of the HTML document or descendants. It overrides the
 * <method>HTMLTag::getDocument</method> to return the actual containing document rather than
 * routing this call to parent. 
 * @since 1.2.0.Alpha
 * @author Dennis Popel
 */
class HTMLRootNode extends HTMLTag {
  private $document = null;
  
  function __construct($d = null, $nameSpace, $name, $attrs = array()) {
    parent::__construct(null, $nameSpace, $name, $attrs);
    $this->setDocument($d);
  }
  
  function setDocument($d) {
    $this->document = $d;
  }
  
  function getDocument() {
    return $this->document;
  }
}

?>