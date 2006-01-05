<?

/**
 * This tag is used to iterate over on <class>Iterable</class> and repeat its content on each
 * iteration. You specify the iterable in the sole <tt>key</tt> attribute of this tag which
 * holds the template variable name of the iterable object.
 *
 * If the value returned by the <method>Iterable::getNext()</method> is non-scalar
 * (i.e., it is an array or an object), its key-value pairs or properties will be extracted into
 * the current variable stack so that they can be later accessed by the {%varName} construct.
 * If the value returned is scalar, it will be available via the {%html.showiterable.value}
 * variable in the body of this tag.
 *
 * The tag will also maintain current position counter in the {%html.showiterable.position} 
 * variable. If the iterable is an instance of the <class>PaginatedIterable</class> then
 * the position counter will hold the actual (real) position of iterable, taking into
 * account current page number. Besides, the following variables will be defined:
 *
 * {%html.showiterable.start} - the starting row number<br/>
 * {%html.showiterable.end} - the ending row number<br/>
 * {%html.showiterable.page} - the current page<br/>
 * {%html.showiterable.pagescount} - the total number of pages in the iterable<br/>
 * {%html.showiterable.total} - the total number of rows in the iterable (regardless of paging)<br/>
 * 
 * This tag also recognizes some pseudo-tags:
 * <ul>
 *  <li>separator - defines a piece of markup that will be output after each iteration if there
 *      still are more items</li>
 *  <li>ifnotempty - defines a piece of markup that will be output before the first iteration</li>
 *  <li>ifempty - defines a piece of markup that will be output if the iterable is empty</li>
 *  <li>ifodd - will be displayed on every odd iteration</li>
 *  <li>ifeven - will be displayed on every even iteration</li>
 * </ul>
 *
 * @since 1.0.0
 * @author Dennis Popel
 */
class HTMLShowIterable extends HTMLTag {
  private $iterable = null;
  private $ifOdd = null;
  private $ifEven = null;
  private $ifEmpty = null;
  private $ifNotEmpty = null;
  private $separator = null;
  private $counter = false;
  
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    $t[] = $this->ifOdd = $this->getTagByName('ifodd');
    $t[] = $this->ifEven = $this->getTagByName('ifeven');
    $t[] = $this->ifEmpty = $this->getTagByName('ifempty');
    $t[] = $this->ifNotEmpty = $this->getTagByName('ifnotempty');
    $t[] = $this->separator = $this->getTagByName('separator');
    foreach($t as $tag) {
      if(!is_null($tag)) {
        $tag->setExposed(false);
      }
    }  
    
    $key = $this->getAttribute('key');
    $this->iterable = $this->getDocument()->getVariable($key);
    
    if(!$this->iterable || !$this->iterable->hasMore()) {
      if($this->ifEmpty) {
        $this->ifEmpty->setEnabled(true);
        $this->getDocument()->process($this->ifEmpty);
      }
      return self::SKIP_BODY;
    } 
    
    if($this->iterable instanceof PaginatedIterable) {
      $this->getDocument()->setVariable('html.showiterable.start', $this->counter = $this->iterable->getFirstRowNumber());
      $this->getDocument()->setVariable('html.showiterable.end', $this->iterable->getLastRowNumber());
      $this->getDocument()->setVariable('html.showiterable.page', $this->iterable->getPage());
      $this->getDocument()->setVariable('html.showiterable.pagescount', $this->iterable->getPagesCount());
      $this->getDocument()->setVariable('html.showiterable.total', $this->iterable->getRowsCount());
    } else {
      $this->counter = 1;
    }
    
    if($this->ifEmpty) {
      $this->ifEmpty->setEnabled(false);
    }
    if($this->separator) {
      $this->separator->setEnabled(false);
    }
    if($this->ifNotEmpty) {
      $this->ifNotEmpty->setEnabled(true);
      $this->getDocument()->process($this->ifNotEmpty);
      $this->ifNotEmpty->setEnabled(false);
    }
      
    return self::PROCESS_BODY;
  }
   
  function onBeforeBody() {
    if($this->ifOdd) {
      $this->ifOdd->setEnabled($this->counter % 2 == 0);
    }
    if($this->ifEven) {
      $this->ifEven->setEnabled($this->counter % 2 != 0);
    }
    
    $this->getDocument()->setVariable('html.showiterable.position', $this->counter);
    $item = $this->iterable->getNext();
    $this->getDocument()->setVariable('html.showiterable.value', $item);
    if(is_array($item) || is_object($item)) {
      foreach($item as $k=>$v) {
        $this->getDocument()->setVariable($k, $v);
      }
    }
  }
  
  function onAfterBody() {   
    $this->counter++;
    if($this->separator && $this->isRepeatBody()) {
      $this->separator->setEnabled(true);
      $this->getDocument()->process($this->separator);
      $this->separator->setEnabled(false);
    }
  }
  
  function isRepeatBody() { 
    return $this->iterable->hasMore();
  }
  
  /**
   * This method will rewind the iterable for possible later reuse
   * @since 1.2.0.Beta
   */
  function onClose() {
    if($this->iterable instanceof Iterable) {
      $this->iterable->reset();
    }
    parent::onClose();
  }
}

?>