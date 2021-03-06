<?php

use SharePoint\PHP\Client\ListCreationInformation;

require_once(__DIR__ . '/../src/ClientContext.php');
require_once(__DIR__.'/../src/auth/AuthenticationContext.php');
require_once 'Settings.php';



try {
    $authCtx = new SharePoint\PHP\Client\AuthenticationContext($Settings['Url']);
    $authCtx->acquireTokenForUser($Settings['UserName'],$Settings['Password']);
    $ctx = new SharePoint\PHP\Client\ClientContext($Settings['Url'],$authCtx);

    $localPath = "./data/";
    $targetLibraryTitle = "Documents";

    $list = ensureList($ctx,$targetLibraryTitle);
    uploadFiles($localPath,$list);
    processFiles($list,$localPath);
    //deleteFolder($ctx,$folderUrl);
    //saveFile($ctx,$localFilePath,$fileUrl);

}
catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
}


function processFiles(\SharePoint\PHP\Client\SPList $list,$targetPath)
{
    $ctx = $list->getContext();
    $files = $list->getRootFolder()->getFiles();
    $ctx->load($files);
    $ctx->executeQuery();

    foreach ($files->getData() as $file) {
        print "File name: '{$file->Name}'\r\n";

        //checkoutFile($ctx,$file->ServerRelativeUrl);
        //checkinFile($ctx,$file->ServerRelativeUrl);
        //approveFile($ctx,$file->ServerRelativeUrl);
        $fileName = $targetPath . "/" . basename($file->ServerRelativeUrl);
        downloadFile($ctx,$file->ServerRelativeUrl,$fileName);
    }
}

function ensureList(SharePoint\PHP\Client\ClientContext $ctx,$listTitle){

    $list = null;
    $lists = $ctx->getWeb()->getLists();
    $ctx->load($lists);
    $ctx->executeQuery();
    foreach($lists->getData() as $l) {
        if ($listTitle == $l->Title) {
            $list = $l;
            print "List '{$list->Title}' has been found\r\n";
            break;
        }
    }
    if(is_null($list)) {
        $info = new ListCreationInformation($listTitle);
        $info->BaseTemplate = 101;
        $list = $ctx->getWeb()->getLists()->add($info);
        $ctx->executeQuery();
        print "List '{$list->Title}' has been created\r\n";
    }
    return $list;
}



function deleteFolder(SharePoint\PHP\Client\ClientContext $ctx,$folderUrl){
    $folder = $ctx->getWeb()->getFolderByServerRelativeUrl($folderUrl);
    $folder->deleteObject();
    $ctx->executeQuery();
    print "Folder has been deleted\r\n";
}


function checkoutFile(SharePoint\PHP\Client\ClientContext $ctx,$fileUrl){
    $file = $ctx->getWeb()->getFileByServerRelativeUrl($fileUrl);
    $file->checkOut();
    $ctx->executeQuery();
    print "File has been checked out\r\n";
}


function checkinFile(SharePoint\PHP\Client\ClientContext $ctx,$fileUrl){
    $file = $ctx->getWeb()->getFileByServerRelativeUrl($fileUrl);
    $file->checkIn('');
    $ctx->executeQuery();
    print "File has been checked in\r\n";
}

function approveFile(SharePoint\PHP\Client\ClientContext $ctx,$fileUrl){
    $file = $ctx->getWeb()->getFileByServerRelativeUrl($fileUrl);
    $file->approve('');
    $ctx->executeQuery();
    print "File {$fileUrl} has been approved\r\n";
}

function uploadFiles($localPath,\SharePoint\PHP\Client\SPList $targetList){

    $ctx = $targetList->getContext();

    $searchPrefix = $localPath . '*.*';
    foreach(glob($searchPrefix) as $filename) {
        $fileCreationInformation = array(
            'Content' => file_get_contents($filename),
            'Url' => basename($filename)
        );

        $uploadFile = $targetList->getRootFolder()->getFiles()->add($fileCreationInformation);
        $ctx->executeQuery();
        print "File {$uploadFile->Name} has been uploaded\r\n";
    }


}


function saveFile(SharePoint\PHP\Client\ClientContext $ctx,$sourceFilePath,$targetFileUrl)
{
    $fileContent = file_get_contents($sourceFilePath);
    SharePoint\PHP\Client\File::saveBinary($ctx,$targetFileUrl,$fileContent);
    print "File has been uploaded\r\n";
}


function downloadFile(SharePoint\PHP\Client\ClientContext $ctx,$sourcefileUrl,$targetFilePath){
    $fileContent = SharePoint\PHP\Client\File::openBinary($ctx,$sourcefileUrl);
    file_put_contents($targetFilePath, $fileContent);
    print "File has been downloaded\r\n";
}



?>