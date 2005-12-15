<?

/**
 * This class is an Iterable wrapper around PEAR::DB_result. 
 * It allows to treat DB_result as a PaginatedIterable. This means that
 * you can paginate it in a more comfort way.
 */
class IterablePearDbResult implements PaginatedIterable {
  private $result = null;
  private $current = null;
  private $pageSize = 0;
  private $page = 0;
  private $rows = 0;
  private $pos = 0;
  
  /** 
   * Construct an IterablePearDbResult for DB_result
   * @param  DB_result $result  the DB result to iterate over
	 */ 
  function __construct($result) {
    if($result instanceof DB_result) {
      $this->result = $result;
      $this->rows = $result->numRows();
    }
    $this->setPageSize($this->getRowsCount());
  }
  
  /**
   * Returns true if the result set has more records
   * @return  bool  true if more records available
   */
  function hasMore() {
    if($this->pos <= $this->getLastRowNumber() + 1) { 
      return !is_null($this->current) && !DB::isError($this->current);
    } else {
      return false;
    }
  }
  
  /**
   * Get the next record. Records are fetched with the DB_FETCHMODE_DEFAULT
   * setting, so you can program it when you connect to the database
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
    if($this->result) {
      $this->current = $this->result->fetchRow(DB_FETCHMODE_DEFAULT, $this->pos - 1);
    }
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
    $this->advance();
  }
  
  function getPage() {
    return $this->page;
  }
  
  function rewind() {
    $this->pos = $this->getFirstRowNumber();
  }
}

?>