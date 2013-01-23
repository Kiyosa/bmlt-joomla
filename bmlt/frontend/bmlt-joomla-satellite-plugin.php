<?php
/****************************************************************************************//**
*   \file   bmlt-joomla-satellite-plugin.php                                                *
*                                                                                           *
*   \brief  This is a Joomla plugin of a BMLT satellite client.                             *
*   \version 2.2.2                                                                            *
*   @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*   \license Unfortunately, Joomla won't let you put an extension in their directory unless *
*   you make it GPL (ick). Because they own the playing field, I need to play by their      *
*   rules. However, the huge bulk of the BMLT project is totally open, and available at     *
*   http://magshare.org/bmlt                                                                *
    
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
********************************************************************************************/

// define ( '_DEBUG_MODE_', 1 ); //Uncomment for easier JavaScript debugging.

// Include the satellite driver class.

if ( file_exists ( dirname ( __FILE__ ).'/BMLT-Satellite-Base-Class/bmlt-cms-satellite-plugin.php' ) )
    {
    require_once ( dirname ( __FILE__ ).'/BMLT-Satellite-Base-Class/bmlt-cms-satellite-plugin.php' );
    
    JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bmlt'.DS.'tables');
    
    class TableBMLT_Settings extends JTable
    {
        var $id = 1;                    ///< The ID of the settings.
        var	$bmlt_data;				    ///< We just cram everything into a serialized bouillabaisse.
        
        function __construct ( &$db )
            {
            parent::__construct ( '#__bmlt_settings', 'id', $db );
            }
    };
    
    /****************************************************************************************//**
    *   \class BMLTWPPlugin                                                                     *
    *                                                                                           *
    *   \brief This is the class that implements and encapsulates the plugin functionality.     *
    *   A single instance of this is created, and manages the plugin.                           *
    *                                                                                           *
    *   This plugin registers errors by echoing HTML comments, so look at the source code of    *
    *   the page if things aren't working right.                                                *
    ********************************************************************************************/
    
    class BMLTJoomlaPlugin extends BMLTPlugin
    {
        /************************************************************************************//**
        *   \brief Constructor.                                                                 *
        ****************************************************************************************/
        function __construct ()
            {
            parent::__construct ();
            }
        
        /************************************************************************************//**
        *   \brief Return an HTTP path to the AJAX callback target.                             *
        *                                                                                       *
        *   \returns a string, containing the path.                                             *
        ****************************************************************************************/
        protected function get_admin_ajax_base_uri()
            {
            return htmlspecialchars ( $this->get_ajax_base_uri().'?option=com_bmlt' );
            }
        
        /************************************************************************************//**
        *   \brief Return an HTTP path to the basic admin form submit (action) URI              *
        *                                                                                       *
        *   \returns a string, containing the path.                                             *
        ****************************************************************************************/
        protected function get_admin_form_uri()
            {
            return htmlspecialchars ( $_SERVER['PHP_SELF'].'?option=com_bmlt' );
            }
        
        /************************************************************************************//**
        *   \brief Return an HTTP path to the plugin directory.                                 *
        *                                                                                       *
        *   \returns a string, containing the path.                                             *
        ****************************************************************************************/
        protected function get_plugin_path()
            {
            $url = JURI::root().'components/com_bmlt/BMLT-Satellite-Base-Class/';
    
            return $url;
            }
        
        /************************************************************************************//**
        *   \brief This uses the WordPress text processor (__) to process the given string.     *
        *                                                                                       *
        *   This allows easier translation of displayed strings. All strings displayed by the   *
        *   plugin should go through this function.                                             *
        *                                                                                       *
        *   \returns a string, processed by WP.                                                 *
        ****************************************************************************************/
        protected function process_text (  $in_string  ///< The string to be processed.
                                        )
            {
            return htmlspecialchars ( JText::_( $in_string ) );
            }
    
        /************************************************************************************//**
        *   \brief This gets the admin options from the database (allows CMS abstraction).      *
        *                                                                                       *
        *   \returns an associative array, with the option settings.                            *
        ****************************************************************************************/
        protected function cms_get_option ( $in_option_key   ///< The name of the option
                                            )
            {        
            $ret = null;
            $row_data = null;
    
            $row =& JTable::getInstance ( 'bmlt_settings', 'Table' );
            $data_array = array ( $this->geDefaultBMLTOptions() );
    
            if ( $row->load ( 1 ) )
                {
                if ( property_exists ( $row, 'bmlt_data' ) && $row->bmlt_data )
                    {
                    $data_array = unserialize ( $row->bmlt_data );
                    }
                }
            
            if ( $in_option_key != self::$admin2OptionsName )
                {
                $index = max ( 1, intval(str_replace ( self::$adminOptionsName.'_', '', $in_option_key ) ));
                
                $ret = $data_array[$index - 1];
                }
            else
                {
                $ret = array ( 'num_servers' => count ( $data_array ) );
                }
            
            return $ret;
            }
        
        /************************************************************************************//**
        *   \brief This gets the admin options from the database (allows CMS abstraction).      *
        ****************************************************************************************/
        protected function cms_set_option ( $in_option_key,   ///< The name of the option
                                            $in_option_value  ///< the values to be set (associative array)
                                            )
            {
            $row =& JTable::getInstance ( 'bmlt_settings', 'Table' );
    
            if ( !$row->load ( 1 ) )
                {
                $ret = JError::raiseWarning ( 500, $row->getError() );
                }
            else
                {
                $index = 0;
                if ( $in_option_key != self::$admin2OptionsName )
                    {
                    $index = max ( 1, intval(str_replace ( self::$adminOptionsName.'_', '', $in_option_key ) ));
                    }
    
                $row_data = unserialize ( $row->bmlt_data );
                if ( isset ( $row_data ) && is_array ( $row_data ) && count ( $row_data ) )
                    {
                    if ( $index )
                        {
                        $row_data[$index - 1] = $in_option_value;
                        }
    
                    $row->bmlt_data = serialize ( $row_data );
                    
                    if ( !$row->store ( false ) )
                        {
                        $ret = JError::raiseWarning ( 500, $row->getError() );
                        }
                    }
                }
            }
        
        /************************************************************************************//**
        *   \brief Deletes a stored option (allows CMS abstraction).                            *
        ****************************************************************************************/
        protected function cms_delete_option ( $in_option_key   ///< The name of the option
                                            )
            {
            $ret = null;
            $row_data = null;
    
            $row =& JTable::getInstance ( 'bmlt_settings', 'Table' );
            $data_array = array ( $this->geDefaultBMLTOptions() );
    
            if ( $row->load ( 1 ) )
                {
                if ( property_exists ( $row, 'bmlt_data' ) && $row->bmlt_data )
                    {
                    $data_array = unserialize ( $row->bmlt_data );
                    if ( $in_option_key != self::$admin2OptionsName )
                        {
                        $index = max ( 1, intval(str_replace ( self::$adminOptionsName.'_', '', $in_option_key ) ));
                        
                        unset ( $data_array[$index - 1] );
                        
                        $row->bmlt_data = serialize ( $data_array );
                        
                        if ( !$row->store ( false ) )
                            {
                            $ret = JError::raiseWarning ( 500, $row->getError() );
                            }
                        }
                    }
                }
            }
    
        /************************************************************************************//**
        *   \brief This function fetches the settings ID for a page (if there is one).          *
        *                                                                                       *
        *   If $in_check_mobile is set to true, then ONLY a check for mobile support will be    *
        *   made, and no other shortcodes will be checked.                                      *
        *                                                                                       *
        *   \returns a mixed type, with the settings ID.                                        *
        ****************************************************************************************/
        protected function cms_get_page_settings_id ($in_content,               ///< Required (for the base version) content to check.
                                                     $in_check_mobile = false   ///< True if this includes a check for mobile. Default is false.
                                                    )
            {
            $options = $this->getBMLTOptions ( 1 );
            $my_option_id = $options['id'];
            
            if ( !$in_check_mobile && isset ( $this->my_http_vars['bmlt_settings_id'] ) && is_array ($this->getBMLTOptions ( $this->my_http_vars['bmlt_settings_id'] )) )
                {
                $my_option_id = $this->my_http_vars['bmlt_settings_id'];
                }
            elseif ( $in_content || $in_check_mobile )
                {
                $my_option_id_content = parent::cms_get_page_settings_id ( $in_content, $in_check_mobile );
    
                if ( $my_option_id_content )
                    {
                    $my_option_id = $my_option_id_content;
                    }
                elseif ( $in_check_mobile )
                    {
                    $my_option_id = null;
                    }
                }
                    
            return $my_option_id;
            }
            
        /************************************************************************************//**
        *                                  THE CMS CALLBACKS                                    *
        ****************************************************************************************/
            
        /************************************************************************************//**
        *   \brief Presents the admin page.                                                     *
        ****************************************************************************************/
        function admin_page ( )
            {
            $this->admin_head ( );
            echo $this->return_admin_page();
            }
            
        /************************************************************************************//**
        *   \brief Prepares any necessary head content.                                         *
        ****************************************************************************************/
        function standard_head ( $in_content = false    ///< Optional content to determine head elements.
                                )
            {
            $document =& JFactory::getDocument();
            $options_id = $this->cms_get_page_settings_id( $in_content );
    
            $options = $this->getBMLTOptions_by_id ( $options_id );
    
            $root_server_root = $options['root_server'];
    
            if ( $root_server_root )
                {
                $support_mobile = $this->cms_get_page_settings_id ( $in_content, true );
        
                if ( $support_mobile )  // If we support mobile (there's a shortcode, and we're a mobile UA), then we short-circuit the process.
                    {
                    $mobile_options = $this->getBMLTOptions_by_id ( $support_mobile );
                    
                    if ( is_array ( $mobile_options ) && count ( $mobile_options ) )
                        {
                        $mobile_url = $_SERVER['PHP_SELF'].'?BMLTPlugin_mobile&bmlt_settings_id='.$support_mobile;
                        if ( isset ( $this->my_http_vars['WML'] ) )
                            {
                            $mobile_url .= '&WML='.intval ( $this->my_http_vars['WML'] );
                            }
                        if ( isset ( $this->my_http_vars['simulate_smartphone'] ) )
                            {
                            $mobile_url .= '&simulate_smartphone';
                            }
                        ob_end_clean();
                        header ( "location: $mobile_url" );
                        die ( );
                        }
                    }
                
                $root_server = $root_server_root."/client_interface/xhtml/index.php";
                    
                if ( !is_array ( $this->my_http_vars ) || (!isset ( $this->my_http_vars['search_form'] ) && !isset ( $this->my_http_vars['single_meeting_id'] ) && !isset ( $this->my_http_vars['do_search'] )) )
                    {
                    $this->my_http_vars['search_form'] = true;
                    }
                
                $params = '';
                
                if ( !defined ( $support_old_browsers ) || !$support_old_browsers )
                    {
                    $this->my_http_vars['supports_ajax'] = 'yes';
                    $this->my_http_vars['no_ajax_check'] = 'yes';
                    }
                else
                    {
                    $this->my_http_vars['no_ajax_check'] = null;
                    unset ( $this->my_http_vars['no_ajax_check'] );
                    }
                
                foreach ( $this->my_http_vars as $key => $value )
                    {
                    if ( $key != 'switcher' )	// We don't propagate switcher.
                        {
                        if ( is_array ( $value ) )
                            {
                            foreach ( $value as $val )
                                {
                                $params .= '&'.urlencode ( $key ) ."[]=". urlencode ( $val );
                                }
                            $key = null;
                            }
                        if ( $key )
                            {
                            $params .= '&'.urlencode ( $key ) ."=". urlencode ( $value );
                            }
                        }
                    }
                
                try
                    {
                    $uri = "$root_server?switcher=GetHeaderXHTML&style_only$params";
                    $header_code = preg_replace ( '/[^\.\,\;= a-zA-Z0-9\&\?\-_\#:\/\\\]/', '', $this->my_driver->call_curl ( $uri, false ) );
            
                    $styles = explode ( " ", $header_code );
                    foreach ( $styles as $uri2 )
                        {
                        $media = null;
                        if ( preg_match ( '/print/', $uri2 ) )
                            {
                            $media = 'print';
                            }
                        
                        $root_server_root2 = $root_server_root;
                        
                        if ( preg_match ( '|http://|', $uri2 ) )
                            {
                            $root_server_root2 = '';
                            }
                        
                        $document->addStylesheet ( "$root_server_root2$uri2", 'text/css', $media );
                        }
                    
                    $url = $this->get_plugin_path();
            
                    $local_style_url = htmlspecialchars ( $url.'themes/'.$options['theme'].'/' );
                    
                    if ( !defined ('_DEBUG_MODE_' ) )
                        {
                        $local_style_url .= 'style_stripper.php?filename=';
                        }
                    
                    $document->addStylesheet ( "$local_style_url"."styles.css", 'text/css' );
                    $document->addStylesheet ( "$local_style_url"."nouveau_map_styles.css", 'text/css' );
                    }
                catch ( Exception $e )
                    {
                    die ( "BMLT Error: ".htmlspecialchars ( $e ) );
                    }
            
                $style_overloads = '';
                
                if ( trim ( $options['additional_css'] ) )
                    {
                    $style_overloads .= trim ( $additional_css );
                    }
                
                if ( $style_overloads )
                    {
                    $document->addStyleDeclaration ( $style_overloads );
                    }
                
                $this->load_params ( );
                }
            }
            
        /************************************************************************************//**
        *   \brief Echoes any necessary head content for the admin.                             *
        ****************************************************************************************/
        function admin_head ( )
            {
            $document =& JFactory::getDocument();
            
            $comp_path = JURI::root().'components/com_bmlt/BMLT-Satellite-Base-Class';
            
            $document->addScript ( "http://maps.google.com/maps/api/js?sensor=false" );  // Load the Google Maps stuff for our map.
            $document->addScript ( "$comp_path/js_stripper.php?filename=javascript.js" );
            $document->addScript ( "$comp_path/js_stripper.php?filename=admin_javascript.js" );
            $document->addStylesheet ( "$comp_path/style_stripper.php?filename=admin_styles.css", 'text/css' );
            }
    };
    
    /****************************************************************************************//**
    *                                   MAIN CODE CONTEXT                                       *
    ********************************************************************************************/
    global $BMLTPluginOp;
    
    if ( !isset ( $BMLTPluginOp ) && class_exists ( "BMLTJoomlaPlugin" ) )
        {
        $BMLTPluginOp = new BMLTJoomlaPlugin();
        }
    }
else
    {
    echo JError::raiseWarning ( 500, "Missing Critical Component!" );
    }
?>