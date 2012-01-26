<?php
	if (!defined('TL_ROOT'))
		die('You can not access this file directly!');

	$GLOBALS['TL_DCA']['tl_rep_git_client_projects'] = array(

		// Config
			'config' => array(
					'dataContainer' => 'Table',
					'ptable' => 'tl_rep_git_client',
		'closed'                      => true,
					'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['title'],
					'onsubmit_callback' => array(
								)
			),

		// List
			'list' => array(
					'sorting' => array(
							'mode' => 4,
							'fields' => array(									
									'repBranch'
							),
							'flag' => 1,
							'panelLayout' => 'search,sort,filter,limit ',
							'headerFields'			=> array('repUser', 'repRepository'),
							'child_record_callback'	=> array('tl_rep_git_client_projects', 'listField')
					),
					'label' => array(
							'fields' => array(
									'repBranch',
									'installPath',
									'installDir',
							),
							'format' => '%s<br>Installpath : %s/%s'
					),
					'global_operations' => array('all' => array(
								'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
								'href' => 'act=select',
								'class' => 'header_edit_all',
								'attributes' => 'onclick="Backend.getScrollOffset();"'
						)),
					'operations' => array(
							'edit' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['edit'],
									'href' => 'act=edit',
									'icon' => 'edit.gif'
							),
							'import' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['import'],
									'href' => 'table=tl_git_client_restore',
									'icon' => 'theme_import.gif'
							),
							'show' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['show'],
									'href' => 'act=show',
									'icon' => 'show.gif'
							),
					)
			),

		// Palettes
			'palettes' => array('default' => '{title_legend},repRepository,installPath,installDir,ignoredFiles'),

		// Fields
			'fields' => array(
					'repRepository' => array(
							'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client']['repRepository'],
							'exclude' => true,
							'inputType' => 'text',
							'eval' => array('readonly'=> 'readonly')
					),
					'installPath' => array(
							'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installPath'],
							'exclude' => true,
							'inputType'				=> 'fileTree',
							'eval'					=> array('fieldType'=>'radio', 'tl_class'=>'clr','path'=>'/')

							
					),
					'installDir' => array(
							'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installDir'],
							'exclude' => true,
							'inputType'				=> 'text',

							
					),
					'ignoredFiles' => array(
							'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['ignoredFiles'],
							'exclude' => true,
							'inputType'	=> 'checkbox',
							'options_callback' => array('tl_rep_git_client_projects','getFileList'),
							'eval'		=> array('multiple'=>true)

							
					),
			)
	);

	class tl_rep_git_client_projects extends Backend
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
	
		public function getFileList(DataContainer $dc)
		{
			$arrFiles = deserialize($dc->activeRecord->allFiles);
			
			$arrIgnoreFiles = array();
			if (is_array($arrFiles ))
			{
				foreach ($arrFiles as $key=>$value)
				{
					$arrIgnoreFiles[$value] = $key;
				}
			}
			
			sort($arrIgnoreFiles);
			return $arrIgnoreFiles;
			
		}
		
		public function listField($arrRow)
		{
		
			return '
<h2>' . $arrRow['repBranch'] . '</h2><br>
Install : TL_ROOT' . $arrRow['installPath'] . '/' . $arrRow['installDir'] . '
' . "\n";
		}
	}
?>