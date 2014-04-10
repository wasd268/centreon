<?php
/**
 * Copyright 2005-2014 MERETHIS
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give MERETHIS
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of MERETHIS choice, provided that
 * MERETHIS also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */

namespace CentreonCustomview\Repository;

use \Centreon\Internal\Exception;


/**
 * @author Sylvestre Ho <sho@merethis.com>
 * @package Centreon
 * @subpackage Repository
 */

/**
 * Class for managing widgets
 */
class WidgetRepository
{
    /**
     * Get Params From Widget Model Id
     *
     * @param int $widgetModelId
     * @return array
     */
    protected static function getParamsFromWidgetModelId($widgetModelId)
    {
        static $tab;

        if (!isset($tab)) {
            $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
            $query = "SELECT parameter_code_name
            		  FROM widget_parameters
            		  WHERE widget_model_id = :model_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':model_id', $widgetModelId);
            $stmt->execute();
            $tab = array();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tab[$row['parameter_code_name']] = $row['parameter_code_name'];
            }
        }
        return $tab;
    }

    /**
     * Get Widget Title
     *
     * @param int $widgetId
     * @return string
     */
    public static function getWidgetTitle($widgetId)
    {
        static $tab;

        if (!isset($tab)) {
            $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
            $tab = array();
            $stmt = $db->prepare("SELECT title, widget_id FROM widgets");
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tab[$row['widget_id']] = $row['title'];
            }
        }
        if (isset($tab[$widgetId])) {
            return $tab[$widgetId];
        }
        return null;
    }

    /**
     * Get Parameter Id By Name
     *
     * @param int $widgetModelId
     * @param string $name
     * @return int
     */
    public static function getParameterIdByName($widgetModelId, $name)
    {
        $tab = array();
        if (!isset($tab[$widgetModelId])) {
            $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
            $query = "SELECT parameter_id, parameter_code_name
            		  FROM widget_parameters
            		  WHERE widget_model_id = :model_id";
            $tab[$widgetModelId] = array();
            $stmt = $db->prepare($query);
            $stmt->bindParam(':model_id', $widgetModelId);
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tab[$widgetModelId][$row['parameter_code_name']] = $row['parameter_id'];
            }
        }
        if (isset($tab[$widgetModelId][$name]) && $tab[$widgetModelId][$name]) {
            return $tab[$widgetModelId][$name];
        }
        return 0;
    }

    /**
     * Get Widget Info
     *
     * @param string $type
     * @param mixed $param
     * @return mixed
     */
    protected static function getWidgetInfo($type = "id", $param)
    {
        static $tabDir;
        static $tabId;

        if (!isset($tabId) || !isset($tabDir)) {
            $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
            $query = "SELECT description, directory, title, widget_model_id, url, version, 
                author, email, website, keywords, screenshot, thumbnail, autoRefresh
            	FROM widget_models";
            $stmt = $db->prepare($query);
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tabDir[$row['directory']] = array();
                $tabId[$row['widget_model_id']] = array();
                foreach ($row as $key => $value) {
                    $tabDir[$row['directory']][$key] = $value;
                    $tabId[$row['widget_model_id']][$key] = $value;
                }
            }
        }
        if ($type == "directory" && isset($tabDir[$param])) {
            return $tabDir[$param];
        }
        if ($type == "id" && isset($tabId[$param])) {
            return $tabId[$param];
        }
        return null;
    }


    /**
     * Add widget to view
     *
     * @param array $params
     * @throws \Centreon\Internal\Exception
     */
    public static function addWidget($params)
    {
        if (!isset($params['custom_view_id']) || !isset($params['widget_model_id']) || !isset($params['widget_title'])) {
            throw new Exception('No custom view or no widget selected');
        }
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $query = "INSERT INTO widgets (title, widget_model_id)
        		  VALUES (:title, :model_id)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $params['widget_title']);
        $stmt->bindParam(':model_id', $params['widget_model_id']);
        $stmt->execute();
        $query = "INSERT INTO widget_views (custom_view_id, widget_id, widget_order)
        		  VALUES (:view_id, :widget_id, :order)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':view_id', $params['custom_view_id']);
        $stmt->bindParam(':widget_id', self::getLastInsertedWidgetId($params['widget_title']));
        $stmt->bindParam(':order', 0);
        $stmt->execute();
    }

    /**
     * Get Wiget Info By Id
     *
     * @param int $widgetModelId
     * @return mixed
     */
    public static function getWidgetInfoById($widgetModelId)
    {
        return self::getWidgetInfo("id", $widgetModelId);
    }

    /**
     * Get Widget Info By Directory
     *
     * @param string $directory
     * @return mixed
     */
    public static function getWidgetInfoByDirectory($directory)
    {
        return self::getWidgetInfo("directory", $directory);
    }

    /**
     * Get URL
     *
     * @param int $widgetId
     * @return string
     */
    public static function getUrl($widgetId)
    {
        $query = "SELECT url FROM widget_models wm, widgets w
        		  WHERE wm.widget_model_id = w.widget_model_id
        		  AND w.widget_id = :widget_id";
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $stmt = $db->prepare($query);
        $stmt->bindParam(':widget_id', $widgetId);
        $stmt->execute();
        if ($stmt->rowCount()) {
            $row = $stmt->fetch();
            return $row['url'];
        } else {
            throw new Exception('No URL found for Widget #'.$widgetId);
        }
    }

    /**
     * Get Refresh Interval
     *
     * @param int $widgetId
     * @return int
     */
    public static function getRefreshInterval($widgetId)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $query = "SELECT autoRefresh FROM widget_models wm, widgets w
        		  WHERE wm.widget_model_id = w.widget_model_id
        		  AND w.widget_id = :widget_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':widget_id', $widgetId);
        $stmt->execute();
        if ($stmt->rowCount()) {
            $row = $stmt->fetch();
            return $row['autoRefresh'];
        } else {
            throw new Exception('No autoRefresh found for Widget #'.$widgetId);
        }
    }

    /**
     * Get Widgets From View Id
     *
     * @param int $viewId
     * @return array
     */
    public static function getWidgetsFromViewId($viewId)
    {
        static $widgets = array();

        if (!isset($widgets[$viewId])) {
            $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
            $widgets[$viewId] = array();
            $query = "SELECT w.widget_id, w.title, wm.url
                FROM widgets w, widget_models wm
            	WHERE w.custom_view_id = :view_id
            	AND w.widget_model_id = wm.widget_model_id
                ORDER BY w.widget_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':view_id', $viewId);
            $stmt->execute();
            $i = 0;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $widgets[$viewId][$i] = $row;
                $i++;
            }
        }
        return $widgets[$viewId];
    }

    /**
     * Get Widget Models
     *
     * @return array
     */
    public static function getWidgetModels()
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $query = "SELECT widget_model_id, title
            FROM widget_models
        	ORDER BY title";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $widgets = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $widgets[$row['widget_model_id']] = $row['title'];
        }
        return $widgets;
    }

    /**
     * Update View Widget Relations
     *
     * @param int $viewId
     * @param array $widgetList
     */
    public static function udpateViewWidgetRelations($viewId, $widgetList)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $query = "DELETE FROM widget_views WHERE custom_view_id = :view_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':view_id', $viewId);
        $stmt->execute();
        $db->beginTransaction();
        foreach ($widgetList as $widgetId) {
            $stmt = $db->prepare("INSERT INTO widget_views (custom_view_id, widget_id) VALUES (?, ?)");
            $stmt->execute(array($viewId, $widgetId));
        }
        $db->commit();
    }

    /**
     * Get Params From Widget Id
     *
     * @param int $widgetId
     * @return array
     */
    public static function getParamsFromWidgetId($widgetId, $hasPermission = false)
    {
        static $params;

        if (!isset($params)) {
            $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
            $params = array();
            $query = "SELECT ft.is_connector, ft.ft_typename, p.parameter_id, p.parameter_name, p.default_value, p.header_title, p.require_permission
            		  FROM widget_parameters_field_type ft, widget_parameters p, widgets w
            		  WHERE ft.field_type_id = p.field_type_id
            		  AND p.widget_model_id = w.widget_model_id
            		  AND w.widget_id = ?
            		  ORDER BY parameter_order ASC";
            $stmt = $db->prepare($query);
            $stmt->execute(array($widgetId));
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                if ($row['require_permission'] && $hasPermission == false) {
                    continue;
                }
                $params[$row['parameter_id']]['parameter_id'] = $row['parameter_id'];
                $params[$row['parameter_id']]['ft_typename'] = $row['ft_typename'];
                $params[$row['parameter_id']]['parameter_name'] = $row['parameter_name'];
                $params[$row['parameter_id']]['default_value'] = $row['default_value'];
                $params[$row['parameter_id']]['is_connector'] = $row['is_connector'];
                $params[$row['parameter_id']]['header_title'] = $row['header_title'];
            }
        }
        return $params;
    }

    /**
     * Update User Widget Preferences
     *
     * @param array $params
     * @param int $userId
     * @return void
     * @throws \Centreon\Internal\Exception
     */
    public static function updateUserWidgetPreferences($params, $userId, $hasPermission = false)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $query = "SELECT wv.widget_view_id
            FROM widget_views wv, custom_view_user_relation cvur
        	WHERE cvur.custom_view_id = wv.custom_view_id
        	AND wv.widget_id = ? 
        	AND cvur.user_id = ?
            AND wv.custom_view_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute(array($params['widget_id'], $userId, $params['custom_view_id']));
        if ($stmt->rowCount()) {
            $row = $stmt->fetch();
            $widgetViewId = $row['widget_view_id'];
        } else {
            throw new Exception('No widget_view_id found for user');
        }
        
        if ($hasPermission == false) {
            $stmt = $db->prepare("DELETE FROM widget_preferences
        	    WHERE widget_view_id = ?
        		AND user_id = ?
                AND parameter_id NOT IN (SELECT parameter_id 
                FROM widget_parameters WHERE require_permission = '1')");
        } else {
            $stmt = $db->prepare("DELETE FROM widget_preferences
        				  WHERE widget_view_id = ?
        				  AND user_id = ?");
        }
        $stmt->execute(array($widgetViewId, $userId));

        $db->beginTransaction();
        foreach ($params as $key => $val) {
            if (preg_match("/param_(\d+)/", $key, $matches)) {
                if (is_array($val)) {
                    if (isset($val['op_'.$matches[1]]) && isset($val['cmp_'.$matches[1]])) {
                        $val = $val['op_'.$matches[1]]. ' ' .$val['cmp_'.$matches[1]];
                    } elseif (isset($val['order_'.$matches[1]]) && isset($val['column_'.$matches[1]])) {
                        $val = $val['column_'.$matches[1]]. ' ' .$val['order_'.$matches[1]];
                    } elseif (isset($val['from_'.$matches[1]]) && isset($val['to_'.$matches[1]])) {
                        $val = $val['from_'.$matches[1]].','.$val['to_'.$matches[1]];
                    }
                }
                $stmt = $db->prepare("INSERT INTO widget_preferences (widget_view_id, parameter_id, preference_value, user_id) VALUES (?, ?, ?, ?)");
                $stmt->execute(array($widgetViewId, $matches[1], $val, $userId));
            }
        }
        $db->commit();
    }

    /**
     * Delete Widget From View
     *
     * @param array $params
     * @return void
     */
    public static function deleteWidgetFromView($params)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $stmt = $db->prepare("DELETE FROM widget_views
        		  WHERE widget_id = ?");
        $stmt->execute(array($params['widget_id']));
    }

    /**
     * Read Configuration File
     *
     * @param string $filename
     * @return array
     */
    public static function readConfigFile($filename)
    {
        $xmlString = file_get_contents($filename);
        $xmlObj = simplexml_load_string($xmlString);
        return CentreonUtils::objectIntoArray($xmlObj);
    }

    /**
     * Get Last Inserted Widget id
     *
     * @param string $title
     * @return int
     */
    protected static function getLastInsertedWidgetId($title)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $stmt = $db->prepare("SELECT MAX(widget_id) as lastId FROM widgets WHERE title = ?");
        $stmt->execute(array($title));
        $row = $stmt->fetch();
        return $row['lastId'];
    }

    /**
     * Get Last Inserted Widget Model id
     *
     * @param string $directory
     * @return int
     */
    protected static function getLastInsertedWidgetModelId($directory)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $stmt = $db->prepare("SELECT MAX(widget_model_id) as lastId FROM widget_models WHERE directory = ?");
        $stmt->execute(array($directory));
        $row = $stmt->fetch();
        return $row['lastId'];
    }

    /**
     * Get Last Inserted Parameter id
     *
     * @param string $label
     * @return int
     */
    protected static function getLastInsertedParameterId($label)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $stmt = $db->prepare("SELECT MAX(parameter_id) as lastId FROM widget_parameters WHERE parameter_name = ?");
        $stmt->execute(array($label));
        $row = $stmt->fetch();
        return $row['lastId'];
    }

    /**
     * Get Parameter Type IDs
     *
     * @return array
     */
    protected static function getParameterTypeIds()
    {
        static $types;

        if (!isset($types)) {
            $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
            $types = array();
            $stmt = $db->prepare("SELECT ft_typename, field_type_id FROM  widget_parameters_field_type");
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $types[$row['ft_typename']] = $row['field_type_id'];
            }
        }
        return $types;
    }

    /**
     * Insert Widget Preferences
     *
     * @param int $lastId
     * @param array $config
     * @throws \Centreon\Internal\Exception
     */
    protected static function insertWidgetPreferences($lastId, $config)
    {
        if (isset($config['preferences'])) {
            $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
            $types = self::getParameterTypeIds();
            foreach ($config['preferences'] as $preference) {
                $order = 1;
                foreach ($preference as $pref) {
                    $attr = $pref['@attributes'];
                    if (!isset($types[$attr['type']])) {
                        throw new Exception('Unknown type : ' . $attr['type'] . ' found in configuration file');
                    }
                    if (!isset($attr['requirePermission'])) {
                        $attr['requirePermission'] = 0;
                    }
                    if (!isset($attr['defaultValue'])) {
                        $attr['defaultValue'] = '';
                    }
                    if (!isset($attr['header'])) {
                        $attr['header'] = null;
                    }
                    $stmt = $db->prepare("INSERT INTO widget_parameters
                    		  (widget_model_id, field_type_id, parameter_name, parameter_code_name, default_value, parameter_order, require_permission, header_title)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute($lastId, $types[$attr['type']], $attr['label'], $attr['name'], $attr['defaultValue'], $order, $attr['requirePermission'], $attr['header']);
                    $lastParamId  = self::getLastInsertedParameterId($attr['label']);
                    self::insertParameterOptions($lastParamId, $attr, $pref);
                    $order++;
                }
            }
        }
    }

    /**
     * Install
     *
     * @param string $widgetPath
     * @param string $directory
     */
    public static function install($widgetPath, $directory)
    {
        $config = self::readConfigFile($widgetPath."/".$directory."/configs.xml");
        if (!$config['autoRefresh']) {
            $config['autoRefresh'] = 0;
        }
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $stmt = $db->prepare("INSERT INTO widget_models (title, description, url, version, directory, 
            author, email, website, keywords, screenshot, thumbnail, autoRefresh)
        	VALUES (:title, :description, :url, :version, :directory, 
            :author, :email, :website, :keywords, :screenshot, :thumbnail, :autorefresh)");
        $stmt->bindParam(':title', $config['title']);
        $stmt->bindParam(':description', $config['description']);
        $stmt->bindParam(':url', $config['url']);
        $stmt->bindParam(':version', $config['version']);
        $stmt->bindParam(':directory', $directory);
        $stmt->bindParam(':author', $config['author']);
        $stmt->bindParam(':email', $config['email']);
        $stmt->bindParam(':website', $config['website']);
        $stmt->bindParam(':keywords', $config['keywords']);
        $stmt->bindParam(':screenshot', $config['screenshot']);
        $stmt->bindParam(':thumbnail', $config['thumbnail']);
        $stmt->bindParam(':autorefresh', $config['autoRefresh']);
        $stmt->execute();
        $lastId = self::getLastInsertedWidgetModelId($directory);
        self::insertWidgetPreferences($lastId, $config);
    }


    /**
     * Insert Parameter Options
     *
     * @param int $paramId
     * @param array $attr
     * @param array $pref
     * @return void
     */
    protected static function insertParameterOptions($paramId, $attr, $pref)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        if ($attr['type'] == "list" || $attr['type'] == "sort") {
            if (isset($pref['option'])) {
                $db->beginTransaction();
                foreach ($pref['option'] as $option) {
                    if (isset($option['@attributes'])) {
                        $opt = $option['@attributes'];
                    } else {
                        $opt = $option;
                    }
                    $stmt = $db->prepare("INSERT INTO widget_parameters_multiple_options (parameter_id, option_name, option_value) VALUES (?, ?, ?)");
                    $stmt->execute(array($paramId, $opt['label'], $opt['value']));
                }
                $db->commit();
            }
        } elseif ($attr['type'] == "range") {
            $stmt = $db->prepare("INSERT INTO widget_parameters_range (parameter_id, min_range, max_range, step)
                VALUES (?, ?, ?, ?)");
            $stmt->execute(array($paramId, $attr['min'], $attr['max'], $attr['step']));
        }
    }

    /**
     * Upgrade preferences
     *
     * @param int $widgetModelId
     * @param array $config
     * @return void
     */
    protected static function upgradePreferences($widgetModelId, $config)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $existingParams = self::getParamsFromWidgetModelId($widgetModelId);
        $currentParameterTab = array();
        if (isset($config['preferences'])) {
            $types = self::getParameterTypeIds();
            foreach ($config['preferences'] as $preference) {
                $order = 1;
                foreach ($preference as $pref) {
                    $attr = $pref['@attributes'];
                    if (!isset($types[$attr['type']])) {
                        throw new Exception('Unknown type : ' . $attr['type'] . ' found in configuration file');
                    }
                    if (!isset($existingParams[$attr['name']])) {
                        if (!isset($attr['requirePermission'])) {
                            $attr['requirePermission'] = 0;
                        }
                        if (!isset($attr['header'])) {
                            $attr['header'] = "NULL ";
                        } else {
                            $attr['header'] = "'".$attr['header']."'";
                        }
                        $stmt = $db->prepare("INSERT INTO widget_parameters (widget_model_id, field_type_id, parameter_name, 
                            parameter_code_name, default_value, parameter_order, require_permission, header_title) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute(array(
                            $widgetModelId, $types[$attr['type']], $attr['label'], $attr['name'], 
                            $attr['defaultValue'], $order, $attr['requiredPermission'], $attr['header']
                        ));
                    } else {
                        $query = "UPDATE widget_parameters SET 
                            field_type_id = :type,
                            parameter_name = :parameter_name,
                            default_value = :default,
                            parameter_order = :order,
                            require_permission = :permission,
                            header_title = :header
                            WHERE parameter_code_name = :code_name
                            AND widget_model_id = :model_id";
                        if (!isset($attr['requirePermission'])) {
                            $attr['requirePermission'] = 0;
                        }
                        if (!isset($attr['header'])) {
                            $attr['header'] = null;
                        }
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':code_name', $attr['name']);
                        $stmt->bindParam(':model_id', $widgetModelId);
                        $stmt->bindParam(':type', $types[$attr['label']]);
                        $stmt->bindParam(':parameter_name', $attr['label']);
                        $stmt->bindParam(':default', $attr['defaultValue']);
                        $stmt->bindParam(':order', $order);
                        $stmt->bindParam(':permission', $attr['requirePermission']);
                        $stmt->bindParam(':header', $attr['header']);
                        $stmt->execute();
                    }
                    $parameterId = self::getParameterIdByName($widgetModelId, $attr['name']);
                    $currentParameterTab[$attr['name']] = 1;
                    $stmt = $db->prepare("DELETE FROM widget_parameters_multiple_options WHERE parameter_id = ?");
                    $stmt->execute(array($parameterId));
                    self::insertParameterOptions($parameterId, $attr, $pref);
                    $order++;
                }
            }
        }
        $db->beginTransaction();
        $stmt = $db->prepare("DELETE FROM widget_parameters WHERE parameter_code_name = ?");
        foreach ($existingParams as $codeName) {
            if (!isset($currentParameterTab[$codeName])) {
                $stmt->execute(array($codeName));
            }
        }
        $db->commit();
    }

    /**
     * Upgrade
     *
	 * @param string $widgetPath
     * @param string $directory
     */
    public static function upgrade($widgetPath, $directory)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $config = self::readConfigFile($widgetPath."/".$directory."/configs.xml");
        if (!$config['autoRefresh']) {
            $config['autoRefresh'] = 0;
        }
        $query = "UPDATE widget_models SET
            title = :title
            description = :description
        	url = :url
        	version = :version
        	author = :author
            email = :email
            website = :website
            keywords = :keywords
            screenshot = :screenshot
            thumbnail = :thumbnail
            autoRefresh = :autorefresh
        	WHERE directory = :directory";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $config['title']);
        $stmt->bindParam(':description', $config['description']);
        $stmt->bindParam(':url', $config['url']);
        $stmt->bindParam(':version', $config['version']);
        $stmt->bindParam(':author', $config['author']);
        $stmt->bindParam(':email', $config['email']);
        $stmt->bindParam(':website', $config['website']);
        $stmt->bindParam(':keywords', $config['keywords']);
        $stmt->bindParam(':screenshot', $config['screenshot']);
        $stmt->bindParam(':thumbnail', $config['thumbnail']);
        $stmt->bindParam(':autorefresh', $config['autoRefresh']);
        $stmt->bindParam(':directory', $directory);
        $stmt->execute();
        $info = self::getWidgetInfoByDirectory($directory);
        self::upgradePreferences($info['widget_model_id'], $config);
    }

    /**
     * Uninstall
     *
     * @param string $directory
     */
    public static function uninstall($directory)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $stmt = $db->prepare("DELETE FROM widget_models WHERE directory = ?");
        $stmt->execute(array($directory));
    }

    /**
     * Get widget Preferences
     *
     * @param int $widgetId
     * @param int $userId
     * @return array
     */
    public static function getWidgetPreferences($widgetId, $userId)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        $stmt = $db->prepare("SELECT default_value, parameter_code_name
            FROM widget_parameters param, widgets w
        	WHERE w.widget_model_id = param.widget_model_id
            AND w.widget_id = ?");
        $stmt->execute(array($widgetId));
        $tab = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tab[$row['parameter_code_name']] = $row['default_value'];
        }

        $stmt = $db->prepare("SELECT pref.preference_value, param.parameter_code_name
            FROM widget_preferences pref, widget_parameters param, widget_views wv
           	WHERE param.parameter_id = pref.parameter_id
           	AND pref.widget_view_id = wv.widget_view_id
           	AND wv.widget_id = ?
            AND pref.user_id = ?");
        $stmt->execute(array($widgetId, $userId));
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tab[$row['parameter_code_name']] = $row['preference_value'];
        }
        return $tab;
    }

    /**
     * Rename widget
     *
     * @param array $params
     * @return string
     */
    public static function rename($params)
    {
        $db = \Centreon\Internal\Di::getDefault()->get('db_centreon');
        if (!isset($params['elementId']) || !isset($params['newName'])) {
            throw new Exception('Missing mandatory parameters elementId or newName');
        }
        if (preg_match("/title_(\d+)/", $params['elementId'], $matches)) {
            if (isset($matches[1])) {
                $widgetId = $matches[1];
            }
        }
        if (!isset($widgetId)) {
            throw new Exception('Missing widget id');
        }
        $stmt = $db->prepare("UPDATE widgets
            SET title = ?
            WHERE widget_id = ?");
        $stmt->execute(array($params['newName'], $widgetId));
        return $params['newName'];
    }
}