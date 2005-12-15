<?

/**
 * This tag is used to embed dynamic images that are generated by other action rather that are
 * static files. You specify the location of the action with the <tt>key</tt> attribute
 * that must point to a valid <class>Location</class> object.
 *
 * Since 1.1.1 you also can show resource images. This tag 
 * will employ content negotiation techniques to allow the remote client to cache the image.
 * To show a resource image, you specify the <tt>name</tt> attribute and optionally the
 * <tt>package</tt> attribute to locate the image. If you do not specify the <tt>package</tt>
 * then this tag will assume the image is located in the package where the default action
 * was declared (i.e., it will analyze the <tt>action.default</tt> configuration option
 * from the <package>freeform</package> package.
 *
 * If in the template there is no <tt>alt</tt> attribute, this tag will set it to the file 
 * name of the image.
 * @since 1.1.0
 * @author Dennis Popel
 */
class HTMLImage extends HTMLTag {
  private $key;
  private $package;
  private $name;
  private $alt;
  
  function onInit() {
    $this->setName('img');
  }
  
  function onOpen() {
    if($this->key || $this->key = $this->removeAttribute('key')) {
      $this->setAttribute('src', $this->getDocument()->getVariable($this->key)->toURL());
      return self::PROCESS_BODY;
    } elseif($this->name || $this->name = $this->removeAttribute('name')) {
      if($this->package || $this->package = $this->removeAttribute('package')) {
        $p = $this->package;
      } else {
        $p = Package::getPackageNameForClass(
          Package::getPackageByName('freeform')->getProperty('action.default'));
      }
      if($p) {
        $l = new Location('HTMLShowImage', array('package'=>$p, 'name'=>$this->name));
        $this->setAttribute('src', $l->toURL());
        if($this->alt || $this->alt = !$this->getAttribute('alt')) {
          $this->setAttribute('alt', $this->name);
        }
        return self::PROCESS_BODY;
      } else {
        return self::SKIP_BODY;
      }
    }
    return self::SKIP_BODY;
  }
}

?>