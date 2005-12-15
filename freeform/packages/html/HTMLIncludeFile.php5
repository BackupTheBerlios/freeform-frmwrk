<?

/**
 * This tag will include external template files. Effectively it will instantiate new 
 * <class>HTMLParser</class> to parse the template file. The template to include can be 
 * specified in these ways:
 * <ol>
 *   <li>by absolute file name - just set the <tt>key</tt> attribute to contain template
 *     variable name that holds the name of the file;</li>
 *   <li>by resource name - set the <tt>name</tt> attribute to contain the name of the 
 *     resource. The resource will be searched in the package where the calling action
 *     (the action that created the document) is located. You can additionally specify
 *     the <tt>package</tt> attribute to explicitly state where the resource is located.</li></ol>
 * For example, suppose that your action has set the <tt>footer</tt> template variable to
 * be '/home/www/templates/footer.html', the following tag
 *
 * <html>
 * <HTMLIncludeFile key="footer" />
 * </html>
 *
 * will insert the content of the parsed <i>/home/www/templates/footer.html</i> file. To be
 * more specific, the HTMLIncludeFile will create the <class>HTMLParser</class> and parse the
 * specified file with it. Then the result of the parsing will be appended as the child node
 * of this tag.
 *
 * If you specify the <tt>name</tt> and <tt>package</tt> attributes like 
 * 
 * <html>
 * <HTMLIncludeFile name="languages.html" package="mymultilingualsite.com" />
 * </html>
 *
 * then this tag will parse and append the result of the parsing of the <i>languages.html</i>
 * resource in the <i>mymultilingualsite.com</i> package.
 *
 * Please note that the root node of the file included will be set to non-exposed mode, so you
 * will have to enclose the content of the included file in, say, &lt;html&gt; container.
 *
 * If the included file cannot be found, an exception will be thrown. 
 * This tag can be safely used in the iterated content.
 */
class HTMLIncludeFile extends HTMLTag {
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    $this->removeAll();
    $d = $this->getDocument();
    
    if(!($name = $this->getAttribute('name'))) {
      $name = $d->getVariable($this->getAttribute('key'));
    } else {
      if(!($pkg = $this->getAttribute('package'))) {
        // Try to find current package (where calling action defined)
        if(!($a = $d->getResponce()->getRequest()->getParameter('action'))) {
          $pkg = Package::getPackageByName('freeform');
          if($a = $pkg->getProperty('action.default')) {
            $pkg = Package::getPackageByName(Package::getPackageNameForClass($a));
          }
        } else {
          $pkg = Package::getPackageByName(Package::getPackageNameForClass($a));
	      }
      } else {
        echo $pkg;
        $pkg = Package::getPackageByName($pkg);
      }
      if($pkg) {
        $name = $pkg->getResourcePath($name);
      }
    }

    if($name) {
      $p = new HTMLParser($this->getDocument());
      $r = $p->parse($name);
      if($r) {
        $r->setExposed(false);
        $this->addNode($r);
        return self::PROCESS_BODY;
      } else {
        return self::SKIP_BODY;
      }
    }
    return self::SKIP_BODY;
  }
}

?>