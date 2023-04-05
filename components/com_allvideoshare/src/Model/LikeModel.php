<?php
/**
 * @version    4.1.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2022 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Model;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\AdminModel;
use \Joomla\CMS\Table\Table;

/**
 * Class LikeModel.
 *
 * @since  4.1.0
 */
class LikeModel extends AdminModel {

	/**
	 * @var    string  The prefix to use with controller messages.
	 *
	 * @since  4.1.0
	 */
	protected $text_prefix = 'COM_ALLVIDEOSHARE';

	/**
	 * @var    string  Alias to manage history control
	 *
	 * @since  4.1.0
	 */
	public $typeAlias = 'com_allvideoshare.like';

	/**
	 * @var    null  Item data
	 *
	 * @since  4.1.0
	 */
	protected $item = null;	

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A database object
	 *
	 * @since   4.1.0
	 */
	public function getTable( $type = 'Like', $prefix = 'Administrator', $config = array() ) {
		return parent::getTable( $type, $prefix, $config );
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A \JForm object on success, false on failure
	 *
	 * @since   4.1.0
	 */
	public function getForm( $data = array(), $loadData = true ) {
		// Initialise variables
		$app = Factory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			$this->typeAlias, 
			'like',
			array(
				'control' => 'jform',
				'load_data' => $loadData 
			)
		);		

		if ( empty( $form ) ) {
			return false;
		}

		return $form;
	}	

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   4.1.0
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data
		$data = Factory::getApplication()->getUserState( 'com_allvideoshare.edit.like.data', array() );

		if ( empty( $data ) ) {
			if ( $this->item === null )	{
				$this->item = $this->getItem();
			}

			$data = $this->item;
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   4.1.0
	 */
	public function getItem( $pk = null ) {		
		if ( $item = parent::getItem( $pk ) ) {
			if ( isset( $item->params ) ) {
				$item->params = json_encode( $item->params );
			}
			
			// Do any procesing on fields here if needed
		}

		return $item;		
	}	

}
