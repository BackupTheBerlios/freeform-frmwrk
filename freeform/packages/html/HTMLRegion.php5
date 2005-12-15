<?

/**
 * This tag allows you to define regions within your templates. A region can be
 * shown later in the template, possibly at several places. The concept of region is
 * similar to that of an included template; however, regions are parsed only once, when
 * they are declared with this tag, while included files are parsed every time they
 * get loaded by <class>HTMLIncludeFile</class> tag.
 *
 * Regions are declared and retrieved by names. It is possible to create
 * a region outside the template (programatically) and to set it via a call to
 * <method>HTMLDocument::setRegion</method> to later show those regions in the resulting 
 * document. You show regions with the <class>HTMLShowRegion</class> tag in the template. 
 * The region can be forced to be shown by setting this tag's <tt>show</tt> attribute
 * to <tt>true</tt>.
 *
 * Names are declared with the <tt>name</tt> attribute of this tag. For example,
 *
 * <html>
 * <HTMLRegion name="header">
 *   <b>Welcome to our site</b>
 *   <hr/>
 * </HTMLRegion>
 * </html>
 *
 * will create a <i>header</i> region in the document. You can show this region later
 * with the following markup:
 *
 * <html>
 * <HTMLShowRegion name="header"/>
 * </html>
 *
 * that will display
 *
 * <html>
 *   <b>Welcome to our site</b>
 *   <hr/>
 * </html>
 *
 * Regions are handy when it comes to repeatable content, especially when the same template
 * is used by more than one <class>HTMLShowIterable</class> tags. For example, 
 * <package>freddy</package> uses the same region for displaying list of classes and interfaces
 * in the package viewer: the classes and interfaces are presented by the same data types
 * but stored in two different <class>Iterable</class>s.
 *
 * <b>Note</b><br/>
 * You should not use <class>HTMLIncludeFile</class> within a region for performance reasons.
 * The difference between the two is in that that the region is parsed only once upon its
 * definition while the included file is parsed on every inclusion. You can define all your
 * common regions in a single external template file and include it into every page with
 * the <class>HTMLIncudeFile</class> tag; in the page, you can reuse the regions as you need
 * to improve performance.
 *
 * @since 1.1.0
 * @author Dennis Popel
 */
class HTMLRegion extends HTMLTag {
  function isExposed() {
    return false;
  }
  
  function onOpen() {
    $this->getDocument()->setRegion($this->getAttribute('name'), $this);
    return $this->getAttribute('show') ? self::PROCESS_BODY : self::SKIP_BODY;
  }
}

?>
