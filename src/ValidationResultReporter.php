<?php

namespace Doards\XRechnungValidator;

class ValidationResultReporter
{
    private static string $reportFile;
    private static bool $initialized = false;

    /**
     * Initializes output path and creates a fresh report.
     */
    private static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        // Always write to the root of the calling project (not this package)
        self::$reportFile = getcwd() . DIRECTORY_SEPARATOR . 'results.html';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XRechnung Validation Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; background: #f9f9f9; }
        h1 { color: #333; }
        .error { color: red; }
        .warning { color: orange; }
        .section { margin-bottom: 2em; }
        ul { padding-left: 1.5em; }
    </style>
</head>
<body>
    <h1>XRechnung Validation Report</h1>

HTML;

        file_put_contents(self::$reportFile, $html);
        self::$initialized = true;
    }

    /**
     * Adds a validation section to the report (creates fresh file on first call).
     *
     * @param string $title
     * @param array $result ['errors' => [...], 'warnings' => [...]]
     */
    public static function writeHtmlSection(string $title, array $result): void
    {
        self::init();

        $section = "<div class='section'>\n<h2>" . htmlspecialchars($title) . "</h2>\n";

        if (empty($result['errors']) && empty($result['warnings'])) {
            $section .= "<p>✅ No issues found.</p>\n";
        } else {
            if (!empty($result['errors'])) {
                $section .= "<h3 class='error'>Errors</h3>\n<ul class='error'>\n";
                foreach ($result['errors'] as $error) {
                    $section .= "<li>" . htmlspecialchars($error) . "</li>\n";
                }
                $section .= "</ul>\n";
            }

            if (!empty($result['warnings'])) {
                $section .= "<h3 class='warning'>Warnings</h3>\n<ul class='warning'>\n";
                foreach ($result['warnings'] as $warn) {
                    $section .= "<li>" . htmlspecialchars($warn) . "</li>\n";
                }
                $section .= "</ul>\n";
            }
        }

        $section .= "</div>\n";

        // Append to existing file
        file_put_contents(self::$reportFile, $section, FILE_APPEND);
    }

    /**
     * Call once after all validations are done to close the HTML
     */
    public static function close(): void
    {
        if (self::$initialized) {
            file_put_contents(self::$reportFile, "</body></html>\n", FILE_APPEND);
        }
    }
}
