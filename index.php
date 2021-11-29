<?php

$secretServerURL = "https://www.secretserveronline.com/webservices/SSWebService.asmx?WSDL";

$username = "thycotictest";

$password = "passwordt";
$organizationCode = "RT9R"; //only needed for Secret Server Online account
$secretId = 154178;

//Create the SOAP Client

print $secretServerURL . "\n";
print "\n";
print "\n";

$soapClient = new SoapClient($secretServerURL);

//Get Version (simplest call)

$versionResult = $soapClient->__soapCall("VersionGet", array());
$version = $versionResult->VersionGetResult->Version;
print "Secret Server Version is " . $version;
print "\n";
print "\n";

//Authenticate

$params = array();
$params["username"] = $username;
$params["password"] = $password;
$params["organization"] = $organizationCode;

$authenticationResult = $soapClient->Authenticate($params);
$errors = (array) $authenticationResult->AuthenticateResult->Errors;
if (count($errors) > 0) {
    print "Login Error for user(" . $username . ") : " . $errors["string"] . "\n";
    return;
}

print "Login Successful \n\n";
$token = $authenticationResult->AuthenticateResult->Token;

//Load the Secret

$params = array();
$params["token"] = $token;
$params["secretId"] = $secretId;

$secretGetResult = $soapClient->GetSecret($params);
//var_dump($secretGetResult);
$errors = (array) $secretGetResult->GetSecretResult->Errors;
if (count($errors) > 0) {
    print "Error getting Secret Id (" . $secretId . ") : " . $errors["string"] . "\n";
    return;
}



$secret = $secretGetResult->GetSecretResult->Secret;
$secretTemplateId = $secret->SecretTypeId;
$secretName = $secret->Name;
$secretItems = (array)$secret->Items->SecretItem;

print "Secret Name: " . $secretName . "\n\n";

foreach ($secretItems as $secretItem) {
    $fieldName = $secretItem->FieldName;
    $fieldValue = $secretItem->Value;
    print $fieldName . " : " . $fieldValue . "\n";
}

print "\n\n";

//Update the Notes Field on the Secret

$updatedSecret = $secret;
//var_dump($secret);
$timestamp = @date("M-d-Y h:i:s", time());
$updatedValue = "This value was updated through webservices at " . $timestamp;
$indexOfNotes = 3;
print "Updating the Field (" . $updatedSecret->Items->SecretItem[$indexOfNotes]->FieldName . ") to : \n'" . $updatedValue . "'\n\n";
$updatedSecret->Items->SecretItem[$indexOfNotes]->Value = $updatedValue;

$params = array();
$params["token"] = $token;
$params["secret"] = $updatedSecret;

$secretUpdateResult = $soapClient->UpdateSecret($params);
$errors = (array) $secretUpdateResult->UpdateSecretResult->Errors;
if (count($errors) > 0) {
    print "Error updating Secret Id (" . $secretId . ") : " . $errors["string"] . "\n";
    return;
}

print "Update Successful\n\n";
