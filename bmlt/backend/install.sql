DROP TABLE IF EXISTS `#__bmlt_settings`;

CREATE TABLE IF NOT EXISTS `#__bmlt_settings` (
    `id` int(11) NOT NULL DEFAULT '1',
    `bmlt_data` longtext NOT NULL,
    KEY `id` (`id`)
) ENGINE=MyISAM;

INSERT INTO `#__bmlt_settings` (`id`, `bmlt_data`) VALUES (1, 'a:1:{i:0;a:17:{s:11:"root_server";s:43:"http://bmlt.magshare.net/stable/main_server";s:19:"map_center_latitude";d:40.7618201;s:20:"map_center_longitude";d:-73.4628296;s:8:"map_zoom";d:9;s:19:"bmlt_new_search_url";s:0:"";s:13:"gmaps_api_key";s:0:"";s:17:"bmlt_initial_view";s:0:"";s:22:"push_down_more_details";s:1:"1";s:14:"additional_css";s:0:"";s:2:"id";i:13028654045921;s:12:"setting_name";s:42:"Default Settings (BMLT Test Stable Server)";s:5:"theme";s:7:"default";s:9:"lang_enum";s:2:"en";s:9:"lang_name";s:7:"English";s:14:"distance_units";s:2:"mi";s:12:"grace_period";s:2:"15";s:11:"time_offset";s:1:"0";}}');
