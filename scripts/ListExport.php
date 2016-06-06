<?php

namespace SharePoint\PHP\Client;

require_once (__DIR__ . '/config.php');

$schemaFileName = getSchemaFileName ();
if (file_exists ( $schemaFileName )) {
	echo "$schemaFileName already exists\n";
	exit ();
}
foreach ( DATA_LISTS as $listName ) {
	listExport ( $listName, $Settings );
}


function listExport($srcListName, $Settings) {
	if (! $srcListName) {
		echo "no listname provided";
		exit ();
	}
	
	$fieldsFileName = getFieldsFileName ( $srcListName );
	$valuesFileName = getValuesFileName ( $srcListName );
	$schemaFileName = getSchemaFileName ();
	
	if (file_exists ( $fieldsFileName )) {
		echo "$fieldsFileName already exists\n";
		exit ();
	}
	
	if (file_exists ( $valuesFileName )) {
		echo "$valuesFileName already exists\n";
		exit ();
	}
	try {
		// authenticate and aquire cookie
		$authCtx = new NtlmAuthenticationContext ( $Settings ['Url'], $Settings ['UserName'], $Settings ['Password'] );
		$authCtx->acquireTokenForUser ( $Settings ['UserName'], $Settings ['Password'] );
		$srcContext = new ClientContext ( $Settings ['Url'], $authCtx );
		
		/* @var $srcList SPList */
		$srcList = getList ( $srcContext, $srcListName );
		if (is_null ( $srcList )) {
			echo "$srcListName does not exist\n";
			exit ();
		} else {
			$schemaData = array();
			if (file_exists ( $schemaFileName )) {
				$schemaData = getJsonForFile($schemaFileName);
			}
			$listProperties = $srcList->getProperties ();
			$schemaData [] = $listProperties;
			$jsonSchemaData = json_encode ( $schemaData );
			$jsonError = getJsonError ();
			if ($jsonError) {
				echo "error encoding schema file: $jsonError\n";
				exit ();
			}
			$schemaFileResult = file_put_contents ( $schemaFileName, $jsonSchemaData );
			if ($schemaFileResult === false) {
				echo "error writing to schema file: $fieldsFileName\n";
				exit ();
			} else {
				echo "wrote list schema to $schemaFileName:" . print_r ( $listProperties, 1 ) . "\n";
			}
		}
		$originalFields = $srcList->getFields ();
		$srcContext->load ( $originalFields );
		$srcContext->executeQuery ();
		$fieldData = [ ];
		foreach ( $originalFields->getData () as $field ) {
			$fieldData [] = $field->getProperties ();
		}
		$jsonFieldData = json_encode ( $fieldData );
		$jsonError = getJsonError ();
		if ($jsonError) {
			echo "error encoding fields: $jsonError\n";
			exit ();
		}
		$fieldsFileResult = file_put_contents ( $fieldsFileName, $jsonFieldData );
		if ($fieldsFileResult === false) {
			echo "error creating fields file: $fieldsFileName\n";
			exit ();
		} else {
			echo "wrote " . count ( $fieldData ) . " fields to $fieldsFileName\n";
		}
		
		// selects only return 100 results by default, top used as workaround
		$originalItems = $srcList->getItems ()->top ( 1000 );
		$srcContext->load ( $originalItems );
		$srcContext->executeQuery ();
		$valueData = [ ];
		foreach ( $originalItems->getData () as $item ) {
			$valueData [] = $item->getProperties ();
		}
		$jsonValueData = json_encode ( $valueData );
		$jsonError = getJsonError ();
		if ($jsonError) {
			echo "error encoding values: $jsonError\n";
			exit ();
		}
		$valuesFileResult = file_put_contents ( $valuesFileName, $jsonValueData );
		if ($valuesFileResult === false) {
			echo "error creating values file: $valuesFileName\n";
			exit ();
		} else {
			echo "wrote " . count ( $valueData ) . " fields to $valuesFileName\n";
		}
	} catch ( Exception $e ) {
		echo 'Error: ', $e->getMessage (), "\n";
	}
}


