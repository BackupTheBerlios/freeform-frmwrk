<?

/**
 * This tag displays previously defined region with the name specified in the <tt>name</tt>
 * attribute
 *
 * @see <class>HTMLRegion</class> tag
 * @since 1.1.0
 */
class HTMLShowRegion extends HTMLTag {
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    if($region = $this->getDocument()->getRegion($this->getAttribute('name'))) {
      foreach($region->getChildren() as $c) {
        $this->getDocument()->process($c);
      }
    } 
    return self::SKIP_BODY;
  }
}

?>