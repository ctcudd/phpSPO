<?php
namespace SharePoint\PHP\Client;
require_once(__DIR__.'/FieldCreationInformation.php');
class XmlFieldCreationInformation extends FieldCreationInformation{
	/**
	 *
	 * @var string
	 */
	public $SchemaXml;
	
	public function __construct($title, $type, $schemaXml) {
		parent::__construct($title, $type);
		$this->SchemaXml = $schemaXml;
	}
}