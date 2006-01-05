<?

/**
 * This class is a PaginatedIterable wrapper around ADODB RecordSet object that allows to
 * iterate and paginate it in the comfort of Freeform iterators. 
 * @author Dennis Popel
 * @since 1.1.1.Alpha
 */
class IterableADODBRecordSet implements PaginatedIterable {
  private $result = null;
  private $current = null;
  private $pageSize = 0;
  private $page = 0;
  private $rows = 0;
  private $pos = 0;
  
  /** 
   * Construct an IterableADODBRecordSet for ADODBRecordSet
   *
   * @param  ADODBRecordSet $result  the DB result to iterate over
	 */ 
  function __construct($result) {
    if($result instanceof ADODBRecordSet) {
      $this->result = $result;
      $this->rows = $result->RecordCount();
    }
    $this->setPageSize($this->getRowsCount());
  }
  
  /**
   * Returns true if the result set has more records
   *
   * @return  bool  true if more records available
   */
  function hasMore() {
    if($this->pos <= $this->getLastRowNumber() + 1) { 
      return !is_null($this->current);
    } else {
      return false;
    }
  }
  
  /**
   * Get the next record. Records are fetched in the default fetch mode of ADODB,
   * so you can program it when you connect to the database
   *
   * @return  mixed  the next record
   */
  function getNext() {
    if($this->hasMore()) {
      $rv = $this->current;
      $this->advance();
      return $rv;
    } else {
      throw new NoMoreElementsException();
    }
  }
  
  private function advance() {
    $this->current = $this->result->FetchRow();
    $this->pos++;
  }
  
  function getRowsCount() {
    return $this->rows;
  }
  
  function setPageSize($pageSize) {
    $this->pageSize = max(0, min($pageSize, $this->getRowsCount()));
    $this->setPage(1);
  }
  
  function getPageSize() {
    return $this->pageSize;
  }
  
  function getFirstRowNumber() {
    return ($this->page - 1) * $this->pageSize + 1;
  }
  
  function getLastRowNumber() {
    return min($this->getRowsCount(), $this->page * $this->pageSize);
  }
  
  function getPagesCount() {
  	if($this->getPageSize() > 0) {
      return (int)ceil($this->getRowsCount() / $this->getPageSize());
  	} else {
  	  return 0;
  	}
  }
  
  function setPage($i) {
    $this->page = max(1, min($i, $this->getPagesCount()));
    $this->pos = $this->getFirstRowNumber();
    if($this->result) {
      $this->result->Move($this->pos - 1);
      $this->advance();
    }
  }
  
  function getPage() {
    return $this->page;
  }
  
  function reset() {
    $this->pos = $this->getFirstRowNumber();
  }
}

?>