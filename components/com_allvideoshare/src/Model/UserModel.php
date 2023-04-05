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
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\AdminModel;
use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\CMS\Table\Table;

/**
 * Class UserModel.
 *
 * @since  4.1.0
 */
class UserModel extends AdminModel {

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
	public $typeAlias = 'com_allvideoshare.video';

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
	 * @return  Table    A database object
	 *
	 * @since   4.1.0
	 */
	public function getTable( $type = 'Video', $prefix = 'Administrator', $config = array() ) {
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
			'video',
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
		$data = Factory::getApplication()->getUserState( 'com_allvideoshare.edit.video.data', array() );

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

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  Table Object
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	protected function prepareTable( $table ) {
		if ( empty( $table->id ) ) {
			// Set ordering to the last item if not set
			if ( @$table->ordering === '' )	{
				$db = Factory::getDbo();
				$db->setQuery( 'SELECT MAX(ordering) FROM #__allvideoshare_videos' );
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * Save any changes on toggle button clicked on list view
	 *
	 * @param   int     $pk     Primary key of the item
	 * @param   string  $field  Name of the field to toggle
	 *
	 * @return  bool
	 * 
	 * @since   4.1.0
	 */
	public function toggle( $pk, $field ) {
		$result = false;

		// Obtain item data
		$item = $this->getItem( $pk );

		if ( $item ) {
			$data           = get_object_vars( $item );
			$data[ $field ] = ( $item->$field == 1 ) ? 0 : 1;
			$result         = $this->save( $data );
		}

		return $result;
	}
	
}
