<?

/**
 * This is the text node that represents text sections in source template.
 * This class has some features that allow to use it for programming custom
 * tags. The text node, if discovered by parser, is set to non-raw mode that
 * means that when displaying the node content, all markup characters will be
 * escaped with htmlSpecialChars() and template variables will be interpolated.
 * Custom tags can create child text nodes and set them to raw mode so that they
 * will be able to contain markup elements. This is useful if you want
 * to create a tag that highlights PHP source - you can design it so that it
 * will add a child text node, set it to raw mode, pump the PHP source through
 * source hiliter (which will return html-formatted string) and feed the result
 * to the setContent() method. If this text node will be processed by the 
 * HTMLDocument, the hiliting will be displayed. If the node were in non-raw mode,
 * the HTML source would appear instead.
 * @author Dennis Popel
 */
class HTMLTextNode extends SDTextNode {
  private $raw = false;
  
  /**
   * Get the unparsed content of this node as it was passed to setContent()
   * @return  string  original unparsed content of this node
   */
  function getUnparsedContent() {
    return parent::getContent();
  }
  
  /**
   * Set this text node to be raw mode. When in raw mode, the content is not 
   * passed through htmlSpecialChars.
   * @param  bool $raw  flag to enable or disable the raw mode
   */
  function setRaw($raw) {
    $this->raw = $raw;
  }
  
  /**
   * Return the raw mode flag for this text node
   * @return  bool  the raw mode flag
   */
  function getRaw() {
    return $this->raw;
  }
  
  /**
   * Return the content of this text node. Will return interpolated template 
   * variables. Will escape special characters if in non-raw mode.
   * @return  string  content of this text node
   */
  function getContent() {
    if($this->raw) {
      return $this->getUnparsedContent();
    } else {
      if($p = $this->getParent()) {
        return $p->getDocument()->expandVars(parent::getContent());
      }
    }
  }
}

?>
