<?

/**
 * This class is the Forms Demo. It shows a simple form with several fields. The
 * processing of the form is made by this action also. Upon success, the form will be hidden,
 * and a thank-you message will be displayed. In case of errors, the error details will
 * be shown, followed by the form itself.
 * @author Dennis Popel
 */
class FDForms extends FDDemo {
  /**
   * This is the method to execute the demo
   */
  function process() {
    // We always call the parent's method
    parent::process();
    $d = $this->getDocument();
    
    // We pass the form our request object and the location of us so the form
    // will make us handle the input (as actually we take care of processing
    // this form) - it could link to any other action as long as it is aware
    // of the form
    $form = new Form($this->getRequest(), new Location($this), Request::METHOD_POST);
    
    // This is how you prepare values for the radio buttons
    $radios = array('visa', 'master');
    
    // This is how we prepare the fields
    $form->addField('text1', new TextInputField('Your name here please', 
       new RegexValidator('^[[:alpha:]]+[[:space:]][[:alpha:]]+$')));
    $form->addField('pass1', 
       new TextInputField('', new RegexValidator('^[[:digit:]]{16}$'), TextInputField::PASSWORD));
    $form->addField('text2', new TextInputField('', null, TextInputField::TEXTAREA));
    $form->addField('check1', new CheckBoxInputField());
    $form->addField('payment', new RadioButtonInputField('visa', $radios));
    
    // Here we test if the form was correctly submitted
    if($form->isValidSubmission()) {
      // Normally, we would do something useful here and redirect to another page
      // since this form uses the POST method
      $d->setVariable('success', true);
    }
    
    // Here we place the form into the document variable so it can process it
    $d->setVariable('form', $form);
  }
  
  // These methods are internal to the demos system
  function getTitle() {
    return 'Forms Demo';
  }
  
  function getPrev() {
    return new Location('FDLinks');
  }
  
  function getNext() {
    return new Location('FDRCContent');
  }
  
  static function getDescription() {
    return 'Forms';
  }
}

?>