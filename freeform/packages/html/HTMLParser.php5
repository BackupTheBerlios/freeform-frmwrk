<?
     
/**
 * <class>HTMLParser</class> is used by <class>HTMLDocument</class> to parse the 
 * template file and build the tag tree. You can use instances of this class to
 * create <class>HTMLTag</class> trees for later use in your programs.
 *
 * Since version 1.2.0.Alpha this parser supports serialization of the parsed tree
 * into a cache and then retrieving cached results. The simplest tests show this
 * gives dramatic improvement in the performance. The cache is auto-managed: the parser
 * compares the last-modified time of the source file and compares it to the cached version.
 * @author Dennis Popel
 * @see <package>simpledom</package> package
 * @since 1.0.0
 */
class HTMLParser extends SDParser {  
  private $doc = null;
  private $lastTextNode = null;
  
  function __construct(HTMLDocument $doc) {
    $this->doc = $doc;
  }
  
  /**
   * This method is overridden to possibly cache (serialize) the element tree if the caching
   * is enabled in the package configuration file.
   * @param  string $fileName  the absolute file name to parse
   * @return  SDElement  the root element of the tree, possibly retrieved from the cache
   * @throws  SDParserException  if something goes wrong
   */
  function parse($fileName) {
    $pkg = Package::getPackageByName('html');
    if($pkg->getProperty('cache.parserCache', false)) {
      $path = $pkg->getProperty('cache.parserCachePath', '/tmp');
      $cacheFile = $path . '/' . crc32($fileName) . '_' . baseName($fileName);
      if(file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($fileName)) {
        $rv = unSerialize(file_get_contents($cacheFile));
        $rv->setDocument($this->doc);
        return $rv;
      } else {
        $rv = parent::parse($fileName);
        $rv->setDocument(null);
        flock($fp = fopen($fileName, 'r'), LOCK_EX);
        file_put_contents($cacheFile, serialize($rv));
        flock($fp, LOCK_UN);
        touch($cacheFile, filemtime($fileName));
        $rv->setDocument($this->doc);
        return $rv;
      }
    } else {
      return parent::parse($fileName);
    }
  }
  
  function onTagOpen($parent, $name, $attrs) {
    if(!$this->getRoot()) {
      $rv = new HTMLRootNode($this->doc, $name, $attrs);
      return $rv;
    }
    try {
      $rc = new ReflectionClass($name);
      if($rc->isSubclassOf(new ReflectionClass('HTMLTag'))) {
        $tag = $rc->newInstance($parent, $name, $attrs);
      } else {
        $tag = new HTMLTag($parent, $name, $attrs);
      }
    } catch(ReflectionException $re) {
      $tag = new HTMLTag($parent, $name, $attrs);
    }        
    $this->lastTextNode = null;
    return $tag;
  }     
  
  // Here we enforce the last text node to null since xml parser "breaks"
  // entire text node into several smaller. This parser will assure that
  // all consecutive CData blocks are represented by a single HTMLTextNode
  function onTagClose(SDElement $e) {
    $this->lastTextNode = null;
  }
  
  function onCData($parent, $data) {
    if($this->lastTextNode) {
      $this->lastTextNode->setContent(
			  $this->lastTextNode->getUnparsedContent() . $data);
    } else {
      $this->lastTextNode = new HTMLTextNode($parent, $data);
      return $this->lastTextNode;
    }
  }    
  
  /**
   * Here we override the default method to create an anonymous function
   * that will execute the PI code
   * @param  SDElement $parent  the parent element
   * @param  string $target  the target of the PI
   * @param  string $data  the code
   * @return  SDProcessingInstruction  the resulting PI element with the function property
   */
  function onProcessingInstruction($parent, $target, $data) {
    $this->lastTextNode = null;
    if(strToLower($target) == 'php') { 
      return new HTMLPINode($parent, $target, $data);
    } else {
      return null;
    }
  }
}    

?>