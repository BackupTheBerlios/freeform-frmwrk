<?

/**
 * This class is a <class>PaginatedIterable</class> wrapper around PDOStatement
 * that is used to fetch results of a SELECT query
 * @since 1.2.0.Beta
 * @author Dennis Popel
 */
class IterablePDOResult implements PaginatedIterable {
  private $data;
  private $page;
  private $pageSize;
  private $total;
  private $pos;
  
  /**
   * Create a new instance, passing the PDOStatement over which to iterate, as
   * well as optional fetch mode constant<br />
   * <b>Note:</b> you will have to use either the PDO_FETCH_xxxx or PDO::FETCH_xxxx notation depending on the PHP version you have.
   * @param  PDOStatement $stmt  the statement to iterate
   * @param  int $fetchMode  the fetch mode to use (PDO::FETCH_BOTH, PDO::FETCH_ASSOC, PDO::FETCH_OBJ, PDO::FETCH_NUM, PDO::FETCH_LAZY). Note you cannot use other constants in this iterable
   */
  function __construct(PDOStatement $stmt, $fetchMode = PDO::FETCH_ASSOC) {
    $this->data = $stmt->fetchAll($fetchMode);
    $this->total = count($this->data);
    $this->setPageSize($this->total);
  }
  
  function setPageSize($pageSize) {
    $this->pageSize = $pageSize;
    $this->setPage(1);
  }
  
  function setPage($page) {
    $this->page = min($this->getPagesCount(), $page);
    $this->pos = max($this->getFirstRowNumber() - 1, 0);
  }
  
  function getNext() {
    if($this->hasMore()) {
      return $this->data[$this->pos++];
    } else {
      throw new NoMoreElementsException();
    }
  }
  
  function hasMore() {
    return $this->pos < count($this->data);
  }
  
  function reset() {
    $this->setPage($this->page);
  }
  
  function getRowsCount() {
    return $this->total;
  }
  
  function getPagesCount() {
    return ceil($this->total / $this->pageSize);
  }

  function getFirstRowNumber() {
    return min(($this->page - 1) * $this->pageSize + 1, $this->total);
  }
  
  function getLastRowNumber() {
    return min($this->total, $this->getFirstRowNumber() + $this->getPageSize()); 
  }
  
  function getPageSize() {
    return $this->pageSize;
  }
  
  function getPage() {
    return $this->page;
  }
}

?>