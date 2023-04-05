<?php
/**
 * @version    4.1.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2022 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Model;

// No direct access.
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

/**
 * Class RatingsModel.
 *
 * @since  4.1.0
 */
class RatingsModel extends ListModel {

	/**
	* Constructor.
	*
	* @param  array  $config  An optional associative array of configuration settings.
	*
	* @see    JController
	* @since  4.1.0
	*/
	public function __construct( $config = array() ) {
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields'] = array(
				'id', 'a.id',				
				'videoid', 'a.videoid',
				'ratings', 'a.ratings',
				'userid', 'a.userid',
				'sessionid', 'a.sessionid',
				'created_date', 'a.created_date',
				'updated_date', 'a.updated_date'
			);
		}

		parent::__construct( $config );
	}	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	protected function populateState( $ordering = null, $direction = null )	{
		// List state information
		parent::populateState( 'a.id', 'DESC' );

		$context = $this->getUserStateFromRequest( $this->context . '.filter.search', 'filter_search' );
		$this->setState( 'filter.search', $context );

		// Split context into component and optional section
		$parts = FieldsHelper::extract( $context );

		if ( $parts ) {
			$this->setState( 'filter.component', $parts[0] );
			$this->setState( 'filter.section', $parts[1] );
		}
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   4.1.0
	 */
	protected function getStoreId( $id = '' ) {
		// Compile the store id.
		$id .= ':' . $this->getState( 'filter.search' );
		$id .= ':' . $this->getState( 'filter.ratings' );
		$id .= ':' . $this->getState( 'filter.userid' );

		
		return parent::getStoreId( $id );		
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   4.1.0
	 */
	protected function getListQuery() {
		// Create a new query object
		$db    = $this->getDbo();
		$query = $db->getQuery( true );

		// Select the required fields from the table
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from( '`#__allvideoshare_ratings` AS a' );
		
		// Join over the videos for the video id, title
		$query->select( "v.id AS vid, v.title AS title" );
		$query->join( "LEFT", "#__allvideoshare_videos AS v ON v.id=a.videoid" );

		// Join over the users for the name
		$query->select( "u.name AS user" );
		$query->join( "LEFT", "#__users AS u ON u.id=a.userid" );

		// Filter by ratings
		$ratings = $this->getState( 'filter.ratings' );

		if ( is_numeric( $ratings ) ) {
			$query->where( 'a.ratings = ' . (float) $ratings );
		}

		// Filter by userid
		$userid = $this->getState( 'filter.userid' );

		if ( is_numeric( $userid ) ) {
			$query->where( 'a.userid = ' . (int) $userid );
		}

		// Filter by search in title
		$search = $this->getState( 'filter.search' );

		if ( ! empty( $search ) ) {
			if ( stripos( $search, 'vid:' ) === 0 ) {
				$query->where( 'v.id = ' . (int) substr( $search, 4 ) );
			} elseif ( stripos( $search, 'id:' ) === 0 ) {
				$query->where( 'a.id = ' . (int) substr( $search, 3 ) );
			} else {
				$search = $db->Quote( '%' . $db->escape( $search, true ) . '%' );
				$query->where( '( v.title LIKE ' . $search . ' )' );
			}
		}
		
		// Add the list ordering clause
		$orderCol  = $this->state->get( 'list.ordering', 'a.id' );
		$orderDirn = $this->state->get( 'list.direction', 'DESC' );

		if ( $orderCol && $orderDirn ) {
			$query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return  mixed  Array of data items on success, false on failure.
	 * 
	 * @since   4.1.0
	 */
	public function getItems() {
		$items = parent::getItems();
		return $items;
	}

}
