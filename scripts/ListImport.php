<?php

namespace SharePoint\PHP\Client;

require_once (__DIR__ . '/config.php');

listImport ( "ApplicationTypes", $Settings );
function listImport($srcListName, $Settings) {
	$dstListName = "$srcListName-TEST";
	
	$fieldsFileName = getFieldsFileName ( $srcListName );
	$valuesFileName = getValuesFileName ( $srcListName );
	$schemaFileName = getSchemaFileName();
	
	$jsonData = [ ];
	
	if (! file_exists ( $fieldsFileName )) {
		echo "$fieldsFileName does not exist\n";
		exit ();
	}
	if (! file_exists ( $valuesFileName )) {
		echo "$valuesFileName does not exist\n";
		exit ();
	}
	if (! file_exists ( $schemaFileName )) {
		echo "$schemaFileName does not exist\n";
		exit ();
	}
	
	try {
		
		$authCtx = new NtlmAuthenticationContext ( $Settings ['Url'], $Settings ['UserName'], $Settings ['Password'] );
		$authCtx->acquireTokenForUser ( $Settings ['UserName'], $Settings ['Password'] );
		$dstCtx = new ClientContext ( $Settings ['Url'], $authCtx );
		$dstList = getList ( $dstCtx, $dstListName );
		if (! is_null ( $dstList )) {
			echo "$destListName already exists\n";
			exit ();
		} else {
			//get the list schema information
			$schemaData = getJsonForFile($schemaFileName);
			
			// create the list
			$info = new ListCreationInformation ( $dstListName );
			if(isset($schemaData->Description)){
				$info->Description = $schemaData->Description;
			}		
			$dstCtx->getWeb ()->getLists ()->add ( $info );
			$dstCtx->executeQuery ();
		}
		// retrieve the list
		$dstList = getList ( $dstCtx, $dstListName );
		if (is_null ( $dstList )) {
			echo "failed to create $dstListName\n";
			exit ();
		} else {
			echo "created $dstListName\n";
		}
		$dstListFields = $dstList->getFields ();
		$dstCtx->load ( $dstListFields );
		$dstCtx->executeQuery ();
		
		
		$fieldsData = getJsonForFile( $fieldsFileName );
		echo "found " . count ( $fieldsData ) . " fields\n";
		foreach ( $fieldsData as $field ) {
			$newFieldInfo = NULL;
			$SchemaXml = $field->SchemaXml;
			if ($SchemaXml) {
				// remove the id and sourceId fields, if you don't... the Id that is returned from a createItem call will NOT be correct.
				// it seems Sharepoint will generate an id, return it, but then re-write the id with the value you passed via the createItem call... much confusion avoid if possible...
				$patterns = array (
						'/ SourceID\=\".*?\"/',
						'/ ID\=\".*?\"/' 
				);
				$replacements = array (
						'',
						'' 
				);
				$SchemaXml = preg_replace ( $patterns, $replacements, $SchemaXml );
			}
			
			if ($field->ReadOnlyField === false) {
				if ($field->FromBaseType === true) {
					if ($field->InternalName === "Title") {
						$fieldUpdateInformation = array (
								'__metadata' => array (
										'type' => $field->__metadata->type 
								),
								'Description' => $field->Description,
								'EnforceUniqueValues' => $field->EnforceUniqueValues,
								'FieldTypeKind' => $field->FieldTypeKind,
								'Indexed' => $field->Indexed,
								'MaxLength' => $field->MaxLength,
								'Required' => $field->Required 
						);
						$dstListField = array_reduce ( $dstListFields->getData (), function ($carry, $item) use($field) {
							if ($field->InternalName == $item->InternalName) {
								return $item;
							} else {
								return $carry;
							}
						} );
						echo "UPDATE {$field->InternalName} field: " . print_r ( $fieldUpdateInformation, 1 ) . "\n";
						$dstCtx = $dstListField->getContext ();
						$dstListField->update ( $fieldUpdateInformation );
						$dstCtx->executeQuery ();
						$dstListField->setShowInAll ( true );
						$dstCtx->executeQuery ();
					}
				} else {
					// list specific replacements
					if (strpos ( $SchemaXml, "pyrthon_direct_access" ) !== false) {
						$SchemaXml = str_replace ( "pyrthon_direct_access", "python_direct_access", $SchemaXml );
					}
					
					if ($field->FieldTypeKind == FieldTypeKind::Lookup) {
						$newFieldInfo = new ComplexFieldCreationInformation ( $field->Title, $field->FieldTypeKind );
						
						// Error: The property 'Description' does not exist on type 'SP.FieldCreationInformation'
						// $newFieldInfo->Description = $field->Description;
						
						$newFieldInfo->LookupListId = $field->LookupList;
						$newFieldInfo->LookupFieldName = $field->LookupField;
						$newFieldInfo->Required = $field->Required;
						$dstListField = $dstList->addComplexField ( $newFieldInfo );
					} else {
						$newFieldInfo = new XmlFieldCreationInformation ( $field->Title, $field->FieldTypeKind, $SchemaXml );
						$dstListField = $dstList->addField ( $newFieldInfo );
					}
					echo "CREATE {$field->InternalName} field: " . print_r ( $newFieldInfo, 1 ) . "\n";
					$dstCtx->executeQuery ();
					
					$dstCtx = $dstListField->getContext ();
					$dstCtx->load ( $dstListField );
					$dstListField->setShowInAll ( true );
					$dstCtx->executeQuery ();
				}
			} elseif (InternalFields::isInternalField ( $field->InternalName )) {
				// echo "cannot produce internalField: {$field->Title}\n";
			} else {
				if ($field->FieldTypeKind == \SharePoint\PHP\Client\FieldTypeKind::Calculated) {
					// $field->debug();
					$newFieldInfo = new FieldCreationInformation ( $field->Title, $field->FieldTypeKind );
					$newFieldInfo->setMetadataType ( "SP.FieldCalculated" );
					$newFieldInfo->Formula = $field->Formula;
					$newFieldInfo->OutputType = $field->OutputType;
					
					echo "CREATE {$field->InternalName} field: " . print_r ( $newFieldInfo, 1 ) . "\n";
					$dstListField = $dstList->addField ( $newFieldInfo );
					// ClientRequest::$debug=true;
					$dstCtx->load ( $dstListField );
					$dstCtx->executeQuery ();
					$dstListField->setShowInDisplayForm ( true );
					$dstCtx->executeQuery ();
				} else {
					echo "cannot produce field: {$field->Title}\n";
				}
			}
		}
		$valuesData = getJsonForFile($valuesFileName);
		echo "found " . count ( $valuesData ) . " values\n";
		foreach ( $valuesData as $item ) {
			foreach ( $fieldsData as $field ) {
				if ($field->ReadOnlyField === false) {
					if ($field->FromBaseType === true && $field->InternalName !== "Title") {
						// echo "skipping baseType field: {$field->InternalName}\n";
						continue;
					}
					$nameField = $field->Title;
					$valueField = $field->InternalName;
					if ($field->FieldTypeKind == FieldTypeKind::Lookup) {
						$valueField .= "Id";
						$nameField .= "Id";
					}
					$itemValue = (isset ( $item->{$valueField} ) ? $item->{$valueField} : $item->{$nameField});
					$itemProperties [$nameField] = $itemValue;
				}
			}
			echo "CREATE item: " . print_r ( $itemProperties, 1 ) . "\n";
			$result = createListItem ( $dstList, $itemProperties );
			if (! $result) {
				echo "CREATE item failed!";
				exit ();
			} else {
				$results [] = $result;
			}
		}
		echo count ( $results ) . " items created\n";
		print_r ( $results );
	} catch ( Exception $e ) {
		echo 'Error: ', $e->getMessage (), "\n";
	}
}
