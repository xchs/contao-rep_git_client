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
 * Table tl_rep_git_client_projects
 */
$GLOBALS['TL_DCA']['tl_rep_git_client_projects'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_rep_git_client',
		'closed'                      => true,
		'label'                       => &$GLOBALS['TL_LANG']['MOD']['rep_git_client'][0]
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('repBranch'),
			'flag'                    => 1,
			'panelLayout'             => 'search,sort,filter,limit ',
			'headerFields' => array
			(
				'repUser',
				'repRepository',
				'repPushed'
			),
			'child_record_callback' => array
			(
				'tl_rep_git_client_projects',
				'listField'
			)
		),
		'label' => array
		(
			'fields'                  => array('repBranch', 'installPath', 'installDir'),
			'format'                  => '%s<br>Installpath: %s/%s'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
			),
			'import' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['import'],
				'href'                => 'table=tl_rep_git_client_restore',
				'icon'                => 'theme_import.gif'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{title_legend},repRepository,installPath,installDir,ignoredFiles'
	),

	// Fields
	'fields' => array
	(
		'repRepository' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['repRepository'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('readonly'=>'readonly'),
			'load_callback' => array
			(
				array("tl_rep_git_client_projects",	"loadRepositoryName")
			)
		),
		'installPath' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installPath'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'tl_class'=>'clr', 'path'=>'/')
		),
		'installDir' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installDir'],
			'exclude'                 => true,
			'inputType'               => 'text'
		),
		'ignoredFiles' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['ignoredFiles'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'options_callback'        => array('tl_rep_git_client_projects', 'getFileList'),
			'eval'                    => array('multiple'=>true)
		),
		'repType' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['repType'],
			'filter'                  => true,
			'inputType'               => 'text'
		)
	)
);


/**
 * Class tl_rep_git_client_projects
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Stefan Lindecke 2012
 * @author     Stefan Lindecke <stefan@chektrion.de>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @package    GitHub Client
 */
class tl_rep_git_client_projects extends Backend
{

	/**
	 * Import the GitHub API library, the Database and the Input object
	 */
	public function __construct()
	{
		include (TL_ROOT . "/plugins/github/ApiInterface.php");
		include (TL_ROOT . "/plugins/github/Api.php");
		include (TL_ROOT . "/plugins/github/Api/Repo.php");
		include (TL_ROOT . "/plugins/github/Api/Object.php");
		include (TL_ROOT . "/plugins/github/Autoloader.php");
		include (TL_ROOT . "/plugins/github/Client.php");
		include (TL_ROOT . "/plugins/github/HttpClientInterface.php");
		include (TL_ROOT . "/plugins/github/HttpClient.php");
		include (TL_ROOT . "/plugins/github/HttpClient/Curl.php");
		include (TL_ROOT . "/plugins/github/HttpClient/Exception.php");

		// Do not use this autoloader. Will not work with Contao autoloader
		// Github_Autoloader::register();
		$this->objGithub = new Github_Client();

		$this->import("Database");
		$this->import("Input");
	}

	/**
	 * Get the file list
	 * @param  DataContainer $dc The DataContainer Object
	 * @return Array             Files to be ignored
	 */
	public function getFileList(DataContainer $dc)
	{
		$arrFiles = deserialize($dc->activeRecord->allFiles);

		$arrIgnoreFiles = array();
		if (is_array($arrFiles))
		{
			foreach ($arrFiles as $key => $value)
			{
				$arrIgnoreFiles[$value] = $key;
			}
		}

		sort($arrIgnoreFiles);
		return $arrIgnoreFiles;
	}

	/**
	 * List the repository branches
	 * @param  Array $arrRow The repository branches array
	 * @return string        The HTML markup
	 */
	public function listField($arrRow)
	{
		return $arrRow['repType'] . ': ' . '<strong>' . $arrRow['repBranch'] . '</strong><br><br>
				INSTALL: TL_ROOT' . $arrRow['installPath'] . '/' . $arrRow['installDir'] . "\n";
	}

	/**
	 * Load the repository name
	 * @return mixed The repository name
	 */
	public function loadRepositoryName()
	{
		$objBranch = new libContaoConnector("tl_rep_git_client_projects", "id", $this->Input->get("id"));
		$objConfig = new libContaoConnector("tl_rep_git_client", "id", $objBranch->pid);

		return $objConfig->repRepository;
	}

}