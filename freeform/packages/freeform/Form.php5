<?

/**
 * The Form class serves to gather information about the form fields and to display them
 * in documents. Form will be considered valid if all input from user is validated. 
 * The form is considered submitted if all its fields are present in the request.
 * Normally the action that processes the form can be called with all parameters correctly
 * passed from the request, in such case it will detect it was submitted and perform
 * its operation. Doing so the same action that displays form can be called as, for
 * example, a remote service.
 *
 * The form detects it is a valid submisiion by the fact that all fields pass 
 * validation.
 *
 * The form's lifecycle is as follows:
 * <ol>
 * <li>The owning action instantiates the form and constructs its fields, passing them 
 *    the default values</li>
 * <li>The owning action calls the isSubmitted() method of the form which will detect
 *    the fact that all input is correct and return true or false as appropriate.</li>
 * <li>Based on that the action can check the consistency of the submitted data and 
 *    display results, forward or prompt to correct input.</li>
 * </ol>
 * 
 * The form has a way to carry parameters between requests - using the
 * setParameter() method. Such parameters will be available on subsequent request because
 * they will be included as hidden fields. By default all forms will be submitted 
 * with the POST method and all forms will have enctype='multipart/form-data', so 
 * they are ready to upload files.
 * Each uploaded file in a request will be represented by the <class>UploadedFile</class> instance. 
 * However, you can specify which HTTP method to use in the form constructor.
 *
 * The form upon validation will set the offending field values to NULLs - note 
 * how this differs from the values submitted by users - empty fields are carried 
 * as empty strings.
 * Note that HTML forms must render a special hidden fields of value '' for 
 * checkboxes since browsers DO NOT SEND ANYTHING when checkbox is unchecked. 
 * In such case the form will get never 'submitted' on the server side.
 *
 * Also note that you cannot trust any field values unless you are sure that the 
 * form was submitted. If you use field values and validate them before the call 
 * to Form::isSubmitted(), the result of validation can be wrong and the value will be not
 * the value submitted by user. This is because the form can make any assumptions on it
 * being submitted only after all fields have been added to it; only then it copies
 * field values from the request. After that it starts validating field values and, if it
 * fails, the field value is set to NULL. In short, the call to isSubmitted() signals the form
 * that all its fields have been added.
 *
 * Also note that you have to display all the fields you have added to the form on the page,
 * otherwise the form will never get submitted.
 *
 * The best place to construct the form is the Action's onInit() method.
 * The best place to check the validity of submission is the process() method.
 *
 * @author    Dennis Popel  
 * @date      11.09.2004
 * @version   1.0
 */
class Form {
  private $fields = array();
  private $parameters = array();
  private $valid = null;
  private $submitted = null;
  private $request = null;
  private $location = null;
  private $method;
  
  /**
   * Create new form with no fields.
   *
   * @param  Request $request  The request this form will use for populating its fields.
   * @param  Location $location  The location of action that will be called on submission
   * @param  int $method  The HTTP method this form will use - either Request::METHOD_POST (default) or Request::METHOD_GET
   
   */
  function __construct($request, $location, $method = Request::METHOD_POST) {
    $this->request = $request;
    $this->location = $location;
    $this->method = $method;
    
    // Add the hash if in private mode
    if($hash = Session::getPrivateMode()) {
      $this->setParameter('hash', $hash);
    }
  }
  
  /**
   * Add a field to this form
   *
   * @param  string $name  The name of the field to add
   * @param  InputField $field  The input field
   */ 
  function addField($name, InputField $field) {
    $field->setForm($this);
    $this->fields[$name] = $field;
  }
  
  /**
   * Remove filed for this form
   *
   * @param  string $name  name of the field to remove
   */
  function removeField($name) {
    unSet($this->fields[$name]);
  }
  
  /**
   * Gets an input field by name
   *
   * @param  string $name   name of the field to get
   * @return InputField  the requested InputField
   */
  function getField($name) {
    if(isSet($this->fields[$name])) {
      return $this->fields[$name];
    } else {
      return null;
    }
  }
  
  /**
   * Get all fields of the form as an array of name=>InputField pairs
   *
   * @return  array  array of form fields
   */
  function getFields() {
    return $this->fields;
  }
    
  /**
   * Returns an array of fieldName=>fieldValue pairs
   *
   * @return   array    array of field names=>values for this form
   */
  function getFieldValues() {
    $rv = array();
    foreach($this->getFields() as $k=>$v) {
      $rv[$k] = $v->getValue();
    }
    return $rv;
  }
  
  /**
   * Set field values. You can use this to set the default values of the fields 
	 * in one call
   *
   * @param   array $values   an array of name=>value pairs to set field values to
   */
  function setFieldValues($values) {
    foreach($values as $k=>$v) {
      if(!is_null($f = $this->getField($k))) {
        $f->setValue($v);
      }
    }
  }
  
  /**
   * Set a parameter for this form. Form's parameters will be available
   * on subsequest request of the action that gets executed upon the form
   * submission. In <class>HTMLDocument</class> these values will be appended as 
   * hidden input fields.
   *
   * @param  string $name  name of the parameter to set
   * @param  string $value  value of the parameter
   */
  function setParameter($name, $value) {
    $this->parameters[$name] = $value;
  }
  
  /**
   * Return all form parameters as an array of name=>value pairs.
   *
   * @return  array  array of parameter name=>value pairs
   */
  function getParameters() {
    return $this->parameters;
  }
  
  /**
   * Returns true if all fields are valid. Note that a form is considered
   * valid if it has not been submitted, regardless of actual field values.
   *
   * @return  bool  true if the form fields passed validation
   */
  function isValid() {
    if(!$this->isSubmitted()) {
      return true;
    }
    if(is_null($this->valid)) {
      $this->valid = true;
      foreach($this->getFields() as $f) {
        if(!($v = $f->isValid())) {
          $this->valid = false;
          $f->setValue(null);
        }
      }
    }
    return $this->valid;
  }

  /**
   * The form is submitted if all fields were passed from the client. It means
   * that for a form to be submitted each its field must be present in the 
   * request. 
   *
   * @return  true  if the form has been submitted.
   */
  function isSubmitted() {
    if(is_null($this->submitted)) {
      $values = array();
      $this->submitted = true;
      foreach($this->fields as $key=>$field) {
        if(is_null($value = $this->request->getParameter($key))) {
          $this->submitted = false;
        }
        $values[$key] = $value;
      }
      if($this->submitted) {
        $this->setFieldValues($values);
      }
    }
    return $this->submitted;
  }
  
  /**
   * This function is the shorthand for simple actions that are interested in the valid
   * submissions of the form, i.e., they do not differentiate invalid input and consider
   * it as a non-submitted state.
   *
   * @return   bool   true if the form was submitted and the input was valid
   */
  function isValidSubmission() {
    return $this->isSubmitted() && $this->isValid();
  }
  
  /**
   * Get the location of the action that will get executed when the form is 
	 * submitted
	 * @return  Location  Location of the Action that will process this form
	 */
  function getLocation() {
    return $this->location;
  }
  
  /**
   * Get the HTTP request method that will be used to submit the form's data.
   *
	 * @return  int  The HTTP request method, either Request::METHOD_GET or Request::METHOD_POST.
	 */
  function getMethod() {
    return $this->method;
  }
}

?>