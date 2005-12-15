<?

/**
 * This tag is used to automatically highlight PHP source in your templates. You use it 
 * like any other formatting tag, just enclose the code you want to highlight with this tag:
 *
 * <html>
 * <HTMLPHPSource>
 *    $x = $y + 2;
 *    $z = doMath($x, $y);
 *    print $z;
 * </HTMLPHPSource>
 * </html>
 *
 * Please note you do <b>not</b> enclose the source with the PHP open and close tags!
 * You can also use template variables inside this tag, if you preset them with valid fragment
 * of PHP source code.
 *
 * Please note that inside this tag you will not be able to use the less-than sign as the 
 * parser will consider it a beginning of a new tag. Instead, just use the <b>&amp;lt;</b> entity
 * reference like: <tt>$x &amp;lt;= 10</tt>
 * @since 1.1.1.Alpha
 * @author Dennis Popel
 */
class HTMLPHPSource extends HTMLTag {
  private $nodes = null;
  
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    $this->nodes = $this->getNodesByClass('HTMLTextNode');
    $text = '';
    foreach($this->nodes as $node) {
      $text .= $node->getContent();
    }
    $this->removeAll();
    $this->addNode($n = new HTMLTextNode($this));
    $n->setRaw(true);
    $n->setContent(highlight_string('<?' . str_replace('&lt;', '<', $text) . '?>', true));
    return self::PROCESS_BODY;
  }
  
  function onClose() {
    $this->removeAll();
    $this->setChildren($this->nodes);
  }
}

?>