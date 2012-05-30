<?php
/**
	\file installbmlt.php
	
	\brief Installer file for the BMLT plugins (so they are installed with the component).
	
	This install file uses inspiration and code snippets from the
	dev.anything-digital.com JCalPro package.

	$File: installbmlt.php - Install file$
    
    This file is part of the Basic Meeting List Toolbox (BMLT).
    
    Find out more at: http://magshare.org/bmlt
    
    BMLT is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    BMLT is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this code.  If not, see <http://www.gnu.org/licenses/>.
*/
defined( '_JEXEC' ) or die( 'Uh. Uh. Uh. You didn\'t say the magic word!' );
jimport('joomla.filesystem.file');

/**
	\brief	Move a plugin file from a given install location to one of the proper plugin directories, and activate it in the DB.
	
	\returns a Boolean. True if the operation succeeded.
*/
function AddPlugin ( $basePath,	///< The filesystem path to the directory containing the file to be moved.
					 $shConfig,
					 $files
					)
{
	// move the files to target location
	$result = array();
	$success = true;

	// check data
	if ( empty ( $files ) )
		{
		return false;
		}

    $extra_dir = version_compare ( JVERSION, '1.6.0', 'ge' ) ? DS.$shConfig['element'] : '';
    $dest_dir = JPATH_ROOT.DS.'plugins'.DS.$shConfig['folder'].$extra_dir;

    if ( !JFolder::exists ( $dest_dir ) )
        {
        if ( !JFolder::create ( $dest_dir ) )
            {
            $success = false;
            JError::RaiseWarning( 500, JText::_( 'Could not create '.$dest_dir.'!'));
            }
        }
    
    if ( $success )
        {
        if ( JFolder::exists ( $dest_dir ) )
            {
            foreach( $files as $pluginFile)
                {
                $src = $basePath.DS.$pluginFile;
                $dest = $dest_dir.DS.$pluginFile;
                
                if ( !JFile::exists ( $src ) )
                    {
                    $success = false;
                    $this_success = false;
                    JError::RaiseWarning( 500, JText::_( $src.' does not exist!'));
                    }
                else
                    {
                    $this_success = (true === JFile::move ( $src, $dest ));
                    
                    $success = $success && $this_success;
                    
                    if ( !$this_success )
                        {
                        JError::RaiseWarning( 500, JText::_( 'Could not move '.$src.' to '.$dest.'!'));
                        }
                    else
                        {
                        echo ( '<span style="color:#090;font-style:italic">&nbsp;&nbsp;&nbsp;Installed '.$pluginFile.'</span><br />' );
                        }
                    }
                
                $result[$pluginFile] = $this_success;
                }
            }
        else
            {
            $success = false;
            JError::RaiseWarning( 500, JText::_( 'Could not create '.$dest_dir.'!'));
            }
        }
    
	// if files moved to destination, setup plugin in Joomla database
	if ($success)
		{
		// insert elements in db
		$db = &JFactory::getDBO();
        if( version_compare( JVERSION, '1.6.0', 'ge' ) )
            {
            $sql="INSERT INTO `#__extensions` ( `name`, `type`,`element`, `folder`, `access`, `ordering`, `enabled`,"
            . " `client_id`, `checked_out`, `checked_out_time`, `params`)"
            . " VALUES ('{$shConfig['name']}', 'plugin', '{$shConfig['element']}', '{$shConfig['folder']}', '{$shConfig['access']}', '{$shConfig['ordering']}',"
            . " '{$shConfig['published']}', '{$shConfig['client_id']}', '{$shConfig['checked_out']}',"
            . " '{$shConfig['checked_out_time']}', '{$shConfig['params']}');";
            }
        elseif( version_compare( JVERSION, '1.5.0', 'ge' ) )
            {
            $sql="INSERT INTO `#__plugins` ( `name`, `element`, `folder`, `access`, `ordering`, `published`,"
            . " `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)"
            . " VALUES ('{$shConfig['name']}', '{$shConfig['element']}', '{$shConfig['folder']}', '{$shConfig['access']}', '{$shConfig['ordering']}',"
            . " '{$shConfig['published']}', '{$shConfig['iscore']}', '{$shConfig['client_id']}', '{$shConfig['checked_out']}',"
            . " '{$shConfig['checked_out_time']}', '{$shConfig['params']}');";
            }
        elseif( version_compare( JVERSION, '2.5.0', 'ge' ) )
            {
            $sql="INSERT INTO `#__extensions` ( `name`, `type`, `element`, `folder`, `access`, `ordering`, `published`,"
            . " `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)"
            . " VALUES ('{$shConfig['name']}', 'plugin', '{$shConfig['element']}', '{$shConfig['folder']}', '{$shConfig['access']}', '{$shConfig['ordering']}',"
            . " '{$shConfig['published']}', '{$shConfig['iscore']}', '{$shConfig['client_id']}', '{$shConfig['checked_out']}',"
            . " '{$shConfig['checked_out_time']}', '{$shConfig['params']}');";
            }

		$db->setQuery( $sql);
		if ( !$db->query() )
		    {
		    $err_msg = $db->getErrorMsg();
            JError::RaiseWarning( 500, JText::_( 'Database Error: '.$err_msg));
		    }
		}
	else
		{
		// don't leave anything behind
		foreach ( $files as $pluginFile )
			{
			if ($result[$pluginFile]) 
				{
				// if file was copied, try to delete it
                $extra_dir = version_compare ( JVERSION, '1.6.0', 'ge' ) ? DS.$shConfig['element'] : '';
                
                $dest = $dest_dir.DS.$pluginFile;

				JFile::delete( $dest );
				}
			}
		
		JFolder::delete( $dest_dir );
		JError::RaiseWarning( 500, JText::_( 'Could not install plugins in '.$dest_dir.'!'));
		}
    
	return $success;
}

function InstallSystemPlugin()
{
		$shConfig = array('name'=>'System - BMLT', 'element' => 'bmltsystem', 'folder'=>'system',
			'access'=>(version_compare( JVERSION, '1.6.0', 'ge' )?1:0), 'ordering'=>-100, 'published' => 1, 'iscore' => 0, 'client_id' => 0, 'checked_out' => 0, 
			'checked_out_time' => '0000-00-00 00:00:00',	'params'=>'');
		
		return AddPlugin ( JPATH_ROOT . DS .'components' . DS.'com_bmlt'. DS.'plugins', $shConfig, array( 'bmltsystem.php', 'bmltsystem.xml') );
}

function InstallContentPlugin()
{
		$shConfig = array('name'=>'Content - BMLT', 'element' => 'bmltcontent', 'folder'=>'content',
			'access'=>(version_compare( JVERSION, '1.6.0', 'ge' )?1:0), 'ordering'=>10, 'published' => 1, 'iscore' => 0, 'client_id' => 0, 'checked_out' => 0, 
			'checked_out_time' => '0000-00-00 00:00:00',	'params'=>'');
		
		return AddPlugin ( JPATH_ROOT . DS .'components'. DS.'com_bmlt'. DS.'plugins', $shConfig, array( 'bmltcontent.php', 'bmltcontent.xml') );
}

function com_install()
{
	if ( version_compare (PHP_VERSION,'5.0.0','>=') )
		{
		if ( InstallSystemPlugin() )
			{
			echo ( '<span style="color:#090;font-weight:bold">Successfully installed the BMLT system plugin</span><br />' );
			if ( InstallContentPlugin() )
				{
                if( version_compare( JVERSION, '2.5.0', 'l' ) )
                    {
		            JFolder::delete ( JPATH_ROOT . DS .'components' . DS.'com_bmlt'. DS.'plugins' );
		            JFile::delete ( JPATH_ADMINISTRATOR . DS .'components' . DS.'com_bmlt'. DS.'install.sql' );
		            }
				echo ( '<span style="color:#090;font-weight:bold">Successfully installed the BMLT content plugin.</span>' );
				JFile::delete(__FILE__);
				}
			else
				{
				echo ( '<span style="color:red;font-weight:bold"> The content plugin install failed. Make sure that you uninstall the BMLT component.</span>' );
				}
			}
		else
			{
			echo ( '<span style="color:red;font-weight:bold"> The system and the content plugin install failed. Make sure that you uninstall the BMLT component.</span>' );
			}
		}
	else
		{
		echo ( '<h2 style="color:red">WARNING: The plugin install failed. You should make sure that you uninstall the BMLT component, or it could cause instability in your site. The BMLT requires PHP 5 or above, and you have PHP '.htmlspecialchars ( PHP_VERSION ).'.</h2>' );
		}
}
?>