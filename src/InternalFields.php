<?php
namespace SharePoint\PHP\Client;
/**
 * Array containing internal sharepoint fields.  
 * @link https://blogs.msdn.microsoft.com/michael_yeager/2008/11/03/reference-list-for-sharepoint-internal-field-names/
 * @author usruoc
 *
 */
abstract class InternalFields {
	
	public static function isInternalField($fieldName){
		return array_key_exists($fieldName, self::INTERNAL_FIELDS);
	}
	
	const INTERNAL_FIELDS = array (
			'ID' => array (
					'DisplayName' => 'ID',
					'GUID' => '{1d22ea11-1e32-424e-89ab-9fedbadb6ce1}',
					'Type' => 'Counter' 
			),
			'ContentTypeId' => array (
					'DisplayName' => 'Content Type ID',
					'GUID' => '{03e45e84-1992-4d42-9116-26f756012634}',
					'Type' => 'ContentTypeId' 
			),
			'ContentType' => array (
					'DisplayName' => 'Content Type',
					'GUID' => '{c042a256-787d-4a6f-8a8a-cf6ab767f12d}',
					'Type' => 'Text' 
			),
			'Title' => array (
					'DisplayName' => 'Title',
					'GUID' => '{fa564e0f-0c70-4ab9-b863-0177e6ddd247}',
					'Type' => 'Text' 
			),
			'Modified' => array (
					'DisplayName' => 'Modified',
					'GUID' => '{28cf69c5-fa48-462a-b5cd-27b6f9d2bd5f}',
					'Type' => 'DateTime' 
			),
			'Created' => array (
					'DisplayName' => 'Created',
					'GUID' => '{8c06beca-0777-48f7-91c7-6da68bc07b69}',
					'Type' => 'DateTime' 
			),
			'Author' => array (
					'DisplayName' => 'Created By',
					'GUID' => '{1df5e554-ec7e-46a6-901d-d85a3881cb18}',
					'Type' => 'User' 
			),
			'Editor' => array (
					'DisplayName' => 'Modified By',
					'GUID' => '{d31655d1-1d5b-4511-95a1-7a09e9b75bf2}',
					'Type' => 'User' 
			),
			'_HasCopyDestinations' => array (
					'DisplayName' => 'Has Copy Destinations',
					'GUID' => '{26d0756c-986a-48a7-af35-bf18ab85ff4a}',
					'Type' => 'Boolean' 
			),
			'_CopySource' => array (
					'DisplayName' => 'Copy Source',
					'GUID' => '{6b4e226d-3d88-4a36-808d-a129bf52bccf}',
					'Type' => 'Text' 
			),
			'owshiddenversion' => array (
					'DisplayName' => 'owshiddenversion',
					'GUID' => '{d4e44a66-ee3a-4d02-88c9-4ec5ff3f4cd5}',
					'Type' => 'Integer' 
			),
			'WorkflowVersion' => array (
					'DisplayName' => 'Workflow Version',
					'GUID' => '{f1e020bc-ba26-443f-bf2f-b68715017bbc}',
					'Type' => 'Integer' 
			),
			'_UIVersion' => array (
					'DisplayName' => 'UI Version',
					'GUID' => '{7841bf41-43d0-4434-9f50-a673baef7631}',
					'Type' => 'Integer' 
			),
			'_UIVersionString' => array (
					'DisplayName' => 'Version',
					'GUID' => '{dce8262a-3ae9-45aa-aab4-83bd75fb738a}',
					'Type' => 'Text' 
			),
			'Attachments' => array (
					'DisplayName' => 'Attachments',
					'GUID' => '{67df98f4-9dec-48ff-a553-29bece9c5bf4}',
					'Type' => 'Attachments' 
			),
			'_ModerationStatus' => array (
					'DisplayName' => 'Approval Status',
					'GUID' => '{fdc3b2ed-5bf2-4835-a4bc-b885f3396a61}',
					'Type' => 'ModStat' 
			),
			'_ModerationComments' => array (
					'DisplayName' => 'Approver Comments',
					'GUID' => '{34ad21eb-75bd-4544-8c73-0e08330291fe}',
					'Type' => 'Note' 
			),
			'Edit' => array (
					'DisplayName' => 'Edit',
					'GUID' => '{503f1caa-358e-4918-9094-4a2cdc4bc034}',
					'Type' => 'Computed' 
			),
			'LinkTitleNoMenu' => array (
					'DisplayName' => 'Title',
					'GUID' => '{bc91a437-52e7-49e1-8c4e-4698904b2b6d}',
					'Type' => 'Computed' 
			),
			'LinkFilenameNoMenu' => array (
					'DisplayName' => 'Title',
					'GUID' => '{bc91a437-52e7-49e1-8c4e-4698904b2b6d}',
					'Type' => 'Computed' 
			),
			'LinkTitle' => array (
					'DisplayName' => 'Title',
					'GUID' => '{82642ec8-ef9b-478f-acf9-31f7d45fbc31}',
					'Type' => 'Computed' 
			),
			'SelectTitle' => array (
					'DisplayName' => 'Select',
					'GUID' => '{b1f7969b-ea65-42e1-8b54-b588292635f2}',
					'Type' => 'Computed' 
			),
			'InstanceID' => array (
					'DisplayName' => 'Instance ID',
					'GUID' => '{50a54da4-1528-4e67-954a-e2d24f1e9efb}',
					'Type' => 'Integer' 
			),
			'Order' => array (
					'DisplayName' => 'Order',
					'GUID' => '{ca4addac-796f-4b23-b093-d2a3f65c0774}',
					'Type' => 'Number' 
			),
			'GUID' => array (
					'DisplayName' => 'GUID',
					'GUID' => '{ae069f25-3ac2-4256-b9c3-15dbc15da0e0}',
					'Type' => 'Guid' 
			),
			'WorkflowInstanceID' => array (
					'DisplayName' => 'Workflow Instance ID',
					'GUID' => '{de8beacf-5505-47cd-80a6-aa44e7ffe2f4}',
					'Type' => 'Guid' 
			),
			'FileRef' => array (
					'DisplayName' => 'URL Path',
					'GUID' => '{94f89715-e097-4e8b-ba79-ea02aa8b7adb}',
					'Type' => 'Lookup' 
			),
			'FileDirRef' => array (
					'DisplayName' => 'Path',
					'GUID' => '{56605df6-8fa1-47e4-a04c-5b384d59609f}',
					'Type' => 'Lookup' 
			),
			'Last_x0020_Modified' => array (
					'DisplayName' => 'Modified',
					'GUID' => '{173f76c8-aebd-446a-9bc9-769a2bd2c18f}',
					'Type' => 'Lookup' 
			),
			'Created_x0020_Date' => array (
					'DisplayName' => 'Created',
					'GUID' => '{998b5cff-4a35-47a7-92f3-3914aa6aa4a2}',
					'Type' => 'Lookup' 
			),
			'FSObjType' => array (
					'DisplayName' => 'Item Type',
					'GUID' => '{30bb605f-5bae-48fe-b4e3-1f81d9772af9}',
					'Type' => 'Lookup' 
			),
			'PermMask' => array (
					'DisplayName' => 'Effective Permissions Mask',
					'GUID' => '{ba3c27ee-4791-4867-8821-ff99000bac98}',
					'Type' => 'Computed' 
			),
			'FileLeafRef' => array (
					'DisplayName' => 'Name',
					'GUID' => '{8553196d-ec8d-4564-9861-3dbe931050c8}',
					'Type' => 'File' 
			),
			'UniqueId' => array (
					'DisplayName' => 'Unique Id',
					'GUID' => '{4b7403de-8d94-43e8-9f0f-137a3e298126}',
					'Type' => 'Lookup' 
			),
			'ProgId' => array (
					'DisplayName' => 'ProgId',
					'GUID' => '{c5c4b81c-f1d9-4b43-a6a2-090df32ebb68}',
					'Type' => 'Lookup' 
			),
			'ScopeId' => array (
					'DisplayName' => 'ScopeId',
					'GUID' => '{dddd2420-b270-4735-93b5-92b713d0944d}',
					'Type' => 'Lookup' 
			),
			'File_x0020_Type' => array (
					'DisplayName' => 'File Type',
					'GUID' => '{39360f11-34cf-4356-9945-25c44e68dade}',
					'Type' => 'Text' 
			),
			'HTML_x0020_File_x0020_Type' => array (
					'DisplayName' => 'HTML File Type',
					'GUID' => '{4ef1b78f-fdba-48dc-b8ab-3fa06a0c9804}',
					'Type' => 'Computed' 
			),
			'_EditMenuTableStart' => array (
					'DisplayName' => 'Edit Menu Table Start',
					'GUID' => '{3c6303be-e21f-4366-80d7-d6d0a3b22c7a}',
					'Type' => 'Computed' 
			),
			'_EditMenuTableEnd' => array (
					'DisplayName' => 'Edit Menu Table End',
					'GUID' => '{2ea78cef-1bf9-4019-960a-02c41636cb47}',
					'Type' => 'Computed' 
			),
			'LinkFilenameNoMenu' => array (
					'DisplayName' => 'Name',
					'GUID' => '{9d30f126-ba48-446b-b8f9-83745f322ebe}',
					'Type' => 'Computed' 
			),
			'LinkFilename' => array (
					'DisplayName' => 'Name',
					'GUID' => '{5cc6dc79-3710-4374-b433-61cb4a686c12}',
					'Type' => 'Computed' 
			),
			'DocIcon' => array (
					'DisplayName' => 'Type',
					'GUID' => '{081c6e4c-5c14-4f20-b23e-1a71ceb6a67c}',
					'Type' => 'Computed' 
			),
			'ServerUrl' => array (
					'DisplayName' => 'Server Relative URL',
					'GUID' => '{105f76ce-724a-4bba-aece-f81f2fce58f5}',
					'Type' => 'Computed' 
			),
			'EncodedAbsUrl' => array (
					'DisplayName' => 'Encoded Absolute URL',
					'GUID' => '{7177cfc7-f399-4d4d-905d-37dd51bc90bf}',
					'Type' => 'Computed' 
			),
			'BaseName' => array (
					'DisplayName' => 'File Name',
					'GUID' => '{7615464b-559e-4302-b8e2-8f440b913101}',
					'Type' => 'Computed' 
			),
			'MetaInfo' => array (
					'DisplayName' => 'Property Bag',
					'GUID' => '{687c7f94-686a-42d3-9b67-2782eac4b4f8}',
					'Type' => 'Lookup' 
			),
			'_Level' => array (
					'DisplayName' => 'Level',
					'GUID' => '{43bdd51b-3c5b-4e78-90a8-fb2087f71e70}',
					'Type' => 'Integer' 
			),
			'_IsCurrentVersion' => array (
					'DisplayName' => 'Is Current Version',
					'GUID' => '{c101c3e7-122d-4d4d-bc34-58e94a38c816}',
					'Type' => 'Boolean' 
			) 
	);
}