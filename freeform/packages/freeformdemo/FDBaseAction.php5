<?

/**
 * This is the base class for every action in the <package>freeformdemo</package> package.
 * It takes care of preparing the responce document and setting the templates. 
 * All demos will have templates that are named same as the action class plus the '.html'
 * extension.
 * @author Dennis Popel
 */
abstract class FDBaseAction extends Action {
  private $document;
  
  /**
   * As we have the same document preparation process in every demo action,
   * we difine this onInit method to take care of this
   */
  function onInit() {
    $this->document = new HTMLDocument($this->getResponce());
    $demos = FDRouterForm::getDemos();
    $this->document->setTemplate($this->getPackage()->getResourcePath('template.html'));
    $this->document->setVariable('main', get_class($this) . '.html');
    $this->document->setVariable('title', $this->getTitle());
    $this->document->setVariable('freddy', Package::getPackageByName('freddy'));
  }
  
  /**
   * Normally demo actions override this to set additional template variables
   */
  function process() {
    $this->getResponce()->setDocument($this->getDocument());
  }
  
  /**
   * This is overridden to return the page title that will be used in the &lt;title&gt; 
   * attribute of every resulting page
   */
  abstract function getTitle();
  
  /**
   * This will return the document prepared by the <method>FDBaseAction::onInit</method>()
   * method
   */
  function getDocument() {
    return $this->document;
  }
}

?>