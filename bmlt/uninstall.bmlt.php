<?php
/**
	\file uninstallbmlt.php
	
	\brief Uninstaller file for the BMLT plugins (so they are uninstalled with the component).
	
	This uninstall file uses inspiration and code snippets from the
	dev.anything-digital.com JCalPro package.

	$File: uninstallbmlt.php - Uninstall file$
    
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
jimport ( 'joomla.filesystem.file' );

function DeletePlugin ( $pluginName, $folder )
{
	$db = & JFactory::getDBO();

	// read plugin param from db
    if( version_compare( JVERSION, '1.6.0', 'ge' ) )
        {
	    $sql = 'SELECT * FROM `#__extensions` WHERE `element`= \''.$pluginName.'\';';
	    }
	else
	    {
	    $sql = 'SELECT * FROM `#__plugins` WHERE `element`= \''.$pluginName.'\';';
	    }
	
	$db->setQuery($sql);
	$result = $db->loadAssocList();
    
	if ( !empty ( $result ) )
		{
        $do_delete = true;
        
		// now remove plugin details from db
        if( version_compare( JVERSION, '1.6.0', 'ge' ) )
            {
		    $db->setQuery ( "DELETE FROM `#__extensions` WHERE `element`= '" . $pluginName . "';" );
            }
        else
            {
		    $db->setQuery ( "DELETE FROM `#__plugins` WHERE `element`= '" . $pluginName . "';" );
            }
        
		if ( !$db->query() )
		    {
		    $err_msg = $db->getErrorMsg();
		    $do_delete = false;
            JError::RaiseWarning( 500, JText::_( 'Database Error: '.$err_msg));
		    }

        // delete the plugin files
        $extra_dir = version_compare ( JVERSION, '1.6.0', 'ge' ) ? DS . $pluginName : '';
        $basePath = JPATH_ROOT . DS . 'plugins' . DS . $folder . $extra_dir;
        if ( $do_delete && $folder != '' && JFile::exists( $basePath . DS . $pluginName.'.php' ) )
            {
            JFile::delete ( array( $basePath . DS . $pluginName.'.php', $basePath . DS . $pluginName.'.xml' ) );
            JFolder::delete ( $basePath );
            }
		}
	else
	    {
        JError::RaiseWarning( 500, JText::_( 'No plugin to delete for '.$pluginName));
	    }
}

function com_uninstall()
{
	DeletePlugin ( 'bmltcontent', 'content' );
	DeletePlugin ( 'bmltsystem', 'system' );
}
?>