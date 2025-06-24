<?php

namespace Doards\XRechnungValidator;

use Saxon\SaxonProcessor;
use DOMDocument;
use Exception;

class EN16931Validator
{
    private string $schematronXsltPath;

    public function __construct(string $schematronXsltPath)
    {
        $this->schematronXsltPath = $schematronXsltPath;
    }

    /**
     * Validates the given XML against EN16931 rules (Schematron via XSLT).
     *
     * @param string $xmlFile
     * @return array{errors: string[], warnings: string[]} Validation results
     * @throws Exception
     */
    public function validate(string $xmlFile, bool $generateHtml = false): array
    {
        if (!extension_loaded('saxonc')) {
            throw new Exception('SaxonC extension is not loaded in PHP.');
        }

        $xmlFile = realpath($xmlFile);
        if (!$xmlFile || !file_exists($xmlFile)) {
            throw new Exception("XML file not found: {$xmlFile}");
        }

        $proc = new SaxonProcessor();
        $xsltProcessor = $proc->newXslt30Processor();

        $xsltPath = realpath($this->schematronXsltPath);
        if (!$xsltPath || !file_exists($xsltPath)) {
            throw new Exception("XSLT file not found: {$this->schematronXsltPath}");
        }

        $xsltExecutable = $xsltProcessor->compileFromFile($xsltPath);
        if (!$xsltExecutable) {
            throw new Exception("Failed to compile XSLT: $xsltPath");
        }

        $svrlXml = $xsltExecutable->transformFileToString($xmlFile);
        if ($svrlXml === null) {
            throw new Exception('XSLT transformation failed.');
        }

        $dom = new DOMDocument();
        if (!@$dom->loadXML($svrlXml)) {
            throw new Exception('Failed to parse SVRL XML.');
        }

        $result = ['errors' => [], 'warnings' => []];
        $failedAsserts = $dom->getElementsByTagName('failed-assert');

        foreach ($failedAsserts as $assert) {
            $text = $assert->getElementsByTagName('text')->item(0)?->nodeValue ?? '';
            $flag = $assert->getAttribute('flag') ?: 'error';

            $message = trim($text);

            // Move specific error to warnings
            if ($message === 'Document MUST not contain empty elements.') {
                $result['warnings'][] = $message;
                continue;
            }

            if ($flag === 'fatal' || $flag === 'error') {
                $result['errors'][] = $message;
            } elseif ($flag === 'warning') {
                $result['warnings'][] = $message;
            } else {
                $result['warnings'][] = "[Unknown flag: $flag] $message";
            }
        }

        if ($generateHtml) {
            ValidationResultReporter::writeHtmlSection('EN16931 Semantic Validation', $result);
        }

        return $result;
    }
}
