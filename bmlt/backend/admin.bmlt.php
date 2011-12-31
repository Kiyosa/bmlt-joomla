<?php
/**
* @version		2.2 $
* @package		Joomla
* @subpackage	BMLT
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*	\file com_bmlt/bmlt.php
*	\brief This implements a simple Joomla! component that displays a BMLT meeting search.	
*	\license Unfortunately, Joomla won't let you put an extension in their directory unless you make it GPL (ick). Because they own the playing field, I need to play by their rules. However, the huge bulk of the BMLT project is totally open, and available at http://magshare.org/bmlt
*	\version 2.1.28
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
ini_set('display_errors', 1);	// Debug only.
ini_set('error_reporting', E_ALL);
require_once ( dirname ( __FILE__ )."/../../../components/com_bmlt/bmlt-joomla-satellite-plugin.php" );

global $BMLTPluginOp;

if ( $BMLTPluginOp instanceof BMLTJoomlaPlugin )
    {
    $BMLTPluginOp->admin_page (  );
    }
?>