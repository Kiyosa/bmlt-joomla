<?php
/**
 * \brief This is a plugin for the BMLT satellite that will respond to BMLT AJAX calls.
 *
 * @package		Joomla
 * @subpackage	System
 * @version		2.2.3
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * \license Unfortunately, Joomla won't let you put an extension in their directory unless
 * you make it GPL (ick). Because they own the playing field, I need to play by their
 * rules. However, the huge bulk of the BMLT project is totally open, and available at
 * http://magshare.org/bmlt
 * \file com_bmlt/bmltsystem.php
 * \brief This handles the AJAX callbacks for the BMLT plugin and component.
 */
defined ( '_JEXEC' ) or die ( 'Cannot run this file directly' );

$extra_dir = version_compare ( JVERSION, '1.6.0', 'ge' ) ? '../' : '';
$dir = dirname ( __FILE__ )."/../$extra_dir../components/com_bmlt/bmlt-joomla-satellite-plugin.php";

if ( file_exists ( $dir ) ) // This prevents the plugin from scragging the system in case of a pooched installation
    {
    require_once ( $dir );
    
    jimport('joomla.application.plugin');
    
    /**
        \brief	This is the class for the system plugin for the Joomla implementation of the BMLT satellite.
                It needs to be a system plugin, so it gets the onAfterRoute() callback.
                This works in concert with the component, content plugin and admin component.
    */
    class plgsystemBMLTSystem extends JPlugin
        {
        /**
         * \brief Constructor
         *
         * For php4 compatability we must not use the __constructor as a constructor for plugins
         * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
         * This causes problems with cross-referencing necessary for the observer design pattern.
         *
         * @since 1.5
         */
        function plgsystemBMLTSystem( &$subject,	///< The object to observe
                                        $params		///< The object that holds the plugin parameters
                                        )
            {
            parent::__construct( $subject, $params );
            }
    
        /**
         * \brief This intercepts AJAX callbacks from the BMLT, and drops out of Joomla to execute them.
         *		  The important thing here, is that it can access the options database before scragging
         *		  the execution and sending execution to the root server.
         */
        public function onAfterInitialise()
            {
            global $BMLTPluginOp;
            
            if ( $BMLTPluginOp instanceof BMLTJoomlaPlugin )
                {
                $BMLTPluginOp->admin_ajax_handler();
                $BMLTPluginOp->ajax_router();
                }
            }
        };
    }
