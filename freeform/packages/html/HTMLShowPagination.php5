<?

/**
 * This tag is used to display a pagination bar for a <class>PaginatedIterable</class>. 
 * It allows you to easily format the pagination bar, display stylish header or footer,
 * use images for prev/next/first/last links etc.
 *
 * Essentially it will generate a series of <class>Location</class> objects that will
 * point to the current action but will have a special parameter <tt>page</tt> set to point
 * to different page numbers. So doing, you will have just one action that can easily display
 * differene pages of a longer iterable.
 *
 * It requires the <tt>key</tt> attribute to be present and to point to a paginated iterable object.
 * You can optionally specify the <tt>displaypages</tt> attribute to set the number of page links
 * shown on a single page. If there are more pages than this value, the entige bar will scroll, 
 * trying to place the current page number in the center.
 *
 * For every page number, the following variables are created:
 *
 * {%html.showpagination.link} - the <class>Location</class> object for every page<br/>
 * {%html.showpagination.page} - the current page number<br/>
 * 
 * This tag also recognizes some pseudo-tags:
 * <ul>
 *  <li>separator - defines a piece of markup that will be output after each iteration if there
 *      still are more pages</li>
 *  <li>ifnotempty - defines a piece of markup that will be output before the first iteration</li>
 *  <li>ifempty - defines a piece of markup that will be output if the iterable is empty</li>
 *  <li>firstpage - will be displayed before everything else; you can create the link to the first
 *      page here</li>
 *  <li>prevpage - will be displayed after firstpage pseudo-tag; use it to create a link to 
 *      the previous page </li>
 *  <li>nextpage - will be displayed after the bar; you can create the link to the next
 *      page here</li>
 *  <li>lastpage - will be displayed last; use it to create a link to the last page </li>
 * </ul>
 * Inside the last foru pseudo-tags you use the {%html.showpagination.link} and 
 * {%html.showpagination.page} variables to create the links.
 *
 * @since 1.0.0
 * @author Dennis Popel
 */
class HTMLShowPagination extends HTMLTag {
  private $pageSize;          // How many items on page
  private $total;             // How many items
  private $pageCount;         // How many pages total 
  private $displayPageCount;  // How many page numbers to show on bar
  private $startPage;         // First displayed page number 
  private $endPage;           // Last displayed page number
  private $currentPage;       // Page being displayed 
  
  private $iterable;         // Source result set  
  private $action;            // Action to take when a user clicks on a page number 
  private $parameter;         // Parameter name that will contain new page number
  
  private $separator = NULL;
  private $prevPage = NULL;
  private $nextPage = NULL;
  private $firstPage = NULL;
  private $lastPage = NULL;
  private $curPage = NULL;
  private $ifNotEmpty = null;
  
  private $current = 0;
  
  function onOpen() {
    $d = $this->getDocument();   
    $key = $this->getAttribute('key');
    $this->setExposed(false);
    if(($this->iterable = $d->getVariable($key)) instanceof PaginatedIterable) { 
      $this->pageSize = $this->iterable->getPageSize();
      $this->total = $this->iterable->getRowsCount();
      
      $this->pageCount = $this->iterable->getPagesCount();
      $this->displayPageCount = $this->getAttribute('displaypages', 10);
    
      if($this->pageCount <= 1) {
        return self::SKIP_BODY;
      }
      
      $this->currentPage = $this->iterable->getPage();
    
      if($this->pageCount > $this->displayPageCount) {
        $this->startPage = max(1, $this->currentPage - (int)(floor($this->displayPageCount / 2)));
        $this->endPage = min($this->pageCount, $this->startPage + $this->displayPageCount - 1);
        $this->startPage -= max(0, ($this->displayPageCount - ($this->endPage - $this->startPage + 1)));
      } else {
        $this->startPage = 1;
        $this->endPage = $this->pageCount;
      }
      $this->current = $this->startPage - 1;
      
      $this->removeNode($this->separator = $this->getTagByName('separator'));
      $this->removeNode($this->prevPage = $this->getTagByName('prevpage'));
      $this->removeNode($this->nextPage = $this->getTagByName('nextpage'));
      $this->removeNode($this->firstPage = $this->getTagByName('firstpage'));
      $this->removeNode($this->lastPage = $this->getTagByName('lastpage'));
      $this->removeNode($this->curPage = $this->getTagByName('currentpage'));
      $this->removeNode($this->ifNotEmpty = $this->getTagByName('ifnotempty'));
      
      $this->location = clone $this->getDocument()->getResponce()->getRequest()->getLocation();
        
      return self::PROCESS_BODY;
    } else {
      return self::SKIP_BODY;
    }
  }
  
  function isRepeatBody() {
    return $this->current < $this->endPage;
  }
  
  function onBeforeBody() {
    $d = $this->getDocument();
    if($this->current < $this->startPage) {
      // We have not started yet, process the IfNotEmty
      if(!is_null($this->ifNotEmpty)) {
        $this->ifNotEmpty->setExposed(false);
        $d->process($this->ifNotEmpty);
      }
      // Check to display Prev Page/First Page
      if(!is_null($this->firstPage)) {
        $this->location->setParameter('page', '1');
        $this->firstPage->setExposed(false);
        $d->setVariable('html.showpagination.link', $this->location);
        $d->process($this->firstPage);
      }
      if(!is_null($this->prevPage)) {
        $this->location->setParameter('page', max($this->currentPage - 1, 1));
        $this->prevPage->setExposed(false);
        $d->setVariable('html.showpagination.link', $this->location);
        $d->process($this->prevPage);
      }  
    }
    // Advance page
    $this->current++;
    $this->location->setParameter('page', $this->current);
    $d->setVariable('html.showpagination.page', $this->current);
    $d->setVariable('html.showpagination.link', $this->location);
  }
  
  function onAfterBody() {
    $d = $this->getDocument();
    if($this->isRepeatBody()) {
      $d->process($this->separator);
    } else {
      if(!is_null($this->nextPage)) {
        $this->location->setParameter('page', min($this->currentPage + 1, $this->pageCount));
        $this->nextPage->setExposed(false);
        $d->setVariable('html.showpagination.link', $this->location);
        $d->process($this->nextPage);
      }
      if(!is_null($this->lastPage)) {
        $this->location->setParameter('page', $this->pageCount);
        $this->lastPage->setExposed(false);
        $d->setVariable('html.showpagination.link', $this->location);
        $d->process($this->lastPage);
      }  
    }
  }
}

?>