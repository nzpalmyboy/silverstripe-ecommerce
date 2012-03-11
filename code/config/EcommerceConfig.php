<?php



/**
 * This Class creates an array of configurations for e-commerce.
 * This class replaces static variables in individual classes, such as Blog::$allow_wysiwyg_editing
 *
 * @see http://en.wikipedia.org/wiki/YAML#Examples
 * @see thirdparty/spyc/spyc.php
 *
 * # HOW TO USE IT
 *
 * 1. Copy ecommerce/_config/ecommerce.yaml and move it your project folder, e.g. mysite/_config/ecommerce.yaml
 * In the copied file, set your configs as you see fit, using the YAML format.  E.g.
 *
 * Order:
 * 	Test: 1
 *
 * Next, include in _config.php:
 * <code>
 * EcommerceConfig::set_folder_and_file_locations(array("mysite/_config/ecommerce.yaml", "myotherconfig.yaml"));
 * </code>
 *
 * Then, in individual classes, you can access configs like this:
 *
 * <code>
 * EcommerceConfig::get("OrderAddress", "include_bla_bla_widget");
 * </code>
 *
 * OR
 *
 * <code>
 * EcommerceConfig::get($this->ClassName, "include_bla_bla_widget");
 * </code>
 *
 * if you are using PHP 5.3.0+ then you can write this in a static method
 *
 * <code>
 * EcommerceConfig::get(get_called_class(), "include_bla_bla_widget");
 * </code>

 * Even though there is no direct connection, we keep linking statics to invidual classes.
 * We do this to (a) group configs (b) make it more interchangeable with other config systems.
 * One of the problems now is to know what "configs" are used by individual classes.
 * Therefore, it is important to clearly document that at the top of each class.
 *
 **/

class EcommerceConfig extends Object {

	/**
	 * Singleton
	 *
	 * @var EcommerceConfig
	 */
	protected static $singleton = null;

	/**
	 * Returns a configuration.  This is the main static method for this Object.
	 *
	 * @return Mixed
	 * @param $className The data class, as specified in your fixture file.  Parent classes won't work
	 * @param $identifier The identifier string, as provided in your fixture file
	 * @param $subIdentifier A secondary identifier string, as provided in your fixture file
	 * @TODO: implement subIdentfier
	 */
	static function get($className, $identifier, $subIdentifier = null) {
		if(!self::$singleton) {
			self::$singleton = new EcommerceConfig();
		}
		return self::$singleton->getStaticValue($className, $identifier);
	}

	/**
	 * The location of the .yml fixture file, relative to the site base dir
	 *
	 * @var string
	 */
	protected static $folder_and_file_locations = array("ecommerce/_config/ecommerce.yaml");
		static function set_folder_and_file_locations($a) {self::$folder_and_file_locations = $a;}

	/**
	 * Array of fixture items
	 *
	 * @var array
	 */
	protected $fixtureDictionary = array();


	/**
	 * Get the value for a static value.
	 * @return Null | Mixed
	 * @param $className The data class, as specified in your fixture file.  Parent classes won't work
	 * @param $identifier The identifier string, as provided in your fixture file
	 * @param $subIdentifier A secondary identifier string, as provided in your fixture file
	 * @TODO: implement subIdentfier
	 */
	public function getStaticValue($className, $identifier, $subIdentifier = null) {
		if(!count($this->fixtureDictionary)) {
			$this->loadData();
		}
		if(isset($this->fixtureDictionary[$className][$identifier])) {
			return $this->fixtureDictionary[$className][$identifier];
		}
		else {
			user_error("Could not find definition for: {$className}.{$identifier}.{$subIdentifier}", E_USER_NOTICE);
			return null;
		}
	}

	/**
	 * loads data from file.
	 * This is only actioned once the first request is made.
	 */
	private function loadData(){
		require_once 'thirdparty/spyc/spyc.php';
		foreach(self::$folder_and_file_locations as $folderAndFileLocation){
			$fixtureFolderAndFile = Director::baseFolder().'/'. $folderAndFileLocation;
			if(!file_exists($fixtureFolderAndFile)) {
				user_error('No custom configuration has been setup for Ecommerce - I was looking for: "' . $fixtureFolderAndFile . '"', E_USER_NOTICE);
			}
			$parser = new Spyc();
			$newArray = $parser->loadFile($fixtureFolderAndFile);
		}
		$this->fixtureDictionary = array_merge($newArray, $this->fixtureDictionary)
	}



}