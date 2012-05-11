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
 * Table tl_rep_git_client_restore
 */
$GLOBALS['TL_DCA']['tl_rep_git_client_restore'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Memory',
		'closed'                      => true,
		'disableSubmit'               => true,
		'onload_callback' => array
		(
			array('tl_rep_git_client_restore', 'onload_callback'),
		),
		'onsubmit_callback' => array
		(
			array('tl_rep_git_client_restore', 'onsubmit_callback'),
		),
		'dcMemory_show_callback' => array
		(
			array('tl_rep_git_client_restore', 'showAll')
		),
		'dcMemory_showAll_callback' => array
		(
			array('tl_rep_git_client_restore', 'showAll')
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array(''),
		'default'                     => 'importFiles,ignoreFiles,databaseUpdate,startImport'
	),


	// Subpalettes
	'subpalettes' => array
	(
	),

	// Fields
	'fields' => array
	(
		'importFiles' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rep_git_client_restore']['importFiles'],
			'inputType'               => 'checkbox',
			'eval'                    => array('multiple'=>true)
		),
		'ignoreFiles'               => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rep_git_client_restore']['ignoreFiles'],
			'inputType'               => 'statictext',
			'eval'                    => array('readonly'=>true)
		),

		'startImport'               => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rep_git_client_restore']['startImport'],
			'inputType'               => 'statictext',
			'addSubmit'               => true,
			'eval'                    => array('readonly'=>true)
		),
		'databaseUpdate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_rep_git_client_restore']['databaseUpdate'],
			'inputType'               => 'checkbox',
			'eval'                    => array()
		)
	)
);


/**
 * Class tl_rep_git_client_restore
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Stefan Lindecke 2012
 * @author     Stefan Lindecke <stefan@chektrion.de>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @package    GitHub Client
 */
class tl_rep_git_client_restore extends Backend
{

	/**
	 * Import the Config, Input, Database, User and Session object
	 */
	public function __construct()
	{
		$this->import("Config");
		$this->import("Input");
		$this->import("Database");
		$this->import("BackendUser","User");

		$this->import("Session");
	}


	/**
	 * Execute the onloud callback
	 * @param  DataContainer $dc The DataContainer object
	 */
	public function onload_callback(DataContainer $dc)
	{

		$objBranch = new libContaoConnector("tl_rep_git_client_projects", "id", $this->Input->get("id"));

		$arrFiles        = deserialize($objBranch->allFiles);
		$arrIgnoredFiles = deserialize($objBranch->ignoredFiles);

		$strDestinationDir = $objBranch->installPath . "/" . $objBranch->installDir . "/";

		$arrImportOutput = array();
		$arrImportCheck  = array();
		$arrIgnoreOutput = array();

		ksort($arrFiles);

		if (is_array($arrIgnoredFiles))
			ksort($arrIgnoredFiles);
		else
		{
			$arrIgnoredFiles = array();
		}


		$bDatabaseSQLExists = false;
		foreach ($arrFiles as $key=>$value)
		{

			$strDestFile = $strDestinationDir.$key;

			$strDestFile = str_replace("/TL_ROOT", "", $strDestFile);
			$strDestFile = str_replace("//", "/", $strDestFile);


			if (in_array($key, $arrIgnoredFiles))
				$arrIgnoreOutput[] = sprintf("<li>%s</li>", $strDestFile);
			else
			{
				$strClass = "";
				if (file_exists(TL_ROOT . $strDestFile))
				{
					$strClass = "(<strong>existing</strong>)";
				}
				$arrImportOutput[$value . '____' . $strDestFile] = sprintf("%s %s", $strDestFile, $strClass);

				if (strlen($strClass) == 0)
					$arrImportCheck[] = $value . '____' . $strDestFile;


				if ((!$bDatabaseSQLExists) && (basename($strDestFile) == "database.sql"))
					$bDatabaseSQLExists = true;
			}
		}


		$strIgnoreOutput = "<ul>" . implode("", $arrIgnoreOutput) . "</ul>";

		$GLOBALS['TL_DCA']['tl_rep_git_client_restore']['fields']['importFiles']['options'] = $arrImportOutput;
		$dc->setData("ignoreFiles", $strIgnoreOutput);
		$dc->setData("importFiles", $arrImportCheck);

		$dc->setData("databaseUpdate", $bDatabaseSQLExists);

	}


	/**
	 * Execute the onsubmit callback
	 * @param  DataContainer $dc The DataContainer object
	 */
	public function onsubmit_callback(DataContainer $dc)
	{
		if ($this->Input->post("submit_startImport"))
		{
			include (TL_ROOT . "/plugins/github/Api.php");
			include (TL_ROOT . "/plugins/github/ApiInterface.php");
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
			$objGithub = new Github_Client();


			$objBranch = new libContaoConnector("tl_rep_git_client_projects", "id", $this->Input->get("id"));
			$objConfig = new libContaoConnector("tl_rep_git_client", "id", $objBranch->pid);

			$strDestinationPath = trim($objBranch->installPath . '/' . $objBranch->installDir, '/') . '/';

			$arrFiles = $dc->getData("importFiles");

			if ((is_array($arrFiles)) && (count($arrFiles) > 0))
			{
				$objZipFile = new RequestExtended();

				$strRequestFilename = sprintf("https://github.com/%s/%s/zipball/%s", $objConfig->repUser, $objConfig->repRepository, $objBranch->repBranch);
				$objZipFile->send($strRequestFilename);

				if (!$objZipFile->hasError())
				{
					$arrFileName = $objZipFile->headers['Content-Disposition'];
					list($preStuff, $storeFileName) = explode("filename=", $arrFileName);

					$strFilename = sprintf("tl_files/%s", $storeFileName);

					$strZipName  = basename($storeFileName, '.zip');

					$objSaveFile = new File($strFilename);
					$objSaveFile->write($objZipFile->response);
					$objSaveFile->close();

					$objArchive      = new ZipReader($strFilename);
					$arrArchiveFiles = $objArchive->getFileList();
					$arrFirstDir     = explode("/", $arrArchiveFiles[0]);
					$strFristDir     = $arrFirstDir[0];

					foreach ($arrFiles as $filePart)
					{
						list($hashKey,$file) = explode("____", $filePart);

						$file = trim($file, "/");

						$strArchiveFilename = $strFristDir . '/' . str_replace($strDestinationPath, "/", $file);
						$strArchiveFilename = str_replace("//", "/", $strArchiveFilename);

						if ($objArchive->getFile($strArchiveFilename))
						{
							$objFile = new File($file);
							$objFile->write($objArchive->unzip());
							$objFile->close();
						}
					}
				}
			}

			$arrSession = $this->Session->getData("referer");

			if ($dc->getData("databaseUpdate"))
			{
				$this->redirect("main.php?do=repository_manager&update=database");
			}
			else
			{
				$this->redirect($arrSession['referer']['last']);
			}
		}
	}


	/**
	 * [showAll description]
	 * @param  DataContainer $dc The DataContainer object
	 * @param  string $strReturn The return string
	 * @return string            The return string
	 */
	public function showAll($dc, $strReturn)
	{
		return $strReturn.$dc->edit();
	}
}