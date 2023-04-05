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
 * Class AdvertisementsController.
 *
 * @since  4.1.0
 */
class AdvertisementsController extends AdminController {
	
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
	public function getModel( $name = 'Advertisement', $prefix = 'Administrator', $config = array() ) {
		return parent::getModel( $name, $prefix, array( 'ignore_request' => true ) );
	}	

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function saveOrderAjax()	{
		// Get the input
		$input = Factory::getApplication()->input;
		$pks   = $input->post->get( 'cid', array(), 'array' );
		$order = $input->post->get( 'order', array(), 'array' );

		// Sanitize the input
		ArrayHelper::toInteger( $pks );
		ArrayHelper::toInteger( $order );

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder( $pks, $order );

		if ( $return ) {
			echo '1';
		}

		// Close the application
		Factory::getApplication()->close();
	}
	
}
