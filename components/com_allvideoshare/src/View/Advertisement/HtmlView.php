<?php
/**
 * @version    4.1.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2022 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\View\Advertisement;

// No direct access
defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * View class for a single advertisement.
 *
 * @since  4.1.0
 */
class HtmlView extends BaseHtmlView {

	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function display( $tpl = null ) {
		$this->state = $this->get( 'State' );
		$this->item  = $this->get( 'Item' );
		$this->form  = $this->get( 'Form' );

		// Check for errors
		if ( count( $errors = $this->get( 'Errors' ) ) ) {
			throw new \Exception( implode( "\n", $errors ) );
		}

		$this->addToolbar();
		parent::display( $tpl );
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	protected function addToolbar()	{
		Factory::getApplication()->input->set( 'hidemainmenu', true );

		$user  = Factory::getUser();
		$isNew = ( $this->item->id == 0 );

		if ( isset( $this->item->checked_out ) ) {
			$checkedOut = ! ( $this->item->checked_out == 0 || $this->item->checked_out == $user->get( 'id' ) );
		} else {
			$checkedOut = false;
		}

		$canDo = AllVideoShareHelper::getActions();

		$componentName = Text::_( 'COM_ALLVIDEOSHARE' );
		$title = $isNew ? Text::sprintf( 'COM_ALLVIDEOSHARE_ADVERTISEMENT_ADD_TITLE', $componentName ) : Text::sprintf( 'COM_ALLVIDEOSHARE_ADVERTISEMENT_EDIT_TITLE', $componentName );
		ToolbarHelper::title( $title, 'jcc fa-bullhorn' );

		// If not checked out, can save the item
		if ( ! $checkedOut && ( $canDo->get( 'core.edit' ) || ( $canDo->get( 'core.create' ) ) ) ) {
			ToolbarHelper::apply( 'advertisement.apply', 'JTOOLBAR_APPLY' );
			ToolbarHelper::save( 'advertisement.save', 'JTOOLBAR_SAVE' );
		}

		if ( ! $checkedOut && ( $canDo->get( 'core.create' ) ) ) {
			ToolbarHelper::custom( 'advertisement.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
		}

		// If an existing item, can save to a copy
		if ( ! $isNew && $canDo->get( 'core.create' ) )	{
			ToolbarHelper::custom( 'advertisement.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false );
		}

		if ( empty( $this->item->id ) )	{
			ToolbarHelper::cancel( 'advertisement.cancel', 'JTOOLBAR_CANCEL' );
		} else {
			ToolbarHelper::cancel( 'advertisement.cancel', 'JTOOLBAR_CLOSE' );
		}
	}
	
}
