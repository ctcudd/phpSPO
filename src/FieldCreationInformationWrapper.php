<?php
namespace SharePoint\PHP\Client;
require_once(__DIR__.'/ClientValueObject.php');
use stdClass;
/**
 * Represents properties that can be set when creating a field.
 * https://msdn.microsoft.com/en-us/library/office/dn600183.aspx#bk_FieldCreationInformation
 */
class FieldCreationInformationWrapper extends ClientValueObject {
	
	public $parameters;
	
	public function __construct(ComplexFieldCreationInformation $info) {
		$this->parameters = $info;	
	}
	
	protected function ensureMetadataType()
	{
		$this->parameters->__metadata = new stdClass();
		if(!isset($this->parameters->metadataType)){
			$this->parameters->__metadata->type = "SP.FieldCreationInformation";
		}
		else {
			$this->parameters->__metadata->type = $this->metadataType;
		}
	}
}