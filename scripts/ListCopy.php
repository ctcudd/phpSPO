<?php

namespace SharePoint\PHP\Client;
require_once (__DIR__ . '/config.php');
use SharePoint\PHP\Client\NtlmAuthenticationContext;
use SharePoint\PHP\Client\ClientContext;
use SharePoint\PHP\Client\ListCreationInformation;
use stdClass;
try {
	$results = [ ];
	$fields = null;
	// authenticate and aquire cookie
	$authCtx = new NtlmAuthenticationContext ( $Settings ['ProdUrl'], $Settings ['UserName'], $Settings ['Password'] );
	$authCtx->acquireTokenForUser ( $Settings ['UserName'], $Settings ['Password'] );
	$srcContext = new ClientContext ( $Settings ['ProdUrl'], $authCtx );
	
// 	$authCtx = new NtlmAuthenticationContext ( $Settings ['Url'], $Settings ['UserName'], $Settings ['Password'] );
// 	$authCtx->acquireTokenForUser ( $Settings ['UserName'], $Settings ['Password'] );
// 	$destCtx = new ClientContext ( $Settings ['Url'], $authCtx );
	$destCtx = $srcContext;
	
	$srcListName = "Application List";
	$destListName = "Applications";
	
	/* @var $srcList SPList */
	$srcList = getList ( $srcContext, $srcListName );
	if (! is_null ( $srcList )) {
		
		/* @var $newList SPList */
		$newList = getList ( $destCtx, $destListName );
		if (! is_null ( $newList )) {
			echo "$destListName already exists\n";
			exit ();
		}
		$info = new ListCreationInformation ( $destListName );
		$info->Description = $srcList->Description;
		$destCtx->getWeb ()->getLists ()->add ( $info );
		$destCtx->executeQuery ();
	} else {
		echo "$srcListName does not exist\n";
		exit ();
	}
	
	/* @var $newList SPList */
	$newList = getList ( $destCtx, $destListName );
	if (! is_null ( $newList )) {
		
		$destCtx = $newList->getContext ();
		$newFields = $newList->getFields ();
		$destCtx->load ( $newFields );
		$destCtx->executeQuery ();
		
		$srcCtx = $srcList->getContext ();
		$originalFields = $srcList->getFields ();
		$srcCtx->load ( $originalFields );
		$srcCtx->executeQuery ();
		foreach ( $originalFields->getData () as $originalField ) {
			if(endsWith($originalField->Title,"OLD")){
				//echo "skipping 'OLD' field: {$originalField->Title}\n";
				continue;
			}
			
			$newFieldInfo = NULL;
			$SchemaXml = $originalField->SchemaXml;
			if ($SchemaXml) {
				// remove the id and sourceId fields, if you don't... the Id that is returned from a createItem call will NOT be correct.
				// it seems Sharepoint will generate an id, return it, but then re-write the id with the value you passed via the createItem call... much confusion avoid if possible...
				$patterns = array (
						'/ SourceID\=\".*?\"/',
						'/ ID\=\".*?\"/', 
						'/_x0020_/',
				);
				$replacements = array (
						'',
						'',
						'',
				);
				$SchemaXml = preg_replace ( $patterns, $replacements, $SchemaXml );
			}
			
			if ($originalField->ReadOnlyField === false) {
				if ($originalField->FromBaseType === true) {
					if ($originalField->InternalName === "Title") {
						$fieldUpdateInformation = array (
								'__metadata' => array (
										'type' => $originalField->__metadata->type 
								),
								'Description' => $originalField->Description,
								'EnforceUniqueValues' => $originalField->EnforceUniqueValues,
								'FieldTypeKind' => $originalField->FieldTypeKind,
								'Indexed' => $originalField->Indexed,
								'MaxLength' => $originalField->MaxLength,
								'Required' => $originalField->Required 
						);
						/* @var $newField Field */
						$newField = array_reduce ( $newFields->getData (), function ($carry, $item) use ($originalField){
							if ($originalField->InternalName == $item->InternalName) {
								return $item;
							} else {
								return $carry;
							}
						} );
						echo "UPDATE {$originalField->InternalName} field: " . print_r ( $fieldUpdateInformation, 1 ) . "\n";
						$destCtx = $newField->getContext ();
						$newField->update ( $fieldUpdateInformation );
						$destCtx->executeQuery ();
						$newField->setShowInAll ( true );
						$destCtx->executeQuery ();
					}
				} else {
					// list specific replacements
					if (strpos ( $SchemaXml, "pyrthon_direct_access" ) !== false) {
						$SchemaXml = str_replace ( "pyrthon_direct_access", "python_direct_access", $SchemaXml );
					}
					$destCtx = $newList->getContext ();
					
					if ($originalField->FieldTypeKind == FieldTypeKind::Lookup) {
						$newFieldInfo = new ComplexFieldCreationInformation ( $originalField->Title, $originalField->FieldTypeKind );
						
						// Error: The property 'Description' does not exist on type 'SP.FieldCreationInformation'
						// $newFieldInfo->Description = $originalField->Description;
						
						if ($srcContext != $destCtx) {
							//if moving a list between hosts, and its a lookup column, we have to use a LookupListId from the dest host
							$srcLookupList = getList ( $srcContext, strtolower(substr($originalField->LookupList, 1, -1)));
							if (! $srcLookupList) {
								echo "failed to find src lookup list.\n";
							} else {
								$dstLookupList = getList ($destCtx, $srcLookupList->Title);
								if($dstLookupList){
									$newFieldInfo->LookupListId = $dstLookupList->Id;
								}else{
									echo "failed to find dst lookup list for {$srcLookupList->Title}.\n";
								}
							}
						}else {
							$newFieldInfo->LookupListId = $originalField->LookupList;
						}
						
						
						// $newFieldInfo->LookupWebId = $originalField->LookupWebId;
						//$newFieldInfo->AllowMultipleValues = $originalField->AllowMultipleValues;
						
						$newFieldInfo->LookupFieldName = $originalField->LookupField;
						$newFieldInfo->Required = $originalField->Required;
						$newField = $newList->addComplexField ( $newFieldInfo );
						if($originalField->AllowMultipleValues){
							$destCtx->executeQuery ();
							$destCtx = $newField->getContext ();
							$destCtx->load ( $newField );
							
							$fieldUpdateInformation = array (
									'__metadata' => array (
											'type' => $newField->__metadata->type
									),
									'AllowMultipleValues' => $originalField->AllowMultipleValues,
							);
							$newField->update($fieldUpdateInformation);
						}
					} else {
						$newFieldInfo = new XmlFieldCreationInformation ( $originalField->Title, $originalField->FieldTypeKind, $SchemaXml );
						$newField = $newList->addField ( $newFieldInfo );
					}
					echo "CREATE {$originalField->InternalName} field: " . print_r ( $newFieldInfo, 1 ) . "\n";
					$destCtx->executeQuery ();
					
					$destCtx = $newField->getContext ();
					$destCtx->load ( $newField );
					$newField->setShowInAll ( true );
					$destCtx->executeQuery ();
				}
			} elseif (InternalFields::isInternalField ( $originalField->InternalName )) {
				// echo "cannot produce internalField: {$originalField->Title}\n";
			} else {
				if ($originalField->FieldTypeKind == \SharePoint\PHP\Client\FieldTypeKind::Calculated) {
					// $originalField->debug();
					$newFieldInfo = new FieldCreationInformation ( $originalField->Title, $originalField->FieldTypeKind );
					$newFieldInfo->setMetadataType ( "SP.FieldCalculated" );
					$newFieldInfo->Formula = $originalField->Formula;
					$newFieldInfo->OutputType = $originalField->OutputType;
					
					echo "CREATE {$originalField->InternalName} field: " . print_r ( $newFieldInfo, 1 ) . "\n";
					$newField = $newList->addField ( $newFieldInfo );
					// ClientRequest::$debug=true;
					$destCtx->load ( $newField );
					$destCtx->executeQuery ();
					$newField->setShowInDisplayForm ( true );
					$destCtx->executeQuery ();
				} else {
					echo "cannot produce field: {$originalField->Title}\n";
				}
			}
		}
	}
	$srcList = getList ( $srcCtx, $srcListName );
	$srcCtx = $srcList->getContext ();
	
	// selects only return 100 results by default, top used as workaround
	$originalItems = $srcList->getItems ()->top ( 1000 );
	$srcCtx->load ( $originalItems );
	$srcCtx->executeQuery ();
	foreach ( $originalItems->getData () as $item ) {
		$itemProperties = array ();
		foreach ( $originalFields->getData () as $field ) {
			if(endsWith($field->Title,"OLD")){
				//echo "skipping OLD field: {$originalField->Title}\n";
				continue;
			}
			if ($field->ReadOnlyField === false) {
				if ($field->FromBaseType === true && $field->InternalName !== "Title") {
					// echo "skipping baseType field: {$field->InternalName}\n";
					continue;
				}
				$nameField = $field->Title;
				$valueField = $field->InternalName;
				if ($field->FieldTypeKind == FieldTypeKind::Lookup || $field->FieldTypeKind == FieldTypeKind::User) {
					$valueField .= "Id";
					$nameField .= "Id";
					
				}
				$itemValue = (isset ( $item->{$valueField} ) ? $item->{$valueField} : $item->{$nameField});
				if(isset($itemValue->__metadata)
// 						&& $itemValue->__metadata->type == "Collection(Edm.Int32)"
						&& isset($itemValue->__metadata->type)){
					//echo "weirdness here\n";
						unset($itemValue->__metadata);
				}
				if (is_null($itemValue)) {
					echo "skipping null value for field: $nameField\n";
				}elseif(isset($itemValue->results) && empty($itemValue->results)){
					echo "skipping empty value for field: $nameField\n";
				}else{
					
					if ($srcContext != $destCtx ) {
						if($field->FieldTypeKind == FieldTypeKind::Lookup){
							echo "found a lookup field: $nameField, value: ".print_r($itemValue,1)." \n";
							
							//if moving a lookup between hosts we need to first find the src list and value...
							$srcLookupList = getList ( $srcContext, strtolower(substr($field->LookupList, 1, -1)));
							if(!$srcLookupList){
								echo "failed to find src lookup list for field: {$nameField}, lookupField: {$field->LookupField}, lookupList:{$field->LookupList}\n";
								continue;
							}
							$srcFilter = "";
							if(is_scalar($itemValue)){
								$srcFilter = "ID eq $itemValue";
							}elseif(isset($itemValue->results)&&is_array($itemValue->results)){
								//(ID eq 1) or (ID eq 2) or (ID eq 3)
								$srcFilter = "(ID eq ".implode(") or (ID eq ", $itemValue->results).")";
							}
							$srcItems = $srcLookupList->getItems()->filter($srcFilter);
							$srcContext->load($srcItems);
							$srcContext->executeQuery();
							$srcValues = array();
							foreach($srcItems->getData() as $srcItem){
								$srcValues []= $srcItem->{$field->LookupField};
							}
							echo "found ".count($srcItems->getData())." src lookup items: ". print_r($srcValues,1)."\n";
								
							$dstLookupList = getList ($destCtx, $srcLookupList->Title);
							if($dstLookupList){
							
								$dstItems = $dstLookupList->getItems();
								$destCtx->load($dstItems);
								$destCtx->executeQuery();
								$destIds = array();
								foreach($dstItems->getData() as $dstItem){
									$dstValue = $dstItem->{$field->LookupField};
									if(in_array($dstValue, $srcValues)){
										echo "found matching lookup item. field: {$field->LookupField}, value: {$dstValue} \n";
										$destIds []= $dstItem->Id;
									}
								}
								if(!empty($destIds)){
									if($field->AllowMultipleValues){
										$itemValue = new stdClass();
										$itemValue->results = $destIds;
									}else{
										if(count($destIds) > 1){
											echo "warning: about to loose data, this shouldn't be happening";
										}
										$itemValue = $destIds[0];
									}
								}else{
									echo "failed to find any matching lookup items for field: {$field->LookupField}\n";
									continue;
								}
							}else{
								echo "failed to find dst lookup list for {$srcLookupList->Title}.\n";
								continue;
							}
						}elseif($field->FieldTypeKind == FieldTypeKind::User){
							echo "found a User field: $nameField, value: ".print_r($itemValue,1)." \n";
							$srcUserIds = array();
							if(is_scalar($itemValue)){
								$srcUserIds []= $itemValue;
							}else{
								$srcUserIds = $itemValue->results;
							}
							$srcUserEmails = array();
							foreach($srcUserIds as $srcUserId){
								$srcUser = $srcContext->getWeb()->getSiteUsers()->getById($srcUserId);
								$srcContext->load($srcUser);
								$srcContext->executeQuery();
								if($srcUser->Email){
									$srcUserEmails []= $srcUser->Email;
								}else{
									echo "failed to find srcUser!!!";
								}
							}
							if(empty($srcUserEmails)){
								echo "failed to find srcUsers\n";
							}else{
								
								echo "found ".count($srcUserEmails)."\n";
								$destIds = array();
								foreach($srcUserEmails as $srcEmail){
									$dstUser = $destCtx->getWeb()->getSiteUsers()->getByEmail($srcEmail);
									$destCtx->load($dstUser);
									$destCtx->executeQuery();
									
									if($dstUser){
										$destIds []= $dstUser->Id;
									}
								}
								if(!empty($destIds)){
									if($field->AllowMultipleValues){
										$itemValue = new stdClass();
										$itemValue->results = $destIds;
									}else{
										if(count($destIds) > 1){
											echo "warning: about to loose data, this shouldn't be happening";
										}
										$itemValue = $destIds[0];
									}
								}else{
									echo "failed to find any matching users for field: {$field->LookupField}\n";
									continue;
								}
							}
						}		
					}
					$itemProperties [$nameField] = $itemValue;
				}
			}
		}
		echo "CREATE item: " . print_r ( $itemProperties, 1 ) . "\n";
		$result = createListItem ( $newList, $itemProperties );
		if (! $result) {
			echo "FAIL!";
			exit ();
		} else {
			$results [] = $result;
		}		
	}
	echo count ( $results ) . " items created\n";
} catch ( Exception $e ) {
	echo 'Error: ', $e->getMessage (), "\n";
}



