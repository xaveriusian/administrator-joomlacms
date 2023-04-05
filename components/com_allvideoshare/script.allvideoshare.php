<?php
/**
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2022 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;

/**
 * Updates the database structure of the component
 *
 * @since  1.0.0
 */
class Com_AllVideoShareInstallerScript extends InstallerScript {

	/**
	 * The title of the component (printed on installation and uninstallation messages)
	 *
	 * @var  string
	 */
	protected $extension = 'All Video Share';

	/**
	 * The minimum Joomla! version required to install this extension
	 *
	 * @var  string
	 */
	protected $minimumJoomla = '4.0';

	/**
	 * Method called before install/update the component
	 * 
	 * Note: This method won't be called during uninstall process
	 *
	 * @param   string  $type    Type of process [install | update]
	 * @param   mixed   $parent  Object who called this method
	 *
	 * @return  boolean  True if the process should continue, false otherwise
	 * 
	 * @since   1.0.0
     * @throws  Exception
	 */
	public function preflight( $type, $parent )	{
		$result = parent::preflight( $type, $parent );

		if ( ! $result ) {
			return $result;
		}

		// logic for preflight before install
		return $result;
	}

	/**
	 * Method to install the component
	 *
	 * @param   mixed  $parent  Object who called this method.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function install( $parent ) {	
		$this->installDb( $parent );
		$this->installPlugins( $parent );
		$this->installModules( $parent );
	}

	/**
	 * Method to update the component
	 *
	 * @param   mixed  $parent  Object who called this method.
	 *
	 * @return  void
	 * 
	 * @since   1.0.0
	 */
	public function update( $parent ) {	
		$this->installDb( $parent );
		$this->installPlugins( $parent );
		$this->installModules( $parent );	
	}	

	/**
	 * Method called after install/update the component.
	 * 
	 * @param   string  $type    type
	 * @param   string  $parent  parent
	 *
	 * @return  boolean
	 * 
	 * @since   1.0.0
	 */
	public function postflight( $type, $parent ) {
		return true;
	}	

	/**
	 * Method to uninstall the component
	 *
	 * @param   mixed  $parent  Object who called this method.
	 *
	 * @return  void
	 * 
	 * @since   4.1.0
	 */
	public function uninstall( $parent ) {
		$this->uninstallPlugins( $parent );
		$this->uninstallModules( $parent );
	}

	/**
	 * Method to update the DB of the component
	 *
	 * @param   mixed  $parent  Object who started the upgrading process
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	private function installDb( $parent ) {
		$db = Factory::getDbo();

		// Migrate from Joomla 3.x
		if ( $this->existsTable( '#__allvideoshare_config' ) ) { 
			$this->migrateFromJoomla3();			
		} 
		
		// Set Configuration Parameters
		$query = $db->getQuery( true );

		$query
			->clear()
			->select( 'params' )
			->from( '#__extensions' )
			->where(
				array(
					'type LIKE ' . $db->quote( 'component' ),
					'element LIKE ' . $db->quote( 'com_allvideoshare' )
				)
			);		

		$db->setQuery( $query );
		$params = json_decode( $db->loadResult(), true );

		if ( ! empty( $params ) ) {	
			if ( isset( $params['playerid'] ) ) { // Updating from version less than 4.1.0
				$playerid = ! empty( $params['playerid'] ) ? (int) $params['playerid'] : 1;
				
				$query
					->clear()
					->select( '*' )
					->from( '#__allvideoshare_players' )
					->where( 'id = ' . $playerid );

				$db->setQuery( $query );
				$player = $db->loadObject();

				if ( ! empty( $player ) ) {
					unset( $params['playerid'] );

					$params['player_width'] = '';
					$params['player_ratio'] = $player->ratio;
					$params['autoplay'] = $player->autostart;
					$params['loop'] = $player->loop;
					$params['volume'] = $player->volumelevel;
					$params['muted'] = 0;
					$params['controlbar'] = $player->controlbar;
					$params['duration'] = $player->durationdock;
					$params['currenttime'] = $player->timerdock;
					$params['fullscreen'] = $player->fullscreendock;
					$params['quality'] = $player->hddock;
					$params['embed'] = $player->embeddock;
					$params['share'] = $player->sharedock;
					$params['adsource'] = $player->ad_engine;
					$params['preroll'] = $player->preroll;
					$params['postroll'] = $player->postroll;
					$params['adtagurl'] = $player->vast_url;
					$params['excerpt'] = 0;
					$params['excerpt_length'] = 150;

					if ( isset( $params['layout'] ) ) {
						if ( 'all' == $params['layout'] || 'relatedvideos' == $params['layout'] ) {
							$params['related_videos'] = 1;
						}

						if ( 'relatedvideos' == $params['layout'] || 'none' == $params['layout'] ) {
							$params['comments_type'] = '';
						}
					}

					$this->setComponentParams( $params );
				}

				// Update Player Module
				$query
					->clear()
					->select( array( 'extension_id', 'params' ) )
					->from( '#__extensions' )
					->where(
						array(
							'type LIKE ' . $db->quote( 'module' ),
							'element LIKE ' . $db->quote( 'mod_allvideoshareplayer' )
						)
					);		

				$db->setQuery( $query );
				$modules = $db->loadObjectList();

				if ( ! empty( $modules ) ) {
					foreach ( $modules as $module ) {
						$params = json_decode( $module->params, true );

						if ( ! empty( $params ) ) {
							if ( isset( $params['playerid'] ) ) { // Updating from versions less than 4.1.0
								$playerid = ! empty( $params['playerid'] ) ? (int) $params['playerid'] : 1;

								$query
									->clear()
									->select( '*' )
									->from( '#__allvideoshare_players' )
									->where( 'id = ' . $playerid );

								$db->setQuery( $query );
								$player = $db->loadObject();
				
								if ( ! empty( $player ) ) {
									unset( $params['playerid'] );

									$params['player_width'] = '';
									$params['player_ratio'] = $player->ratio;
									$params['autoplay'] = $player->autostart;
									$params['loop'] = $player->loop;
									$params['volume'] = $player->volumelevel;
									$params['muted'] = 0;
									$params['controlbar'] = $player->controlbar;
									$params['duration'] = $player->durationdock;
									$params['currenttime'] = $player->timerdock;
									$params['fullscreen'] = $player->fullscreendock;
									$params['quality'] = $player->hddock;
									$params['embed'] = $player->embeddock;
									$params['share'] = $player->sharedock;
									$params['adsource'] = $player->ad_engine;
									$params['preroll'] = $player->preroll;
									$params['postroll'] = $player->postroll;
									$params['adtagurl'] = $player->vast_url;
				
									$query
										->clear()
										->update( '#__extensions' )
										->set( 'params = ' . $db->quote( json_encode( $params ) ) )
										->where( 'extension_id = ' . (int) $module->extension_id );

									$db->setQuery( $query );
									$db->execute();
								}
							}
						}
					}
				}
			}
		} else {	
			$params = array(
				'player_width' => '',
				'player_ratio' => 56.25,
				'autoplay' => 0,
				'loop' => 0,
				'volume' => 50,
				'muted' => 0,
				'show_gdpr_consent' => 1,								
				'controlbar' => 1,
				'duration' => 1,
				'currenttime' => 1,
				'fullscreen' => 1,
				'quality' => 1,
				'embed' => 0,
				'share' => 0,
				'adsource' => 'custom',
				'preroll' => 0,
				'postroll' => 0,
				'adtagurl' => '',
				'image_ratio' => 56.25,
				'rows' => 3,
				'cols' => 3,
				'views' => 1,
				'excerpt' => 0,
				'excerpt_length' => 150,
				'popup' => 0,		
				'itemid_video' => 0,
				'ratings' => 0,
				'guest_ratings' => 0,
				'likes' => 0,
				'guest_likes' => 0,						
				'title' => 1,
				'description' => 1,
				'related_videos' => 1,
				'category' => 1,				
				'search' => 1,
				'comments_type' => '',
				'fbappid' => '',
				'comments_posts' => 2,
				'comments_color' => 'color',
				'type_youtube' => 1,
				'type_vimeo' => 1,
				'type_hls' => 1,								
				'vimeo_authorization_token' => '',
				'multi_categories' => 0,
				'custom_css' => '',
				'show_feed' => 1,
				'feed_limit' => 20,
				'licensekey' => '',
				'logo' => '',
				'logoposition' => 'bottomleft',
				'logoalpha' => 50,
				'logotarget' => 'https://allvideoshare.mrvinoth.com/',
				'displaylogo' => 1
			);	
			
			$this->setComponentParams( $params );
		}		
		
		// ...
		$query = 'CREATE TABLE IF NOT EXISTS `#__allvideoshare_options` (
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,    
			`name` VARCHAR(255) NOT NULL,
			`value` TEXT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;';

		$db->setQuery( $query );
		// $db->execute();
	}

	/**
	 * Installs plugins for this component
	 *
	 * @param   mixed  $parent  Object who called the install/update method
	 *
	 * @return  void
	 * 
	 * @since  4.1.0
	 */
	private function installPlugins( $parent ) {
		$installation_folder = $parent->getParent()->getPath( 'source' );
		$app = Factory::getApplication();

		/* @var $plugins SimpleXMLElement */
		if ( method_exists( $parent, 'getManifest' ) ) {
			$plugins = $parent->getManifest()->plugins;
		} else {
			$plugins = $parent->get( 'manifest' )->plugins;
		}

		if ( count( $plugins->children() ) ) {
			$db    = Factory::getDbo();
			$query = $db->getQuery( true );

			foreach ( $plugins->children() as $plugin )	{
				$pluginName  = (string) $plugin['plugin'];
				$pluginGroup = (string) $plugin['group'];
				$path        = $installation_folder . '/plugins/' . $pluginGroup . '/' . $pluginName;
				$installer   = new Installer;

				if ( ! $this->isAlreadyInstalled( 'plugin', $pluginName, $pluginGroup ) ) {
					$result = $installer->install( $path );
				} else {
					$result = $installer->update( $path );
				}

				if ( $result ) {
					$app->enqueueMessage( 
						Text::sprintf(
							'Plugin %s was installed successfully', 
							$pluginName
						)
					);
				} else {
					$app->enqueueMessage(
						Text::sprintf(
							'There was an issue installing the plugin %s', 
							$pluginName
						),
						'error'
					);
				}

				$query
					->clear()
					->update( '#__extensions' )
					->set( 'enabled = 1' )
					->where(
						array(
							'type LIKE ' . $db->quote( 'plugin' ),
							'element LIKE ' . $db->quote( $pluginName ),
							'folder LIKE ' . $db->quote( $pluginGroup )
						)
					);

				$db->setQuery( $query );
				$db->execute();
			}
		}
	}	

	/**
	 * Installs modules for this component
	 *
	 * @param   mixed  $parent  Object who called the install/update method
	 *
	 * @return  void
	 * 
	 * @since  4.1.0
	 */
	private function installModules( $parent ) {
		$installation_folder = $parent->getParent()->getPath( 'source' );
		$app = Factory::getApplication();

		if ( method_exists( $parent, 'getManifest' ) ) {
			$modules = $parent->getManifest()->modules;
		} else {
			$modules = $parent->get( 'manifest' )->modules;
		}

		if ( ! empty( $modules ) ) {
			if ( count( $modules->children() ) ) {
				foreach ( $modules->children() as $module ) {
					$moduleName = (string) $module['module'];
					$path       = $installation_folder . '/modules/' . $moduleName;
					$installer  = new Installer;

					if ( ! $this->isAlreadyInstalled( 'module', $moduleName ) )	{
						$result = $installer->install( $path );
					} else {
						$result = $installer->update( $path );
					}

					if ( $result ) {
						$app->enqueueMessage( 
							Text::sprintf(
								'Module %s was installed successfully', 
								$moduleName
							)
						);
					} else {
						$app->enqueueMessage(
							Text::sprintf(
								'There was an issue installing the module %s', 
								$moduleName
							),
							'error'
						);
					}
				}
			}
		}
	}	

	/**
	 * Uninstalls plugins
	 *
	 * @param   mixed  $parent  Object who called the uninstall method
	 *
	 * @return  void
	 * 
	 * @since   4.1.0
	 */
	private function uninstallPlugins( $parent ) {
		$app = Factory::getApplication();

		if ( method_exists( $parent, 'getManifest' ) ) {
			$plugins = $parent->getManifest()->plugins;
		} else {
			$plugins = $parent->get( 'manifest' )->plugins;
		}

		if ( count( $plugins->children() ) ) {
			$db    = Factory::getDbo();
			$query = $db->getQuery( true );

			foreach ( $plugins->children() as $plugin )	{
				$pluginName  = (string) $plugin['plugin'];
				$pluginGroup = (string) $plugin['group'];

				$query
					->clear()
					->select( 'extension_id' )
					->from( '#__extensions' )
					->where(
						array(
							'type LIKE ' . $db->quote( 'plugin' ),
							'element LIKE ' . $db->quote( $pluginName ),
							'folder LIKE ' . $db->quote( $pluginGroup )
						)
					);

				$db->setQuery( $query );
				$extension = $db->loadResult();

				if ( ! empty( $extension ) ) {
					$installer = new Installer;
					$result    = $installer->uninstall( 'plugin', $extension );

					if ( $result ) {
						$app->enqueueMessage( 
							Text::sprintf(
								'Plugin %s was uninstalled successfully', 
								$pluginName
							)
						 );
					} else {
						$app->enqueueMessage(
							Text::sprintf(
								'There was an issue uninstalling the plugin %s', 
								$pluginName
							),
							'error'
						);
					}
				}
			}
		}
	}

	/**
	 * Uninstalls modules
	 *
	 * @param   mixed  $parent  Object who called the uninstall method
	 *
	 * @return  void
	 * 
	 * @since   4.1.0
	 */
	private function uninstallModules( $parent ) {
		$app = Factory::getApplication();

		if ( method_exists( $parent, 'getManifest' ) ) {
			$modules = $parent->getManifest()->modules;
		} else {
			$modules = $parent->get( 'manifest' )->modules;
		}

		if ( ! empty( $modules ) ) {
			if ( count( $modules->children() ) ) {
				$db    = Factory::getDbo();
				$query = $db->getQuery( true );

				foreach ( $modules->children() as $module ) {
					$moduleName = (string) $module['module'];

					$query
						->clear()
						->select( 'extension_id' )
						->from( '#__extensions' )
						->where(
							array(
								'type LIKE ' . $db->quote( 'module' ),
								'element LIKE ' . $db->quote( $moduleName )
							)
						);

					$db->setQuery( $query );
					$extension = $db->loadResult();

					if ( ! empty( $extension ) ) {
						$installer = new Installer;
						$result    = $installer->uninstall( 'module', $extension );

						if ( $result ) {
							$app->enqueueMessage( 
								Text::sprintf(
									'Module %s was uninstalled successfully', 
									$moduleName
								) 
							);
						} else {
							$app->enqueueMessage(
								Text::sprintf(
									'There was an issue uninstalling the module %s', 
									$moduleName
								),
								'error'
							);
						}
					}
				}
			}
		}
	}	

	/**
	 * Checks if a certain table exists on the current database
	 *
	 * @param   string   $table_name  Name of the table
	 *
	 * @return  boolean  True if it exists, false if it does not
	 * 
	 * @since   4.1.0
	 */
	private function existsTable( $table_name ) {
		$db = Factory::getDbo();
		$table_name = str_replace( '#__', $db->getPrefix(), (string) $table_name );
		return in_array( $table_name, $db->getTableList() );
	}

	/**
	 * Checks if a field exists on a table
	 *
	 * @param   string   $table_name  Table name
	 * @param   string   $field_name  Field name
	 *
	 * @return  boolean  True if exists, false if it do
	 * 
	 * @since   4.1.0
	 */
	private function existsField( $table_name, $field_name ) {
		$db = Factory::getDbo();
		return in_array( (string) $field_name, array_keys( $db->getTableColumns( $table_name ) ) );
	}

	/**
	 * Check if an extension is already installed in the system
	 *
	 * @param   string  $type    Extension type
	 * @param   string  $name    Extension name
	 * @param   mixed   $folder  Extension folder(for plugins)
	 *
	 * @return  boolean
	 * 
	 * @since  4.1.0
	 */
	private function isAlreadyInstalled( $type, $name, $folder = null )	{
		$result = false;

		switch ( $type ) {
			case 'plugin':
				$result = file_exists( JPATH_PLUGINS . '/' . $folder . '/' . $name );
				break;
			case 'module':
				$result = file_exists( JPATH_ROOT . '/modules/' . $name );
				break;
		}

		return $result;
	}

	/**
	 * Migrate from Joomla 3.x
	 * 
	 * @since  4.1.0
	 */
	private function migrateFromJoomla3() {
		$app = Factory::getApplication();
		$db  = Factory::getDbo();		

		// Get config data
		$query = 'SELECT * FROM #__allvideoshare_config WHERE id=1';
		$db->setQuery( $query );
		$config = $db->loadObject();

		// Get licensing data
		$query = 'SELECT * FROM #__allvideoshare_licensing WHERE id=1';
		$db->setQuery( $query );
		$licensing = $db->loadObject();

		$obj_merged = (object) array_merge( (array) $config, (array) $licensing );

		// Set component params
		$this->setComponentParams( $obj_merged );

		// Drop the config table
		$query = 'DROP TABLE IF EXISTS #__allvideoshare_config';
		$db->setQuery( $query );
		$db->execute();

		// Drop the licensing table
		$query = 'DROP TABLE IF EXISTS #__allvideoshare_licensing';
		$db->setQuery( $query );
		$db->execute();

		// Clean unwanted directories from the back-end components folder
		$folders = array(
			'assets',
			'controllers',
			'libraries',
			'models',
			'tables',
			'views'
		);

		foreach ( $folders as $folder ) {
			$path = JPATH_ROOT . '/administrator/components/com_allvideoshare/' . $folder;

			if ( Folder::exists( $path ) ) {
				Folder::delete( $path );
			}
		}

		$files = array(
			'allvideoshare.php',
			'install.mysql.sql',
			'uninstall.mysql.sql'
		);

		foreach ( $files as $file ) {
			$path = JPATH_ROOT . '/administrator/components/com_allvideoshare/' . $file;
			
			if ( File::exists( $path ) ) {
				File::delete( $path );
			}
		}

		// Clean unwanted directories from the front-end components folder
		$folders = array(
			'assets',
			'controllers',
			'models',
			'views'
		);

		foreach ( $folders as $folder ) {
			$path = JPATH_ROOT . '/components/com_allvideoshare/' . $folder;

			if ( Folder::exists( $path ) ) {
				Folder::delete( $path );
			}
		}

		$files = array(
			'allvideoshare.php',
			'komento_plugin.php',
			'router.php',
			'player.swf'
		);

		foreach ( $files as $file ) {
			$path = JPATH_ROOT . '/components/com_allvideoshare/' . $file;
			
			if ( File::exists( $path ) ) {
				File::delete( $path );
			}
		}
	}

	/**
	 * Set component configuration parameters
	 * 
	 * @param  int  $config  Array of default configuration values
	 * 
	 * @since  4.1.0
	 */
	private function setComponentParams( $config ) {
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		$params = array();
		foreach ( $config as $key => $value ) {
			$params[ $key ] = $value;
		}	

		// Save the parameters		
		$query = $db->getQuery( true );

		$query
			->clear()
			->update( '#__extensions' )
			->set( 'params = ' . $db->quote( json_encode( $params ) ) )
			->where(
				array(
					'type LIKE ' . $db->quote( 'component' ),
					'element LIKE ' . $db->quote( 'com_allvideoshare' )
				)
			);
			
		$db->setQuery( $query );
		$db->execute();
	}
	
}
