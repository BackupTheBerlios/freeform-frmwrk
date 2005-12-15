<?

/**
 * This form field allows to create upload fields and validate uploaded files (check size, mime and whether
 * the file was uploaded).
 *
 * @author Dennis Popel  
 * @since 1.0.0 
 */
class UploadFileInputField extends InputField {
  private $maxSize = 100000;
  private $mimeTypes = array('*/*');
  private $required = false;
  
  /**
   * Create a new upload file input field
   *
   * @param  int $maxSize  maximum allowed size of the file
   * @param  array $mimeTypes  array of MIME types that can be uploaded
   * @param  bool $required  flag to signal that the file is required to be uploaded
   */
  function __construct($maxSize = 100000, $mimeTypes = array('*/*'), $required = false) {
    $this->maxSize = $maxSize;
    $this->mimeTypes = $mimeTypes;
    $this->required = $required;
  }
  
  /**
   * Validate the field
   *
   * @return  bool  true if the file was uploaded, its size is less than maximum 
	 *                allowed, its MIME type is listed in the accepted, or if
	 *                there was no upload and the field is not marked required
	 */
  function isValid() {
    $v = $this->getValue();
    if($v === '') {
      // The file was not uploaded, see if it was required and quit
      return !$this->required;
    }
    // If we are here, the form was submitted
    if($v instanceof UploadedFile) {
      if($v->getSize() <= $this->maxSize) {
        error_log('UploadedFileInputField::isValid(): ' . $v->getMimeType());
        foreach($this->mimeTypes as $mime) {
          $mime = str_replace('*', '.+', $mime);
          if(eregi($mime, $v->getMimeType())) {
            return true;
          }
        }
        return false;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

?>