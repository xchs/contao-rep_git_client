<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Stefan Lindecke 2012
 * @author     Stefan Lindecke <stefan@chektrion.de>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @package    GitHub Client
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['repRepository'] = array('Projektarchiv', 'Bitte geben Sie das gewünschte GitHub Projektarchiv ein.');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installPath']   = array('Projektinstallation', 'Bitte wählen Sie einen Ordner aus der Verzeichnisstruktur, welcher die Projektinstallation enthalten soll.');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installDir']    = array('Installationsordner', 'Bitte geben Sie einen Namen für den Installationsordner ein. In dieses Verzeichnis wird die Projektinstallation importiert.');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['ignoredFiles']  = array('Dateien ignorieren', 'Die ausgewählten Dateien werden nicht importiert.');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['repType']       = array('Entwicklungszweig', 'Ein Entwicklungszweig im Projektarchiv.');


/**
 * Legend
 */
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['title_legend'] = 'Einstellungen der Projektinstallation';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['editheader'] = array('Projektarchiv-Einstellungen bearbeiten', 'Die Projektarchiv-Einstellungen bearbeiten');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['edit']       = array('Projektinstallation bearbeiten', 'Projektinstallation ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['delete']     = array('Projektinstallation löschen', 'Projektinstallation ID %s löschen');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['import']     = array('Projektinstallation importieren', 'Projektinstallation ID %s importieren bzw. aktualisieren');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['show']       = array('Projektinstallationsdetails', 'Details der Projektinstallation ID %s anzeigen');
