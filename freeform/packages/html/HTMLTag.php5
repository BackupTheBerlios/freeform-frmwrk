<?
       
/**
 * The HTMLTag is the base class of tags in HTML Document API. It
 * allows you to control various aspects of tag behaviour and create
 * complex views with a single tag. The tags effectively constitute
 * the View components in the MVC architecture.
 * 
 * All tags can have children - either other tags (i.e, HTMLTag or its 
 * subclasses instances) or text nodes (<class>HTMLTextNode</class>s). The tag can be exposed
 * (this means that its open and close html tag strings will be output) and/or
 * enabled (this means that the tag will not output anything).
 * 
 * Instances of HTMLTag and subclasses are created once per document lifetime
 * by the template parser. If it discovers a tag whose name matches a HTMLTag 
 * subclass, it will instantiate it and add it to the tag tree.
 *
 * After the parser builds the tag tree, the <class>HTMLDocument</class> will process the tags.
 * It starts from the root element (the &lt;html&gt; tag) and recursively goes deeper
 * to every tag in the template.
 *
 * Each tag is processed as follows:
 * <ol>
 * <li>The <method>HTMLTag::onOpen</method>() method is called. This is where all internal states are set
 *    if the tag sees it can not display its body, the onOpen() should return
 *    HTMLTag::SKIP_BODY; otherwise it sould return HTMLTag::PROCESS_BODY;</li>
 * <li>If the tag returns PROCESS_BODY, its <method>HTMLTag::isExposed()</method> method is checked whether
 *    to include the html open/close tags in the responce (for example, <class>HTMLShowIterable</class>
 *    is not exposed, so you will not see it nowhere in the responce)</li>
 * <li>If the tag has children:
 *    <ol>
 *      <li>its <method>HTMLTag::onBeforeBody</method>() method is called;</li>
 *      <li>its children are processed;</li>
 *      <li>its <method>HTMLTag::onAfterBody</method>() method is called;</li>
 *      <li>if the <method>HTMLTag::isRepeatBody</method>() returns true, the step 3 is repeated</li>
 *    </ol></li>
 * <li>if the tag's body was processed, the <method>HTMLTag::onClose</method>() method is called 
 * to reset all internal states.</li>
 * </ol>
 * The tag is processed only if it is enabled (<method>HTMLTag::isEnabled</method>() returns true).
 *
 * The HTMLTag supports access to document variables in its attributes.
 * If an attribute contains a value in form '%variableName', then the tag
 * will try to find the specified variable in the document stack in a call
 * to <method>HTMLTag::getAttribute()</method>.
 *
 * Tags can change their names and attributes when the template processes them.
 * For instance, HTMLLink tag (&lt;HTMLLink&gt;) will change its name to <tt>a</tt> and
 * remove the <tt>key</tt> attribute and add the <tt>href</tt> attribute. However, it should be
 * noted that the same instance can be processed multiple times in a template,
 * so it is important to restore the tag state and its children state in
 * the <method>HTMLTag::onClose</method>() method. Also tags must preserve same name after 
 * <method>HTMLTag::onOpen</method>() and <method>HTMLTag::onClose</method>() because the template 
 * generates html markup after these methods.
 *
 * There are many situations when the tags are constructed not by the parser from the template,
 * but by another tags that render compex views. In these cases the attributes can contain
 * non-string values also; the created tags can access these values to render their representation
 * according to their semantics.
 *
 * <b>NOTE:</b> you must add all custom tag class names to the .spi file so that the runtime
 * will be able to resolve them
 */
class HTMLTag extends SDElement {
  /**
   * The template will process this tag's children
   */
  const PROCESS_BODY = 1;

  /**
   * The template will not process children
   */
  const SKIP_BODY = 2;

  private $enabled = true;        
  private $exposed = true;      

  /**
   * Create new HTMLTag with the given owner document, parent, name and attributes
   *
   * @param  HTMLDocument $doc  the owner document
   * @param  HTMLTag $parent  the parent tag 
   * @param  string $nameSpace  the namespace (not used in this version)
   * @param  string $name  the name of the tag as it appears in the template
   * @param  array $attrs  the attributes of this tag (strings if created from template, may be other types if invoked directly)
   */
  function __construct($parent, $name, $attrs = array()) {
    parent::__construct($parent, $name, $attrs);
    $this->onInit();
  }

  /**
   * Override this method to do some initializing here. This method is
   * called by HTMLTag constructor and is a convenience method that
   * allows bypass constructor override. This is the ideal place to
   * set exposed/enabled properties. This method is called once per
   * tag lifetime, at the moment of tag constructor is called so
   * the tag cannot make any assumptions of its children nodes.
   */
  function onInit() { }

  /**
   * Return the HTMLDocument object this element belongs to
   * @return  HTMLDocument  the containing document
   */
  function getDocument() {
    if($p = $this->getParent()) {
      return $p->getDocument();
    } else {
      throw new Exception('Cannot get document; node without parent');
    }
  }
  
  /**
   * Override this to take some action (possibly modify own children state and 
   * own attributes) and
   * decide whether to process the body. Please note that this method
   * can be called multiple times on the same instance if the tag is repeated
   * with the body of parent tag.
   * @return  int  either PROCESS_BODY or SKIP_BODY
   */
  function onOpen() {
    return $this->isEnabled() ? self::PROCESS_BODY : self::SKIP_BODY;
  }                   
  
  /**
   * Override this to take some action (possibly restore own children
   * state and attributes) after the template has finished processing the body
   * of the tag.
   */
  function onClose() {}

  /**
   * Override this to take some action before each iteration over the tag 
   * children
   */
  function onBeforeBody() {}

  /**
   * Override this to take some action after each iteration over the tag 
   * children
   */
  function onAfterBody() {}
  
  /**
   * Returns true if the tag is exposed
   * @return   bool  true if the tag wishes to expose itself in the output
   */
  function isExposed() {
    return $this->exposed;
  }

  /**
   * Set the exposed property of the tag
   * @param   bool $exposed   true if the tag is exposed
   */
  function setExposed($exposed) {
    $this->exposed = $exposed;
  }
  
  /**
   * Returns true if the tag is enabled
   * @return   bool  true if the tag wishes to expose itself in the output
   */
  function isEnabled() {
    return $this->enabled;
  }
  
  /**
   * Set the enabled flag of this tag
   * @param  bool $enabled   flag to enable or disable this tag
   */
  function setEnabled($enabled) {
    $this->enabled = $enabled;
  }
  
  /**
   * Returns true if the tag requests that its body be processed again
   * @return  bool   true if the tags requests to repeat its body
   */
  function isRepeatBody() {
    return false;
  }
  
  /**
   * Returns attribute $name or default value if the attribute not set.
	 * Will resolve document variable if the value of the attribute is in
	 * form of '%variableName'. If the attribute value is in form '@msgId' then
	 * the tag will try to get the translated message with id of <tt>msgId</tt> using
	 * the document's locale. If the attribute value starts with '@%var', then the
	 * value will be the localized message with the key that is the value of the
	 * template variable <tt>var</tt>.
	 * @param   string $name   name of the attribute
	 * @param   mixed $defValue   default value to return if no attribute $name found
   */
  function getAttribute($name, $defValue = null) {
    $rv = parent::getAttribute($name, null);
    if(!is_null($rv)) {
      if(is_string($rv) && $rv != '' && $rv[0] == '%') {
        return $this->getDocument()->getVariable(subStr($rv, 1));
      } elseif(is_string($rv) && $rv != '' && $rv[0] == '@' && $rv[1] == '%') {
        return $this->getDocument()->getMessage($this->getDocument()->getVariable(subStr($rv, 2)));
      } elseif(is_string($rv) && $rv != '' && $rv[0] == '@') {
        return $this->getDocument()->getMessage(subStr($rv, 1));
      } else {
        return $this->getDocument()->expandVars($rv);
      }
    } else {
      return $defValue;
    }
  }
  
  function getAttributes() {
    $rv = array();
    foreach(array_keys($this->attributes) as $a) {
      $rv[$a] = $this->getAttribute($a);
    }
    return $rv;
  }
  
  /**
   * Find first child with the given name
   * @param   string $name  tag name to find
   * @return  HTMLTag   the tag searched for
   */
  function getTagByName($name) {
    foreach($this->children as $c) {  
      if($c instanceof HTMLTag && strToLower($c->getName()) == strToLower($name)) {
        return $c;
      }
    }
  }
}

?>