-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************


-- --------------------------------------------------------

--
-- Table `tl_rep_git_client`
--

CREATE TABLE `tl_rep_git_client` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `repUser` varchar(255) NOT NULL default '',
  `repRepository` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table `tl_rep_git_client_projects`
--

CREATE TABLE `tl_rep_git_client_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `repType` varchar(255) NOT NULL default '',
  `repPushed` varchar(255) NOT NULL default '',
  `repUrl` varchar(255) NOT NULL default '',
  `repBranch` varchar(255) NOT NULL default '',
  `repHash` varchar(255) NOT NULL default '',
  `installPath` varchar(255) NOT NULL default '',
  `installDir` varchar(255) NOT NULL default '',
  `ignoredFiles` longtext NULL,
  `allFiles` longtext NULL,
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;