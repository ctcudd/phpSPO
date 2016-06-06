<?php
namespace SharePoint\PHP\Client;
require_once(__DIR__.'/FieldCreationInformation.php');
/**
 * Represents properties that can be set when creating a field.
 * https://msdn.microsoft.com/en-us/library/office/dn600183.aspx#bk_FieldCreationInformation
 */
class ComplexFieldCreationInformation extends FieldCreationInformation {
	
// 	public $Choices = [];
// 	public $IsCompactName = false;
	public $LookupFieldName = "";
	public $LookupListId ="";
// 	public $LookupWebId = "";
	public $Required = false;
	
	public function __construct($title, $type) {
		parent::__construct($title, $type);
		$this->setMetadataType("SP.FieldCreationInformation");
	}
}