<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doards\XRechnungValidator\UBLStructureValidation;
use Doards\XRechnungValidator\EN16931Validator;

$xsdPath = __DIR__ . '/resources/ubl/2.1/xsd/maindoc/UBL-Invoice-2.1.xsd';
$schematronXsltPath = __DIR__ . '/resources/peppol/billing-bis/3.0.18/PEPPOL-EN16931-UBL.xslt';
$xmlFile = __DIR__ . '/examples/base-example.xml';

echo "▶️  Running UBL structure validation...\n";
$structureValidator = new UBLStructureValidation($xsdPath);
$structureResult = $structureValidator->validate($xmlFile);

echo "\n▶️  Running EN16931 Schematron validation...\n";
$semanticValidator = new EN16931Validator($schematronXsltPath);
$semanticResult = $semanticValidator->validate($xmlFile);

var_dump($structureResult, true);

var_dump($semanticResult, true);