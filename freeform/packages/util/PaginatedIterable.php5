<?

/**
 * The PaginatedIterable interface is an extension to the <class>Iterable</class> interface and 
 * allows to paginate entities that are iterated over.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
interface PaginatedIterable extends Iterable {
  /**
   * This should be the first method called. Normally PaginatedIterable 
	 * implementations must call this method to initialize one-page pagination 
	 * with page size set to number of rows in the iterator.
   * @param  int $size  page size
   */
  function setPageSize($size);
  
  /**
   * Return current page size
   * @return  int  page size
   */
  function getPageSize();
  
  /**
   * Return current page
   * @return  int  page
   */
  function getPage();
  
  /**
   * Set current page
   * @param  int  page number
   */
  function setPage($page);
  
  /**
   * Get the first row on current page
   * @return  int  first row number of current page   
   */
  function getFirstRowNumber();
  
  /**
   * Get the last row on current page, it may be less than
   * getFirstRowNumber() + pageSize - 1 if the page selected is the last page and 
   * it contains less rows that pageSize. 
   * @return  int  last row number of current page
   */
  function getLastRowNumber();
  
  /**
   * Get total number of rows int the iterable
   * @return  int  total number of rows
   */
  function getRowsCount();
  
  /**
   * Get total number of pages int the iterable
   * @return  int  total number of pages
   */
  function getPagesCount();
}

?>