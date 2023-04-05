<?php
/**
 * @version    4.1.0
 * @package    Com_Allvideoshare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2022 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Field;

\defined( 'JPATH_BASE' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Form\Field\UrlField;
use \Joomla\CMS\Language\Text;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * Class CustomurlField.
 *
 * @since  4.1.0
 */
class CustomurlField extends UrlField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.1.0
	 */
	protected $type = 'customurl';

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   4.1.0
	 */
	protected function getInput() {
		if ( ! AllVideoShareHelper::canDo() ) {
			$extension = isset( $this->element['extension'] ) ? $this->element['extension'] : 'com_allvideoshare';
			return '<label class="control-label text-danger">' . Text::_( strtoupper( $extension ) . '_PREMIUM_LBL_PREMIUM_ONLY' ) . '</label>';
		}
		
		// Trim the trailing line in the layout file
		return rtrim( $this->getRenderer( $this->layout )->render( $this->getLayoutData() ), PHP_EOL );
	}	

}