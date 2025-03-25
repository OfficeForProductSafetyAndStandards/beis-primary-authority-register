# Rector extension
The main reason for this change is that the produced patch file is not
recognised as a valid patch file as by default the line numbers are missing.
It uses
[UnifiedDiffOutputBuilder](../../vendor/sebastian/diff/src/Output/UnifiedDiffOutputBuilder.php)
and there is a class __construct() parameter to control this functionality but in
[ConsoleDiffer](../../vendor/rector/rector/src/Console/Formatter/ConsoleDiffer.php)
it does not set it to TRUE, hence this file is fully customised as a different
class
[StrictUnifiedDiffOutputBuilder](../../vendor/sebastian/diff/src/Output/StrictUnifiedDiffOutputBuilder.php).
as well.

# Developers Notes

The files under src are referenced in rector.php and are copies of the files
from vendor/rector/rector. They are needed as the originals are coded with

```php
declare (strict_types=1);
```
and

```php
final class NonPhpFileProcessor implements FileProcessorInterface {
```

so the class can not be extended. The parameters to functions are not PHP
*Interface*

The fully qualified class name is the same as the original but has a namespace
prefix of *Par*

## Customisation
These files have been customised to add the line numbers to the patch file.
* [tests/rector/src/Console/Formatter/ConsoleDiffer.php](src/Console/Formatter/ConsoleDiffer.php)
  is fully customised to add the line numbers to the patch file.
* [tests/rector/src/ChangesReporting/ValueObjectFactory/FileDiffFactory.php](src/ChangesReporting/ValueObjectFactory/FileDiffFactory.php)
```diff
diff --git a/tests/rector/src/ChangesReporting/ValueObjectFactory/FileDiffFactory.php b/tests/rector/src/ChangesReporting/ValueObjectFactory/FileDiffFactory.php
--- a/tests/rector/src/ChangesReporting/ValueObjectFactory/FileDiffFactory.php
+++ b/tests/rector/src/ChangesReporting/ValueObjectFactory/FileDiffFactory.php
@@ -41,6 +41,12 @@
   public function createFileDiffWithLineChanges(File $file, string $oldContent, string $newContent, array $rectorsWithLineChanges) : FileDiff
   {
     $relativeFilePath = $this->filePathHelper->relativePath($file->getFilePath());
+
+    if (method_exists($this->consoleDiffer, 'setFile')) {
+      // Inform the console differ of the file being processed.
+      $this->consoleDiffer->setFile($relativeFilePath);
+    }
+
     // always keep the most recent diff
     return new FileDiff($relativeFilePath, $this->defaultDiffer->diff($oldContent, $newContent), $this->consoleDiffer->diff($oldContent, $newContent), $rectorsWithLineChanges);
   }
```

The other files under tests/rector/src are just copies of the original files
with changes to the name space.

## Check a contrib module or theme
```bash
cd web/modules/contrib/module_name;
../../../../vendor/bin/rector --dry-run --no-progress-bar --config=./../../../../tests/rector/rector.php process .
```

## Code Styling
These copies has been excludes from Drupal code styling checking by adding
```php
// phpcs:ignoreFile
```

## PSR4 composer.json addition
 The following needs to be added to composer.json to enable the composer
 autoloader to find the new classes.
```json
{
    "autoload": {
        "psr-4": {
            "Par\\Rector\\": "path/to/rector/src"
        }
    }
}
```
