<?php
/**
 * @version    4.1.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2022 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\View\Likes;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Form\Form;
use \Joomla\CMS\HTML\Helpers\Sidebar;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * View class for a list of likes.
 *
 * @since  4.1.0
 */
class HtmlView extends BaseHtmlView {

	protected $state;

	protected $items;

	protected $pagination;	

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
		$this->items = $this->get( 'Items' );
		$this->pagination = $this->get( 'Pagination' );		

		if ( ! count( $this->items ) && $this->get( 'IsEmptyState' ) ) {
			$this->setLayout( 'emptystate' );
		}
		
		$this->filterForm = $this->get( 'FilterForm' );
		$this->activeFilters = $this->get( 'ActiveFilters' );

		// Check for errors
		if ( count( $errors = $this->get( 'Errors' ) ) ) {
			throw new \Exception( implode( "\n", $errors ) );
		}

		$this->addToolbar();

		$this->sidebar = Sidebar::render();
		parent::display( $tpl );
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	protected function addToolbar()	{
		$state = $this->get( 'State' );
		$canDo = AllVideoShareHelper::getActions();

		ToolbarHelper::title( Text::_( 'COM_ALLVIDEOSHARE_TITLE_LIKES_DISLIKES' ), 'jcc fa-thumbs-up' );

		$toolbar = Toolbar::getInstance( 'toolbar' );

		if ( $canDo->get( 'core.edit.state' ) || count( $this->transitions ) ) {
			if ( isset( $this->items[0] ) ) {
				// If this component does not use state then show a direct delete button as we can not trash
				$toolbar->delete( 'likes.delete' )
					->text( 'JTOOLBAR_DELETE' )
					->message( 'JGLOBAL_CONFIRM_DELETE' )
					->listCheck( true );
			}
		}		

		if ( $canDo->get( 'core.admin' ) ) {
			$toolbar->preferences( 'com_allvideoshare' );
		}

		// Set sidebar action
		Sidebar::setAction( 'index.php?option=com_allvideoshare&view=likes' );
	}
	
	/**
	 * Method to order fields 
	 *
	 * @return  void 
	 * 
	 * @since   4.1.0
	 */
	protected function getSortFields() {
		return array(			
			'a.`id`' => Text::_( 'JGRID_HEADING_ID' ),
			'a.`title`' => Text::_( 'JGLOBAL_TITLE' ),
			'a.`userid`' => Text::_( 'COM_ALLVIDEOSHARE_TITLE_USER' ),
			'a.`likes`' => Text::_( 'COM_ALLVIDEOSHARE_TITLE_LIKES' ),
			'a.`dislikes`' => Text::_( 'COM_ALLVIDEOSHARE_TITLE_DISLIKES' )			
		);
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return  bool
	 * 
	 * @since   4.1.0
	 */
	public function getState( $state ) {
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}

}
