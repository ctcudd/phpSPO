<?php
namespace SharePoint\PHP\Client;

/**
 * Represents properties that can be set when creating a field.
 * https://msdn.microsoft.com/en-us/library/office/dn600183.aspx#bk_FieldCreationInformation
 */
class FieldCreationInformation extends ClientValueObject {
	/**
	 * @var string
	 */
	public $Title;
	/**
	 * @see FieldTypeKind
	 * @var int
	 */
	public $FieldTypeKind;
	
	public function __construct($title, $type) {
		$this->Title = $title;
		$this->FieldTypeKind = $type;
		$this->setMetadataType("SP.Field");
	}
}