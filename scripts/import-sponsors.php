<?php

/**
 * Refresh locally: php scripts/import-sponsors.php /private/export.csv > /tmp/sponsors.php
 * Review the result before replacing config/sponsors.php. Its identities/logos
 * are a verified public allowlist; add new names only after public verification.
 * Unverified accounts are acknowledged collectively. No runtime CSV dependency.
 * A full contribution history is required; missing approved handles abort the refresh.
 */
require dirname(__DIR__) . '/components/Sponsors.php';

if ($argc !== 2) {
    fwrite(STDERR, "Usage: php scripts/import-sponsors.php /path/to/export.csv\n");
    exit(1);
}
try {
    $data = require dirname(__DIR__) . '/config/sponsors.php';
    $contributions = \app\components\Sponsors::readContributions($argv[1]);
    $approved = array_column($data['sponsors'], null, 'handle');
    if (array_diff_key($approved, $contributions['accounts'])) {
        throw new \RuntimeException('Export is missing approved sponsors; provide the full contribution history.');
    }
    $data['snapshot'] = $contributions['snapshot'];
    $data['sponsors'] = [];
    foreach ($contributions['accounts'] as $handle => $account) {
        if (!isset($approved[$handle])) {
            continue;
        }
        $sponsor = $approved[$handle];
        $sponsor['lastContribution'] = $account['lastContribution'];
        $data['sponsors'][] = $sponsor;
    }
    echo "<?php\n\n// Verified public profiles, ordered by lifetime eligible contributions. No individual amounts.\n";
    echo '// Refresh with scripts/import-sponsors.php; verify new public identities before adding them.' . "\n";
    echo "return [\n";
    foreach (['snapshot', 'currentMonths', 'featuredCount'] as $key) {
        echo '    ' . var_export($key, true) . ' => ' . var_export($data[$key], true) . ",\n";
    }
    echo "    'sponsors' => [\n";
    foreach ($data['sponsors'] as $sponsor) {
        $fields = [];
        foreach ($sponsor as $key => $value) {
            $fields[] = var_export($key, true) . ' => ' . var_export($value, true);
        }
        echo '        [' . implode(', ', $fields) . "],\n";
    }
    echo "    ],\n];\n";
    fwrite(STDERR, sprintf("Aggregated %d eligible contributions across %d handles; retained %d approved public names.\n",
        $contributions['eligible'], count($contributions['accounts']), count($data['sponsors'])));
} catch (\Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
