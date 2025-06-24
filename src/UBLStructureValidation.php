<?php

namespace Doards\XRechnungValidator;

use Exception;

class UBLStructureValidation
{
    private string $xsdPath;

    public function __construct(string $xsdPath)
    {
        $this->xsdPath = $xsdPath;
    }

    /**
     * Validates XML against UBL XSD schema.
     *
     * @param string $xmlFile
     * @return array{errors: string[]} Validation result (only errors)
     */
    public function validate(string $xmlFile, bool $generateHtml = false): array
    {
        $xmlFile = realpath($xmlFile);
        if (!$xmlFile || !file_exists($xmlFile)) {
            throw new Exception("XML file not found: {$xmlFile}");
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->load($xmlFile);

        $errors = [];

        if (!$doc->schemaValidate($this->xsdPath)) {
            $libxmlErrors = libxml_get_errors();
            libxml_clear_errors();
            foreach ($libxmlErrors as $error) {
                $errors[] = trim($error->message);
            }
        }

        $result = ['errors' => $errors];
        if ($generateHtml) {
            ValidationResultReporter::writeHtmlSection('UBL Structure Validation', $result);
        }

        return $result;
    }
}
