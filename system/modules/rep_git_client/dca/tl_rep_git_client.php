<?php
	if (!defined('TL_ROOT'))
		die('You can not access this file directly!');

	$GLOBALS['TL_DCA']['tl_rep_git_client'] = array(

		// Config
			'config' => array(
					'dataContainer' => 'Table',
					'enableVersioning' => true,
					'switchToEdit' => true,
					'ctable' => array('tl_rep_git_client_projects'),
					'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client']['title'],
					'onsubmit_callback' => array(
							array('tl_rep_git_client','receiveBranches'
								))
			),

		// List
			'list' => array(
					'sorting' => array(
							'mode' => 1,
							'fields' => array(
									
									'repUser'
							),
							'flag' => 1,
							'panelLayout' => 'search,sort,filter,limit ',
					),
					'label' => array(
							'fields' => array(
									'repUser',
									'repRepository'
							),
							'format' => '%s :: %s'
					),
					'global_operations' => array('all' => array(
								'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
								'href' => 'act=select',
								'class' => 'header_edit_all',
								'attributes' => 'onclick="Backend.getScrollOffset();"'
						)),
					'operations' => array(
							'edit' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client']['edit'],
									'href' => 'act=edit',
									'icon' => 'edit.gif',
							),
							'edit_installs' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client']['edit_installs'],
									'href' => 'table=tl_rep_git_client_projects',
									'icon' => 'header.gif',
							),
							'delete' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client']['delete'],
									'href' => 'act=delete',
									'icon' => 'delete.gif',
									'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
							),
							'show' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client']['show'],
									'href' => 'act=show',
									'icon' => 'show.gif'
							),
					)
			),

		// Palettes
			'palettes' => array('default' => '{title_legend},repUser,repRepository'),

		// Fields
			'fields' => array(

					'repUser' => array(
							'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client']['repUser'],
							'exclude' => true,
							'inputType' => 'text',
							'eval' => array('maxlength' => 255),
							'save_callback' => array
							(
								array('tl_rep_git_client', 'importUserRepos')
							)
					),
					
					'repRepository' => array(
							'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client']['repRepository'],
							'exclude' => true,
							'inputType' => 'select',
							'options_callback' => array('tl_rep_git_client','getUserRepos'),
							'eval' => array('mandatory' => true),
					),
			)
	);

	class tl_rep_git_client extends Backend
	{
		
		public  function __construct()
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
			$this->objGithub = new Github_Client();
			$this->import("Database");
		}
	
		public function importUserRepos($varValue,DataContainer $dc)
		{
			
			return $varValue;
		}

		public function getUserRepos(DataContainer $dc)
		{
			$arrValues = array();
			
			
			if ($dc->activeRecord->repUser)
			{
				
				try
				{
					$arrRepos = $this->objGithub->getRepoApi()->getUserRepos($dc->activeRecord->repUser);
					
					foreach ($arrRepos as $repo)
					{
						$arrValues[$repo['name']] = $repo['name']; 
					
					}
				}
				
				catch (Exception $e) 
				{
				}
				
				
			}
			
			return $arrValues;
		}

		
		public function receiveBranches(DataContainer $dc)
		{
			if ( $dc->activeRecord->repRepository)
			{
				$arrTags = $this->objGithub->getRepoApi()->getRepoTags($dc->activeRecord->repUser, $dc->activeRecord->repRepository);
				$arrBranches = $this->objGithub->getRepoApi()->getRepoBranches($dc->activeRecord->repUser, $dc->activeRecord->repRepository);
				$arrRepos = $this->objGithub->getRepoApi()->getUserRepos($dc->activeRecord->repUser);
				
				$arrMyRepo = array();
				foreach ($arrRepos as $repo)
				{
					if ($repo['name']==$dc->activeRecord->repRepository)
					{
						$arrMyRepo = $repo;
					}
				}
				
				if ((is_array($arrBranches)) && (count($arrBranches)>0))
				{
					foreach ($arrBranches as $key=>$value)
					{
						$tree = $this->objGithub->getObjectApi()->showTree($dc->activeRecord->repUser, $dc->activeRecord->repRepository, $value);
						$blobs = $this->objGithub->getObjectApi()->listBlobs($dc->activeRecord->repUser, $dc->activeRecord->repRepository, $value);
					
					
						$objBranch = new libContaoConnector("tl_rep_git_client_projects","repHash",$value);
						$objBranch->pid=$dc->id;
						$objBranch->repUrl = $arrMyRepo['url'];
						$objBranch->repPushed = $arrMyRepo['pushed_at'];
						$objBranch->repBranch = $key;
						$objBranch->repHash = $value;
						$objBranch->allFiles = $blobs;
						$objBranch->ignoredFiles = array();
						$objBranch->repType = 'BRANCH';
						
						$objBranch->Sync();
						
					}
				}
				
				
				if ((is_array($arrTags)) && (count($arrTags)>0))
				{
					foreach ($arrTags as $key=>$value)
					{
						$tree = $this->objGithub->getObjectApi()->showTree($dc->activeRecord->repUser, $dc->activeRecord->repRepository, $value);
						$blobs = $this->objGithub->getObjectApi()->listBlobs($dc->activeRecord->repUser, $dc->activeRecord->repRepository, $value);
					
					
						$objBranch = new libContaoConnector("tl_rep_git_client_projects","repHash",$value);
						$objBranch->pid=$dc->id;
						$objBranch->repUrl = $arrMyRepo['url'];
						$objBranch->repPushed = $arrMyRepo['pushed_at'];
						$objBranch->repBranch = $key;
						$objBranch->repHash = $value;
						$objBranch->allFiles = $blobs;
						$objBranch->ignoredFiles = array();
						$objBranch->repType = 'TAGS';
						
						$objBranch->Sync();
						
					}
				}
			}
		}
	}
?>