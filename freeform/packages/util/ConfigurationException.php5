<?

/**
 * This is a general-purpose exception that should be thrown when a particular package discovers
 * that it has not been setup properly.
 * @since 1.1.0
 * @author Dennis Popel
 */
class ConfigurationException extends Exception {
  private $packageName;
  private $propertyName;
  private $problem;
  
  /**
   * Construct new ConfigurationException object.
   * @param  string $packageName  name of the package
   * @param  string $propertyName  name of the misconfigured package config option
   * @param  string $problem  short description of the problem
   */
  function __construct($packageName, $propertyName, $problem) {
    parent::__construct("Package $packageName is not configured properly: 
      property $propertyName: $problem");
    $this->packageName = $packageName;
    $this->propertyName = $propertyName;
    $this->problem = $problem;
  }
  
  /**
   * Get the package name
   * @return  string  the name of the misconfigured package
   */
  function getPackageName() {
    return $this->packageName;
	}
	
	/**
	 * Get the misconfigured option name
	 * @return  string  the name of the misconfigured property
	 */
	function getPropertyName() {
	  return $this->propertyName;
	}
}

?>