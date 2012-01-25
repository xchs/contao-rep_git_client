<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * System configuration
 */
$GLOBALS['TL_DCA']['tl_rep_git_client_restore'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Memory',
		'closed'                      => true,
		'onload_callback'		=> array(
			array('tl_rep_git_client_restore','onload_callback'),
		),
		'onsubmit_callback'		=> array(
			array('tl_rep_git_client_restore','onsubmit_callback'),
		),
		'disableSubmit'			=> true,
		
		'dcMemory_show_callback' => array(
			array('tl_rep_git_client_restore','showAll')
			),
		'dcMemory_showAll_callback' => array(
			array('tl_rep_git_client_restore','showAll')
		),
	),
	
	// Palettes
	'palettes' => array
	(
		'__selector__'  	=> array(''),
		'default' 			=> 'importFiles,ignoreFiles,databaseUpdate,startImport',

	),

	'subpalettes' => array
	(
	),
	

	// Fields
	'fields' => array
	(
		'importFiles' => array
		(
			'label'             => &$GLOBALS['TL_LANG']['tl_rep_git_client_restore']['importFiles'],
			'inputType'    		=> 'checkbox',
			'eval'				=> array('multiple'	=> true)
		),  
		'ignoreFiles' => array
		(
			'label'             => &$GLOBALS['TL_LANG']['tl_rep_git_client_restore']['ignoreFiles'],
			'inputType'    		=> 'statictext',
			'eval'				=> array('readonly'	=> true)
		),
		  
		'startImport' => array
		(
			'label'             => &$GLOBALS['TL_LANG']['tl_rep_git_client_restore']['ignoreFiles'],
			'inputType'    		=> 'statictext',
			'addSubmit'		=> true,
			'eval'				=> array('readonly'	=> true)
		),
		'databaseUpdate' => array
		(
			'label'             => &$GLOBALS['TL_LANG']['tl_rep_git_client_restore']['databaseUpdate'],
			'inputType'    		=> 'checkbox',
			'eval'				=> array()
		),
	)
);



class tl_rep_git_client_restore extends Backend
{

	public function __construct()
	{
		$this->import("Config");
		$this->import("Input");
		$this->import("Database");
		$this->import("BackendUser","User");
	
		$this->import("Session");	
	}

	
	public function onload_callback(DataContainer $dc)
	{			
		$objBranch = new libContaoConnector("tl_rep_git_client_projects","id",$this->Input->get("id"));
		
		$arrFiles = deserialize($objBranch->allFiles);
		$arrIgnoredFiles = deserialize($objBranch->ignoredFiles);
		
		$strDestinationDir = $objBranch->installPath."/".$objBranch->installDir."/";
		
		$arrImportOutput = array();
		$arrImportCheck = array();
		$arrIgnoreOutput = array();
		
		ksort($arrFiles);
		
		if (is_array($arrIgnoredFiles))
			ksort($arrIgnoredFiles);
		else {
			$arrIgnoredFiles = array();
		}
		
		
		$bDatabaseSQLExists = false;
		
		foreach ($arrFiles as $key=>$value)
		{
			
			$strDestFile = $strDestinationDir.$key;			
			
			$strDestFile = str_replace("/TL_ROOT", "", $strDestFile);
			$strDestFile = str_replace("//", "/", $strDestFile);
		
			
			if (in_array($key,$arrIgnoredFiles))
				$arrIgnoreOutput[] = sprintf("<li>%s</li>",$strDestFile);
			else		
			{
				$strClass = "";
				if (file_exists(TL_ROOT.$strDestFile))
				{
					$strClass = "(<strong>existing</strong>)";
				}	
				$arrImportOutput[$value.'____'.$strDestFile] = sprintf("%s %s",$strDestFile,$strClass);
				
				if (strlen($strClass)==0)
					$arrImportCheck[] = $value.'____'.$strDestFile;
				
				if ((!$bDatabaseSQLExists) && (basename($strDestFile)=="database.sql"))
					$bDatabaseSQLExists=true;
			}
		}
		
		
		$strIgnoreOutput = "<ul>".implode("",$arrIgnoreOutput)."</ul>";
		
		$GLOBALS['TL_DCA']['tl_rep_git_client_restore']['fields']['importFiles']['options'] = $arrImportOutput;
		$dc->setData("ignoreFiles",$strIgnoreOutput);
		$dc->setData("importFiles",$arrImportCheck);
		$dc->setData("databaseUpdate",$bDatabaseSQLExists);
	
				
	}
	
	
	
	public function onsubmit_callback(DataContainer $dc)
	{
		if ($this->Input->post("submit_startImport"))
		{
			include (TL_ROOT."/plugins/Github/ApiInterface.php");
			include (TL_ROOT."/plugins/Github/Api.php");
			include (TL_ROOT."/plugins/Github/Api/Repo.php");
			include (TL_ROOT."/plugins/Github/Api/Object.php");
			include (TL_ROOT."/plugins/Github/Autoloader.php");
			include (TL_ROOT."/plugins/Github/Client.php");
			include (TL_ROOT."/plugins/Github/HttpClientInterface.php");
			include (TL_ROOT."/plugins/Github/HttpClient.php");
			include (TL_ROOT."/plugins/Github/HttpClient/Curl.php");
			include (TL_ROOT."/plugins/Github/HttpClient/Exception.php");
			
			
			// Do not use this autoloader. Will not works with Contao autoloader
			//Github_Autoloader::register();
			$objGithub = new Github_Client();
			
			$objBranch = new libContaoConnector("tl_rep_git_client_projects","id",$this->Input->get("id"));
			$objConfig = new libContaoConnector("tl_rep_git_client","id",$objBranch->pid);
		
			$arrFiles = $dc->getData("importFiles");
			
			if (is_array($arrFiles))
			{
				foreach ($arrFiles as $filePart)
				{
					list($hashKey,$file) = explode("____",$filePart);	
				
					$strContent = $objGithub->getObjectApi()->getRawData($objConfig->repUser, $objConfig->repRepository,$hashKey);
				
					$objFile = new File($file);
					$objFile->write($strContent);
					$objFile->close();
					
				}
			}

			$arrSession = $this->Session->getData("referer");
			
			if ($dc->getData("databaseUpdate"))
				$this->redirect("main.php?do=repository_manager&update=database");
			else
				$this->redirect($arrSession['referer']['last']);
		}
	}
	
	
	public function showAll($dc,$strReturn)
	{
		return $strReturn.$dc->edit();
	}
}

?>