<?php
/**
 * @version    4.1.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2022 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_ALLVIDEOSHARE_ADVERTISEMENTS',
	'formURL' => 'index.php?option=com_allvideoshare&view=advertisements',
	'helpURL' => 'https://allvideoshare.mrvinoth.com/',
	'icon' => 'icon-copy',
];

$user = Factory::getApplication()->getIdentity();

if ( $user->authorise( 'core.create', 'com_allvideoshare' ) || count( $user->getAuthorisedCategories( 'com_allvideoshare', 'core.create' ) ) > 0 ) {
	$displayData['createURL'] = 'index.php?option=com_allvideoshare&view=advertisements&task=advertisement.add';
}

echo LayoutHelper::render( 'joomla.content.emptystate', $displayData );
