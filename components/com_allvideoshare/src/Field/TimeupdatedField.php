<?php
/**
 * @version    4.1.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2022 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Field;

\defined( 'JPATH_BASE' ) or die;

use \Joomla\CMS\Date\Date;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Form\FormField;
use \Joomla\CMS\Language\Text;

/**
 * Class TimeupdatedField.
 *
 * @since  4.1.0
 */
class TimeupdatedField extends FormField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.1.0
	 */
	protected $type = 'timeupdated';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   4.1.0
	 */
	protected function getInput() {
		// Initialize variables
		$html = array();

		$old_time_updated = $this->value;
		$hidden           = (boolean) $this->element['hidden'];

		if ( $hidden == null || ! $hidden )	{
			if ( ! strtotime( $old_time_updated ) )	{
				$html[] = '-';
			} else {
				$jdate       = new Date( $old_time_updated );
				$pretty_date = $jdate->format( Text::_( 'DATE_FORMAT_LC2' ) );
				$html[]      = "<div>" . $pretty_date . "</div>";
			}
		}

		$time_updated = Factory::getDate( 'now', Factory::getConfig()->get( 'offset' ) )->toSql( true );
		$html[]       = '<input type="hidden" name="' . $this->name . '" value="' . $time_updated . '" />';

		return implode( $html );
	}
	
}
