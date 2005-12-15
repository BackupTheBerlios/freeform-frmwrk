<?

/**
 * This class is a convenient way to handle uploaded files. The <method>Request::getParameter</method> will
 * return instances of this class if the parameter name designates an uploaded file.
 *
 * @author Dennis Popel  
 * @since 1.0.0
 */
class UploadedFile {
  private $name;
  private $mime;
  private $size;
  private $localName;
  private $error;
  
  /**
   * Construct new UploadedFile object from an entry in $_FILES array. These objects
   * are created by Request::parseHTTP().
   *
   * @param  array $fileInfo  entry in $_FILES array
   */
  function __construct($fileInfo) {
    $this->name = str_replace('\\', '/', $fileInfo['name']);
    $this->mime = $fileInfo['type'];
    $this->localName = $fileInfo['tmp_name'];
    $this->size = $fileInfo['size'];
    $this->error = $fileInfo['error'];
  }
  
  /**
   * Get contents of the file
   *
   * @return  string  uploaded file content
   */
  function getContent() {
    return file_get_contents($this->localName);
  }
  
  /**
   * Move the uploaded file to $path
   *
   * @param  string $path  destination path
   */
  function copyTo($path) {
    copy($this->localName, $path);
  }
  
  /**
   * Get the original file name
   *
   * @return  string  original file name
   */
  function getName() {
    return $this->name;
  }
  
  /**
   * Get the local name of the uploaded file
   *
   * @return  string  local temporary file name
   */
  function getLocalName() {
    return $this->localName;
  }
  
  /**
   * Get the size of uploaded file in bytes
   *
   * @return  int  file size
   */
  function getSize() {
    return $this->size;
  }
  
  /**
   * Get the mime type of the file as reported by the user agent
   *
   * @return  string  mime type
   */
  function getMimeType() {
    return $this->mime;
  }
}

?>