<?php
/**
 * @version    4.1.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2022 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Controller;

\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Application\SiteApplication;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Multilanguage;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Controller\AdminController;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use \Joomla\Utilities\ArrayHelper;

/**
 * Class UsersController.
 *
 * @since  4.1.0
 */
class UsersController extends AdminController {
	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since   4.1.0
	 */
	public function getModel( $name = 'User', $prefix = 'Administrator', $config = array() ) {
		return parent::getModel( $name, $prefix, array( 'ignore_request' => true ) );
	}

	/**
	 * Method to toggle fields on a list
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function toggle() {
		// Initialise variables
		$app    = Factory::getApplication();
		$ids    = $app->input->get( 'cid', array(), '', 'array' );
		$field  = $app->input->get( 'field' );

		if ( empty( $ids ) ) {
			$app->enqueueMessage( 'warning', Text::_( 'JERROR_NO_ITEMS_SELECTED' ) );
		} else {
			// Get the model
			$model = $this->getModel( 'User' );

			foreach ( $ids as $pk )	{
				// Toggle the items
				if ( ! $model->toggle( $pk, $field ) ) {
					throw new \Exception( $model->getError(), 500 );
				}
			}
		}

		$this->setRedirect( Route::_( 'index.php?option=' . $app->input->get( 'option' ) . '&view=' . $app->input->get( 'view' ), false ) );
	}
	
}
