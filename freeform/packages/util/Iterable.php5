<?

/**
 * This interface is a replacement for SPL Iterator since the latter
 * has a somewhat strange naming. Moreover, the behaviour is also slightly 
 * different - a call to <method>Iterable::hasMore()</method> returns true if there are more items
 * in the iterable, but <method>Iterable::getNext()</method> returns the next item AND advances the 
 * iterable so that subsequent calls to this method will fetch new items.
 * @author Dennis Popel  
 * @since 1.0.0
 */
interface Iterable {
  /**
   * Return true if more elements available
   * @return  bool  if there are more elements
   */
  function hasMore();
  
  /**
   * Return next element and advance pointer
   * @return  mixed  next item in the iterator
   * @throws  NoMoreElementsException  if there were no more items
   */
  function getNext();
  
  /**
   * Rewind this iterable to the before-the-first position. A call to the <method>Iterable::getNext</method>
   * will fetch the first item. Note that in PaginatedIterable this method will rewind to the
   * first item on the current page.
   * 
   * Note: In order to avoid name collision method name has been changed. Previous name was "rewind"
   * @since 1.2.0.Alpha   
   */
  function reset();
}

?>