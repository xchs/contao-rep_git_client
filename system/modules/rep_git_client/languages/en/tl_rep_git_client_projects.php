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
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['repRepository'] = array('Repository', 'Please select the appropriate GitHub repository.');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installPath']   = array('Repository installation', 'Please select a folder from the directory structure, which will contain the repository installation.');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installDir']    = array('Installation folder', 'Please enter a name for the installation folder. In this directory, the repository installation will be imported.');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['ignoredFiles']  = array('Ignore files', 'The selected files are not imported.');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['repType']       = array('Branch', 'A development branch of the repository.');


/**
 * Legend
 */
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['title_legend'] = 'Repository installation settings';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['editheader'] = array('Edit repository settings', 'Edit the repository settings');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['edit']       = array('Edit repository installation', 'Edit repository installation ID %s');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['delete']     = array('Delete repository installation', 'Delete repository installation ID %s');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['import']     = array('Import repository installation', 'Import repository installation ID %s');
$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['show']       = array('Show repository installation details', 'Show the details of repository installation ID %s');
