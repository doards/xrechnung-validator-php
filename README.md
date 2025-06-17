# XRechnung Validator for PHP

This library validates **electronic invoices** (XRechnungen) according to:

- **UBL 2.1 structural rules** (via XSD validation)
- **EN 16931 semantic rules** (via Schematron using XSLT 2.0/3.0)

> 🛠️ Built for use with **SaxonC** (required for XSLT 3.0 in PHP)

## 📁 Installation

Include this project in your PHP codebase (or copy the validator classes into your namespace).  
Autoloading via Composer is supported if you define it in `composer.json`.

```bash
composer dump-autoload
```

---

## 🚀 Features

- ✅ UBL 2.1 XSD structure validation
- ✅ EN 16931 Schematron validation via XSLT
- ✅ Works with PHP 8.x
- ✅ Clean object-oriented API

---

## 📦 Requirements

- PHP 8.1 or higher
- [SaxonC 12.x](https://www.saxonica.com/saxon-c/documentation12/index.html#!starting/installing) (must be installed and available via `saxon.so`)
- DOM extension (enabled by default in PHP)
- `DYLD_LIBRARY_PATH` (macOS) or `LD_LIBRARY_PATH` (Linux) must include the SaxonC library path

> ❗ `saxon.so` must be loaded via `php.ini` or a custom `.ini` file

---

## Basic Usage

```php
use Doards\XRechnungValidator\UBLStructureValidation;
use Doards\XRechnungValidator\EN16931Validator;

$xsdPath  = __DIR__ . '/resources/ubl/2.1/xsd/maindoc/UBL-Invoice-2.1.xsd';
$schematronXsltPath = __DIR__ . '/resources/peppol/billing-bis/3.0.18/PEPPOL-EN16931-UBL.xslt';
$xmlFile  = __DIR__ . '/examples/base-example.xml';

// Validate UBL structure
$ublValidator = new UBLStructureValidation($xsdPath);
$ublResult = $ublValidator->validate($xmlFile);

// Validate EN16931 semantic rules
$enValidator = new EN16931Validator($xsltPath);
$enResult = $enValidator->validate($xmlFile);

// Output results
print_r($ublResult);
print_r($enResult);
```

📄 HTML Report Output
Both validators support an optional HTML reporting mode.
When enabled, a results.html file will be created (or updated) in the project root.

✅ Example usage with HTML report enabled:

```php
$ublValidator = new UBLStructureValidation($xsdPath);
$ublValidator->validate($xmlFile, true); // Will write UBL results to results.html

$enValidator = new EN16931Validator($xsltPath);
$enValidator->validate($xmlFile, true); // Will append EN results to the same file
```

The resulting results.html includes:

Clear sections per validation type

Errors (in red)

Warnings (in orange)

## 📄 License

This project is open-source and licensed under the [MIT License](./LICENSE).

---

## 📚 Attribution

This validator makes use of Schematron rules and XSLT stylesheets derived from the official XRechnung validator configuration provided by the KoSIT project:

- https://github.com/itplr-kosit/validator-configuration-bis/tree/master

All rights and licenses for these resources remain with their respective maintainers.
