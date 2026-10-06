<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

/**
 * Advisories excluded from the security audit report of `./yii deps/summarize-changes`
 * (e.g. mitigated by a local patch, or not affecting stat.ink in practice).
 * Leave a comment explaining why each advisory is ignored.
 *
 * - id: advisory ID (GHSA-*, CVE-*, or composer's PKSA-*). Case-insensitive.
 * - package: (optional) restrict the rule to this package name
 */
return [
    // CVE-2024-6485: mitigated by BootstrapButtonPatchAsset
    [
        'id' => 'GHSA-vxmc-5x29-h64v',
        'package' => 'bootstrap',
    ],
    // CVE-2025-1647: mitigated by data/patch/npm/bootstrap+3.4.1.patch
    [
        'id' => 'GHSA-q58r-hwc8-rm9j',
        'package' => 'bootstrap',
    ],
];
