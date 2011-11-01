<?php
/**
* @version		2.1.28 $
* @package		Joomla
* @subpackage	BMLT
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* \license Unfortunately, Joomla won't let you put an extension in their directory unless
* you make it GPL (ick). Because they own the playing field, I need to play by their
* rules. However, the huge bulk of the BMLT project is totally open, and available at
* http://magshare.org/bmlt
* \file com_bmlt/bmlt.php
* \brief This implements a simple Joomla! component that displays a BMLT meeting search.
* \version 2.1.28
    
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
//ini_set('display_errors', 1);	// Debug only.
//ini_set('error_reporting', E_ALL);
defined ( '_JEXEC' ) or die ( 'Cannot run this file directly' );

require_once ( dirname ( __FILE__ )."/bmlt-joomla-satellite-plugin.php" );

global $BMLTPluginOp;

if ( $BMLTPluginOp instanceof BMLTJoomlaPlugin )
    {
    $BMLTPluginOp->standard_head();
    echo $BMLTPluginOp->display_old_search('[[bmlt]]');
    }
?>