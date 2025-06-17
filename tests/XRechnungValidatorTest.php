<?php

use PHPUnit\Framework\TestCase;
use Doards\XRechnungValidator\UBLStructureValidation;
use Doards\XRechnungValidator\EN16931Validator;

class XRechnungValidatorTest extends TestCase
{
    private string $xsdPath;
    private string $xsltPath;
    private string $xmlFile;

    protected function setUp(): void
    {
        $this->xsdPath = __DIR__ . '/../resources/ubl/2.1/xsd/maindoc/UBL-Invoice-2.1.xsd';
        $this->xsltPath = __DIR__ . '/../resources/peppol/billing-bis/3.0.18/PEPPOL-EN16931-UBL.xslt';
        $this->xmlFile = __DIR__ . '/../examples/base-example.xml';

        $this->assertFileExists($this->xsdPath,  'Missing XSD schema file.');
        $this->assertFileExists($this->xsltPath, 'Missing XSLT Schematron file.');
        $this->assertFileExists($this->xmlFile,  'Missing test XML invoice.');
    }

    public function testUBLStructureValidation(): void
    {
        $validator = new UBLStructureValidation($this->xsdPath);
        $result = $validator->validate($this->xmlFile);

        $this->assertTrue($result === true, 'UBL XSD Validation failed: ' . print_r($result, true));
    }

    public function testEN16931SemanticValidation(): void
    {
        $validator = new EN16931Validator($this->xsltPath);
        $result = $validator->validate($this->xmlFile);

        $this->assertTrue($result === true, 'EN16931 Semantic Validation failed: ' . print_r($result, true));
    }
}
