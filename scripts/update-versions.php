<?php
/**
 * Script to update versions.php with new Yii framework releases
 * Usage: php update-versions.php <yii_version> <tag_name> <release_date>
 */

if ($argc !== 4) {
    echo "Usage: php update-versions.php <yii_version> <tag_name> <release_date>\n";
    echo "Example: php update-versions.php 2.0 2.0.54 'Aug 15, 2025'\n";
    exit(1);
}

$yiiVersion = $argv[1];
$tagName = $argv[2];
$releaseDate = $argv[3];

$versionsFile = __DIR__ . '/../config/versions.php';

if (!file_exists($versionsFile)) {
    echo "Error: versions.php not found at $versionsFile\n";
    exit(1);
}

// Read the current versions file
$content = file_get_contents($versionsFile);

// Check if this version already exists
if (strpos($content, "'$tagName'") !== false) {
    echo "Version $tagName already exists\n";
    exit(0);
}

// Find the minor-versions section and then the specific Yii version
$minorVersionsPos = strpos($content, "'minor-versions' => [");
if ($minorVersionsPos === false) {
    echo "Error: Could not find minor-versions section\n";
    exit(1);
}

// Find the specific version section within minor-versions
$versionSectionPattern = "'$yiiVersion' => [";
$versionPos = strpos($content, $versionSectionPattern, $minorVersionsPos);

if ($versionPos === false) {
    echo "Error: Could not find version section for Yii $yiiVersion\n";
    exit(1);
}

// Find the first version entry (should be right after the opening bracket)
$afterBracket = $versionPos + strlen($versionSectionPattern);
$nextNewline = strpos($content, "\n", $afterBracket);
$insertPos = $nextNewline + 1;

// Insert the new version at the beginning of the list (most recent first)
$newVersionLine = "            '$tagName' => '$releaseDate',\n";
$updatedContent = substr($content, 0, $insertPos) . $newVersionLine . substr($content, $insertPos);

// Write the updated content back to the file
if (file_put_contents($versionsFile, $updatedContent) === false) {
    echo "Error: Could not write to $versionsFile\n";
    exit(1);
}

echo "Successfully added $tagName ($releaseDate) to Yii $yiiVersion versions\n";