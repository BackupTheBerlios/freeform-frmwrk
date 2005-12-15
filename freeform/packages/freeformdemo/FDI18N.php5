<?

/**
 * This is the I18N API demo that will allow you to select country, language, collation and timezone
 * and watch how the localized items will change. To successfully run this demo you will have
 * to correctly confogure the <package>i18n</package> package.
 * @author Dennis Popel
 */
class FDI18N extends FDDemo {
  private $form;
  private $dialect;
  private $available = true;
  
  // This sorts time zones as returned by I18NTimeZone::getTimeZones by the UTC offset
  function tzSort($tz1, $tz2) {
    $v1 = $tz1->getUTCOffset();
    $v2 = $tz2->getUTCOffset();  
    return $v1 < $v2 ? -1 : $v1 == $v2 ? 0 : 1;
  }
  
  // This sorts the localized words using the default collation
  function langSort($s1, $s2) {
    return $this->collation->compareStrings($s1, $s2, !$this->caseSensitive);
  }
    
  function onInit() {
    parent::onInit();
    
    try {
      I18N::getCountries();
    } catch(Exception $e) {
      $this->available = false;
      return;
    }
    
    $this->form = new Form($this->getRequest(), new Location($this), Request::METHOD_GET);
    
    // Add the country select field
    $countries = I18N::getCountries();  // This returns all countries in form of cc => I18NCountry object pairs
    ksort($countries);                  // Sort the list by country code 
    foreach($countries as $k=>$v) {
      $countries[$k] = $v->getCode() . ' - ' . $v->getName();
    }
    // Now $countries is an array of cc => 'code - country' pairs
    $this->form->addField('country', new SelectInputField($this->getRequest()->getParameter('country'), $countries, '...'));
    
    // Add the lang select field
    $dialects = I18N::getDialects(); // This returns all dialects in form of cc_dc => I18NDialect object
    ksort($dialects);                // sorting by key will assure that dialects are sorted by their lang codes
    foreach($dialects as $k=>$v) {   // Create the dropdown select box for dialects
      $colls = $v->getCollations();  // We will add collations too
      foreach($colls as $collc=>$coll) {
        $dials[$k . '_' . $collc] = $v->getLanguage()->getCode() . ' - ' . $v->getName() . ' / ' . $coll->getName();
      }
      // Now $dials holds an array in form lc_dc_collc => 'lc - dialect / collation' pairs
    }
    $this->form->addField('dialect', new SelectInputField($this->getRequest()->getParameter('dialect'), $dials, '...'));    
    
    // Add the timezone select field
    $tzs = I18N::getTimeZones();     // This returns all timezones in form code => I18NTimeZone object
    uasort($tzs, array($this, 'tzSort'));  // We sort this array with the user function to have timezones sorted by GMT offset
    foreach($tzs as $k=>$v) {
      $tzs1[$k] = I18NTimeZone::formatOffset($v->getUTCOffset()) . ': ' . join(', ', $v->getLocations());
    }
    // Now $tzs1 is an array of code => '+HH:MM: locations' pairs  
    $this->form->addField('tz', new SelectInputField($this->getRequest()->getParameter('tz'), $tzs1, '...'));
    
    // Add the 'case sensitive collate' checkbox
    $this->form->addField('caseSensitive', new CheckBoxInputField($this->getRequest()->getParameter('caseSensitive')));
  }
    
  function process() {
    parent::process();
    
    if(!$this->available) {
      $this->getDocument()->setVariable('notAvailable', true);
      return;
    }
    
    // Prepare the document
    $r = $this->getResponce();
    $rq = $this->getRequest();
    $d = $this->getDocument();
    $d->setVariable('form', $this->form);
    
    try {
      // If the form was submitted...
      if($this->form->isValidSubmission()) {
        // See if the dialect and collation have been selected
        @list($lc, $dc, $collc) = split('_', $rq->getParameter('dialect'));
      
        // Get the selected country code
        $cc = $this->getRequest()->getParameter('country', 'US') or $cc = null;
      
        // Instantiate the locale
        $l = I18N::getLocale($cc, $lc, $dc);
      
        // See if the time zone has been selected
        if($tz = $this->getRequest()->getParameter('tz')) {
          $l->setTimeZone(I18N::getTimeZone($tz));
        }
      } else {
        // the form has not been submitted, get the default locale
        $l = I18N::getLocale();
        $collc = 'Default';
      }
      
      $this->dialect = $l->getDialect();
      $this->collation = $this->dialect->getCollation($collc ? $collc :  'Default');
      $this->caseSensitive = $rq->getParameter('caseSensitive');
      
      // Load the message dictionary
      $mf = new I18NMessageFile($this->getPackage()->getResourcePath('messages'));
      
      // Collate some words in the selected dialect and coolation
      $coll = explode(',', $mf->translateMessage('words', $this->dialect->getLanguage()->getCode()));
      usort($coll, array($this, 'langSort'));
      $d->setVariable('coll', (join(', ', $coll)));
    } catch(I18NException $e) {
      // Smth went wrong
      $d->setVariable('error', $e->getMessage());
      $l = I18N::getLocale();
      $collc = 'Default';
    }
    
    $d->setLocale($l);
    
    // Set some info
    $c = $l->getCountry();
    $d->setVariable('cc', $c->getCode());
    $d->setVariable('cn', $c->getName());
    $d->setVariable('cdc', $c->getDialCode());
    $d->setVariable('ccc', $l->getCurrencyCode());
    $d->setVariable('ccs', $l->getCurrencySymbol());
    $dial = $l->getDialect();
    $d->setVariable('lc', $dial->getLanguage()->getCode());
    $d->setVariable('dc', $dial->getCode());
    $d->setVariable('dn', $dial->getName());    
    $col = $l->getDialect()->getCollation($collc ? $collc :  'Default');
    $d->setVariable('collc', $col->getCode());
    $d->setVariable('colln', $col->getName());
    $tz = $l->getTimeZone();
    $d->setVariable('tzl', join(', ', $tz->getLocations()));
    $d->setVariable('tzo', $tz->formatOffset($tz->getUTCOffset()));
    $d->setVariable('tza', $tz->getStandardAbbrev());
    $d->setVariable('tzco', $tz->formatOffset($tz->getCurrentOffset()));
    $d->setVariable('tzca', $tz->getCurrentAbbrev());
    $d->setVariable('tzt', $tz->isDSTEnabled() ? 'yes' : 'no');
    $d->setVariable('tzs', $tz->isSummerTime() ? 'yes' : 'no');

    $l->setMessageDictionary($mf);
  }
  
  // These methods are internal to the demos system
  function getTitle() {
    return 'I18N Demo';
  }
  
  function getNext() {
    return new Location('FDCache');
  }
  
  function getPrev() {
    return new Location('FDRCContent');
  }
  
  static function getDescription() {
    return 'Internationalization';
  }
}

?>