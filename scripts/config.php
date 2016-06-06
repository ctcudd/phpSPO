<?php

namespace SharePoint\PHP\Client;
// require_once __DIR__ .'/../src/taxonomy/TaxonomyField.php';
// require_once __DIR__ .'/../src/FieldLookup.php';
require_once (__DIR__ . '/../src/ClientContext.php');
require_once (__DIR__ . '/../src/runtime/ClientQuery.php');
require_once (__DIR__ . '/../src/runtime/ClientRequest.php');
require_once (__DIR__ . '/../src/List.php');
require_once (__DIR__ . '/../src/FieldCreationInformation.php');
require_once (__DIR__ . '/../src/XmlFieldCreationInformation.php');
require_once (__DIR__ . '/../src/ComplexFieldCreationInformation.php');
require_once (__DIR__ . '/../src/InternalFields.php');
require_once (__DIR__ . '/../src/FieldTypeKind.php');
require_once (__DIR__ . '/../src/auth/NtlmAuthenticationContext.php');
require_once (__DIR__ . '/../examples/Settings.php');

use SharePoint\PHP\Client\NtlmAuthenticationContext;
use SharePoint\PHP\Client\ClientContext;
use SharePoint\PHP\Client\ListCreationInformation;
use SharePoint\PHP\Client\TaxonomyField;
use SharePoint\PHP\Client\FieldLookup;

const DATA_DIR = __DIR__ . '/../data/';
const SEPARATOR = "-";
const FILE_EXTENSION = ".json";
const DATA_LISTS = array (
		// lists without lookup columns first
		'ApplicationLevels',
		'ApplicationTypes',
		'Environments',
		'ExternalSupportContacts',
		'Glossary', // not related to dynamic docs, but might as well backup?
		'OperatingSystems',
		'SupportTypes',
		
		// depends on 'OperatingSystems'
		'OperatingSystemVersions',
		
		// depends on 'OperatingSystems' and 'OperatingSystemVersions'
		'Servers',
		
		// depends on 'ExternalSupportcontacts',
		'SupportContacts',
		
		// depends on 'SupportContacts'
		'WebServices',
		
		// depends on 'Environments' and 'WebServices'
		'WebServiceEndpoints',
		
		// depends on servers
		'Applications',
		
		// depends on 'Applications' and 'WebServiceEndpoints'
		'WebServiceIntegrations' 
);
function getFieldsFileName($listName) {
	return getFilename ( $listName, "fields" );
}
function getValuesFileName($listName) {
	return getFilename ( $listName, "values" );
}
function getSchemaFileName() {
	return getFilename ( "lists", "schema" );
}
function getFilename($listName, $string) {
	return DATA_DIR . $listName . SEPARATOR . $string . FILE_EXTENSION;
}

/**
 * Read the file and attempt to decode the json data.
 * 
 * @param String $fileName        	
 * @throws Exception if unable to read or decode the file
 * @return array of json objects
 */
function getJsonForFile($fileName) {
	$jsonData = file_get_contents ( $fileName );
	if ($jsonData === false) {
		throw new Exception ( "error reading from file: $fileName" );
	}
	$decodedData = json_decode ( $jsonData );
	$jsonError = getJsonError ();
	if ($jsonError) {
		throw new Exception ( "error decoding json file: $fileName, error: $jsonError\n" );
		exit ();
	}
	return $jsonData;
}
/**
 * @param String $fileName, the file to write to
 * @param mixed $data, the data to encode and write to file
 * @throws Exception, if the data cannot be encoded or the file cannot be accessed.
 * @return boolean, true if successful, false otherwise.
 */
function putJsonToFile($fileName, $data) {
	$jsonData = json_encode ( $data );
	$jsonError = getJsonError ();
	if ($jsonError) {
		throw new Exception ( "error encoding data: $jsonError");
	}
	$result = file_put_contents ( $fileName, $jsonFieldData );
	if ($result === false) {
		throw new Exception ( "error creating fields file: $fileName");
	} else {
		echo "wrote " . count ( $fieldData ) . " fields to $fileName\n";
	}
	return $result;
}
/**
 * Create list item operation example
 *
 * @param \SharePoint\PHP\Client\SPList $list        	
 * @param array $itemProperties        	
 * @return mixed|null
 */
function createListItem(\SharePoint\PHP\Client\SPList $list, array $itemProperties) {
	$ctx = $list->getContext ();
	$item = $list->addItem ( $itemProperties );
	$ctx->executeQuery ();
	return $item->Id;
}
/**
 *
 * @param unknown $ctx        	
 * @param unknown $title        	
 * @return \SharePoint\PHP\Client\SPList
 */
function getList($ctx, $title) {
	$list = null;
	$lists = $ctx->getWeb ()->getLists ();
	$ctx->load ( $lists );
	$ctx->executeQuery ();
	foreach ( $lists->getData () as $curList ) {
		if ($title == $curList->Title) {
			print "list found by name. title: {$curList->Title}, id: {$curList->Id}\r\n";
			return $curList;
		} elseif ($title == $curList->Id) {
			print "list found by id. title: {$curList->Title}, id: {$curList->Id}\r\n";
			return $curList;
		}
	}
}
/**
 * Delete list operation example
 */
function deleteList(\SharePoint\PHP\Client\SPList $list) {
	$ctx = $list->getContext ();
	$list->deleteObject ();
	$ctx->executeQuery ();
	print "List '{$list->Title}' has been deleted.\r\n";
}
function getJsonError() {
	if (! function_exists ( 'json_last_error_msg' )) {
		function json_last_error_msg() {
			static $ERRORS = array (
					JSON_ERROR_NONE => 'No error',
					JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
					JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
					JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
					JSON_ERROR_SYNTAX => 'Syntax error',
					JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded' 
			);
			
			$error = json_last_error ();
			return isset ( $ERRORS [$error] ) ? $ERRORS [$error] : 'Unknown error';
		}
	}
}

function startsWith($haystack, $needle) {
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}