<?php
/*
 * Banner Plugin for WolfCMS <http://www.wolfcms.org>
 * Copyright (C) 2011 Shannon Brooks <shannon@brooksworks.com>
 *
 * This file is part of Banner Plugin. Banner Plugin is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

// Security Measure
if (!defined('IN_CMS')) { exit(); }

/**
 * The Banner class represents a banner on a page.
 */
class Banner extends Record
{
	const TABLE_NAME = 'banner';
	
	public static function find($args = null)
	{
		// Collect attributes...
		$where = isset($args['where']) ? trim($args['where']) : '1';
		$order_by = isset($args['order']) ? trim($args['order']) : 'banner.created ASC';
		$offset = isset($args['offset']) ? (int)$args['offset'] : 0;
		$limit = isset($args['limit']) ? (int)$args['limit'] : 0;

		// Prepare query parts
		$order_by_string = empty($order_by) ? '' : "ORDER BY $order_by";
		$limit_string = $limit > 0 ? "LIMIT $limit" : '';
		$offset_string = $offset > 0 ? "OFFSET $offset" : '';

		$tablename = self::tableNameFromClassName('Banner');

		// Prepare SQL
		$sql = "SELECT * FROM $tablename AS banner WHERE $where $order_by_string $limit_string $offset_string";

		$stmt = self::$__CONN__->prepare($sql);
		$stmt->execute();

		// Run!
		if ($limit == 1) {
			return $stmt->fetchObject('Banner');
		} else {
			$objects = array();
			while ($object = $stmt->fetchObject('Banner'))
				$objects[] = $object;

			return $objects;
		}
	}

	/**
	 * Find Banners limited to 10.
	 * 
	 * @param mixed $args Unused.
	 * @return Array An array of Banner objects.
	 */
	public static function findAll($args = array()) {
		//$args['limit'] = isset($args['limit']) ? $args['limit'] : 10;
		return self::find($args);
	}

	/**
	 * Find a specific banner by its id.
	 * 
	 * @param int $id The banner's id.
	 * @return Banner A Banner object.
	 */
	public static function findById($id)
	{
		return self::find(array('where' => 'banner.id=' . (int)$id, 'limit' => 1));
	}

	/**
	 * Find a specific banner by its size.
	 * 
	 * @param int $width is banner width
	 * @param int $height is banner height
	 * @return Banner A Banner object.
	 */
	public static function findBySize($width,$height)
	{
		return self::find(array('where' => 'banner.width='.(int)$width.' AND banner.height='.(int)$height.' AND banner.active=1 AND ( banner.expires IS NULL OR banner.expires > CURDATE() )','order'=>'banner.updated ASC','limit'=>1));
	}

	/**
	 * Find a specific banner by its size.
	 * 
	 * @param int $width is banner width
	 * @param int $height is banner height
	 * @return Banner A Banner object.
	 */
	public static function findAllBySize($width,$height)
	{
		return self::find(array('where' => 'banner.width='.(int)$width.' AND banner.height='.(int)$height.' AND banner.active=1 AND ( banner.expires IS NULL OR banner.expires > CURDATE() )','order'=>'banner.updated ASC'));
	}


} // end Banner class
