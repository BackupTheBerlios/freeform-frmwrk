<?

/**
 * This is a helper action that implements the <class>Document</class> interface to display
 * a resource image by the <class>HTMLImage</class> tag. Requires proper installation of GD.
 * The images served will be marked with the <tt>ETag</tt> response header, and upon subsequent
 * requests, it will be compared with the <tt>If-None-Match</tt> request header; and if there is 
 * a match, only the <tt>HTTP/1.1 304 Not Modified</tt> header will be returned.
 * @since 1.1.1.Alpha
 * @author Dennis Popel
 */
class HTMLShowImage extends Action implements Document {
  private $fileName;
  private $mimeType;
  private $notModified = false;
  private $packageName;
  private $body;
  
  function process() {
    $this->success = false;
    if(($this->packageName = $this->getRequest()->getParameter('package')) && 
      ($this->fileName = $this->getRequest()->getParameter('name'))) {
      if($pkg = Package::getPackageByName($this->packageName)) {
        $filePath = $pkg->getResourcePath($this->fileName);
        if($this->body = file_get_contents($filePath)) {
          if($ii = getImageSize($filePath)) {
            $this->mimeType = $ii['mime'];
            if($etag = $this->getRequest()->getHeader('If-None-Match')) {
              $this->notModified = $etag == md5($this->body);
            }
            $this->success = true;
          }
        }
      }
    }
      
    $this->getResponce()->setDocument($this);
  }
  
  function getHeaders() {
    if($this->success) {
      if($this->notModified) {
        return array('HTTP/1.1 304 Not Modified');
      } else {
        return array(
          'ETag: ' . md5($this->body),
          'Content-Length: ' . strLen($this->body),
          'Content-Type: ' . $this->mimeType,
          'Content-Disposition: attachment; filename=' . $this->fileName);
      }
    } else {
      return array();
    }
  }
  
  function getBody() {
    if($this->success && !$this->notModified) {
      return $this->body;
    } else {
      return null;
    }
  }
}

?>