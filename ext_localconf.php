<?php

/*
 * This file is part of the package ku_driftinformation.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3') or die('Access denied.');

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

$versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
// Only include page.tsconfig if TYPO3 version is below 12 so that it is not imported twice.
if ($versionInformation->getMajorVersion() < 12) {
  ExtensionManagementUtility::addPageTSConfig('
      @import "EXT:ku_driftinformation/Configuration/page.tsconfig"
   ');
}

// Register plugin
ExtensionUtility::configurePlugin(
  'ku_driftinformation',
  'Pi1',
  [\UniversityOfCopenhagen\KuDriftinformation\Controller\DriftinfoController::class => 'list'],
  [\UniversityOfCopenhagen\KuDriftinformation\Controller\DriftinfoController::class => 'list']
);

// KU driftinformation Viewhelper namespace
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['kuDriftinfo'] = ['UniversityOfCopenhagen\KuDriftinformation\ViewHelpers'];