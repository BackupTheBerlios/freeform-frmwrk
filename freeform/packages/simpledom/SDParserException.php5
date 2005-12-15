<?

/**
 * Exception of this type is thrown whenever <class>SDParser</class> encounters errors in source XML document.
 * If this exception occurs, it means that the parsing was unsuccessful; however, this
 * exception will still return partially parsed document in a call to its 
 * <method>SDParserException::getRoot</method> method (if any element could be successfully parsed).
 * 
 * This class also defines several error numbers to supplement the xml extension
 * error reporting (like file not found or CDATA before root etc.)
 * @since 1.1.0
 * @see <package>simpledom</package>
 */
class SDParserException extends Exception {
  /**
   * These constants supplement the XML extension error codes 
   */
  const FILE_NOT_FOUND_OR_EMPTY = -1;
  const NON_ELEMENT_BEFORE_ROOT = -2;
  
  private $fileName = '';
  private $lineNumber = 0;
  private $column = 0;
  private $root = null;
  
  // This holds the custom error strings, defined in this class
  private static $errorStrings = array(
    SDParserException::FILE_NOT_FOUND_OR_EMPTY => 'File not found or empty',
    SDParserException::NON_ELEMENT_BEFORE_ROOT => 'Non-element (either a CDATA or ENTITY) appears before the root element'
  );

  /**
   * Create parsing exception
   * @param  string $msg  the error message
   * @param  int $errNo  the error number
   * @param  SDElement $root  the partially parsed root element
   * @param  int $line  line number where the error occured
   * @param  int $col  column number where the error occured
   * @param  string $file  the file name 
   */
  function __construct($errNo = 0, $root = null, $line = 0, $col = 0, $file = '') {
    // !!! This is the most stupid bug in ZE2 !!! - displays a popup box!!! Can you imagine???
    // parent::__construct('', $errNo);
    $this->code = $errNo;
    $this->lineNumber = $line;
    $this->column = $col;
    $this->fileName = $file;
    $this->root = $root;
    $this->buildMessage();
  }

  /**
   * Call this to set the file name parsing of which caused this exception
   * @param  string $file  the source XML file name
   */
  function setFileName($file) {
    $this->fileName = $file;
    $this->buildMessage();
  }

  function getFileName() {
    return $this->fileName;
  }

  function getLineNumber() {
    return $this->lineNumber;
  }

  function getColumn() {
    return $this->column;
  }
  
  function getRoot() {
    return $this->root;
  }

  private function buildMessage() {
    $this->message =
      'SDParserException: ' . ($this->code < 0 ? $this->getErrorString($this->code) : xml_error_string($this->code)) . 
      ' (' . $this->code . ') at line ' .
      $this->lineNumber . ', column ' . $this->column;
    if($this->fileName) {
      $this->message .= ' in file ' . $this->fileName;
    }
  }
  
  private function getErrorString($code) {
    return self::$errorStrings[$code];
  }
}

?>
