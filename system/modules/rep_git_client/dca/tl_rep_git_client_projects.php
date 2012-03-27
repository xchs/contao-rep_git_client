<?php
if (!defined('TL_ROOT'))
	die('You can not access this file directly!');

$GLOBALS['TL_DCA']['tl_rep_git_client_projects'] = array(

	// Config
		'config' => array(
				'dataContainer' => 'Table',
				'ptable' => 'tl_rep_git_client',
				'closed' => true,
				'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['title'],
		),

	// List
		'list' => array(
				'sorting' => array(
						'mode' => 4,
						'fields' => array('repBranch'),
						'flag' => 1,
						'panelLayout' => 'search,sort,filter,limit ',
						'headerFields' => array(
								'repUser',
								'repRepository',
								'repPushed'
						),
						'child_record_callback' => array(
								'tl_rep_git_client_projects',
								'listField'
						)
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
						'delete' => array(
									'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['delete'],
									'href' => 'act=delete',
									'icon' => 'delete.gif',
									'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
						),
						'import' => array(
								'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['import'],
								'href' => 'table=tl_rep_git_client_restore',
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
						'eval' => array('readonly' => 'readonly'),
						'load_callback' => array( array(
									"tl_rep_git_client_projects",
									"loadRepositoryName"
							))
				),
				'installPath' => array(
						'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installPath'],
						'exclude' => true,
						'inputType' => 'fileTree',
						'eval' => array(
								'fieldType' => 'radio',
								'tl_class' => 'clr',
								'path' => '/'
						)
				),
				'installDir' => array(
						'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['installDir'],
						'exclude' => true,
						'inputType' => 'text',
				),
				'ignoredFiles' => array(
						'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['ignoredFiles'],
						'exclude' => true,
						'inputType' => 'checkbox',
						'options_callback' => array(
								'tl_rep_git_client_projects',
								'getFileList'
						),
						'eval' => array('multiple' => true)
				),
				'repType' => array(
						'label' => &$GLOBALS['TL_LANG']['tl_rep_git_client_projects']['repType'],
						'filter' => true,
						'inputType' => 'text',
				),
		)
);

class tl_rep_git_client_projects extends Backend
{

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

		// Do not use this autoloader. Will not works with Contao autoloader
		//Github_Autoloader::register();
		$this -> objGithub = new Github_Client();

		$this -> import("Database");
		$this -> import("Input");
	}

	public function getFileList(DataContainer $dc)
	{
		$arrFiles = deserialize($dc -> activeRecord -> allFiles);

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

	public function listField($arrRow)
	{

		return '
<strong>' . $arrRow['repBranch'] . '</strong><br>
' . $arrRow['repType'] . '<br>
Install : TL_ROOT' . $arrRow['installPath'] . '/' . $arrRow['installDir'] . '
' . "\n";
	}

	public function loadRepositoryName()
	{
		$objBranch = new libContaoConnector("tl_rep_git_client_projects", "id", $this -> Input -> get("id"));
		$objConfig = new libContaoConnector("tl_rep_git_client", "id", $objBranch -> pid);

		return $objConfig -> repRepository;

	}

}
?>