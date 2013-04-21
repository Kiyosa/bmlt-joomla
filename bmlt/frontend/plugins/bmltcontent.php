<?php
/**
 * @package		Joomla
 * @subpackage	Content
 * @version		3.0.7
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * \license Unfortunately, Joomla won't let you put an extension in their directory unless you make it GPL (ick). Because they own the playing field, I need to play by their rules. However, the huge bulk of the BMLT project is totally open, and available at http://magshare.org/bmlt
 * \file com_bmlt/bmltcontent.php
 * \brief This is an inline content plugin for the BMLT.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
$extra_dir = version_compare ( JVERSION, '1.6.0', 'ge' ) ? '../' : '';
$dir = dirname ( __FILE__ )."/../$extra_dir../components/com_bmlt/bmlt-joomla-satellite-plugin.php";

if ( file_exists ( $dir ) ) // This prevents the plugin from scragging the system in case of a pooched installation
    {
    require_once ( $dir );
    
    jimport( 'joomla.plugin.plugin' );
    
    /**
     * \brief This is a plugin for the BMLT satellite that will allow the BMLT to display inline in text.
     *
     * @package		Joomla
     * @subpackage	Content
     * @since 		1.5
     */
    class plgcontentBMLTcontent extends JPlugin
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
        function plgcontentBMLTcontent( &$subject,	///< The object to observe
                                        $params			///< The object that holds the plugin parameters
                                        )
            {
            parent::__construct( $subject, $params );
            }
        
        /**
         * \brief Prepare content method
         *	This will replace any instance of <!--BMLT--> with the "classic" interactive BMLT, and any instance of the more complex <!--BMLT_SIMPLE(***)--> (How's that for
         *	an oxymoron?) with the inline table/block element version. There can only be one "classic" BMLT, but multiple tables.
         *
         *  The "onContentPrepare" is because J1.6, in their wisdom, renamed the darn function.
         *
         * Method is called by the view
         *
         */
        function onContentPrepare(	$context,   ///< Added by J1.6
                                    &$article,	///< The article object.  Note $article->text is also available
                                    &$params,	///< The article params
                                    $limitstart	///< The 'page' number
                                )
            {
            return $this->onPrepareContent ( $article, $params, $limitstart );
            }
        
        
        /**
         * \brief Prepare content method
         *	This will replace any instance of <!--BMLT--> with the "classic" interactive BMLT, and any instance of the more complex <!--BMLT_SIMPLE(***)--> (How's that for
         *	an oxymoron?) with the inline table/block element version. There can only be one "classic" BMLT, but multiple tables.
         *
         * Method is called by the view
         *
         */
        function onPrepareContent(	&$article,	///< The article object.  Note $article->text is also available
                                    &$params,	///< The article params
                                    $limitstart	///< The 'page' number
                                )
            {
            global $BMLTPluginOp;
            $ret = null;
            
            if ( $BMLTPluginOp instanceof BMLTJoomlaPlugin )
                {
                if ( BMLTJoomlaPlugin::get_shortcode ( $article->text, 'bmlt') || BMLTJoomlaPlugin::get_shortcode ( $article->text, 'bmlt_simple') || BMLTJoomlaPlugin::get_shortcode ( $article->text, 'bmlt_changes') || BMLTJoomlaPlugin::get_shortcode ( $article->text, 'bmlt_map') )
                    {
                    $BMLTPluginOp->standard_head($article->text);
                    $article->text = $BMLTPluginOp->content_filter ( $article->text );
                    }
                }
            
            return $ret;
            }
        };
    }
