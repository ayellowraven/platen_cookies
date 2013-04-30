<?php
	
	Class extension_platen_cookies extends Extension{

		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'appendPreferences'
				),
				array(
					'page' => '/system/preferences/',
					'delegate' => 'Save',
					'callback' => 'savePreferences'
				),
				array(
					'page' => '/frontend/',
					'delegate' => 'FrontendParamsPostResolve',
					'callback' => 'addParameters'
				)
			);
		}
		
		public function install() {
			// Add defaults to config.php
			Symphony::Configuration()->setArray(
				array('platen_cookies' => array(
					
					// default preferences
					'bookslug' => '',
				))
			);

			return Administration::instance()->saveConfig();
		}
		
		public function uninstall() {
			if($config) {
				// remove config
				Symphony::Configuration()->remove('platen_cookies');			
				Administration::instance()->saveConfig();
			}
		}
		
		
		public function appendPreferences($context) {
			$config = Symphony::Configuration()->get('platen_cookies');

			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', 'Project Platen Cookies'));


			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');
			
			$label = Widget::Label(__('Slug for book goes in front of book-specific cookies'));
			$label->appendChild(Widget::Input(
				'settings[platen_cookies][bookslug]',
				$config['bookslug'],
				'text',
				array(
					'placeholder' => 'e.g. eohc'
				)
			));
			$label->appendChild(new XMLElement('span', __('No spaces or dashes. (e.g. eohc for Expedition of Humphry Clinker.)'), array('class'=>'help')));
			$group->appendChild($label);

			$fieldset->appendChild($group);
			
							
			$context['wrapper']->appendChild($fieldset);
		}
		
		/*
		 * Save preferences
		 *
		 * @param array $context
		 *  delegate context
		 */
		public function savePreferences($context) {
			$settings = array_map('trim', $context['settings']['platen_cookies']);
		}
		
		/*
		 * Append lat/long/country to param pool
		 */
		public function addParameters($context) {
			$config = Symphony::Configuration()->get('platen_cookies');
			$slug = $config['bookslug'];
			$reading_mode = $slug.'-reading-mode';
			$bookmark = $slug.'-bookmark';
			
    		$night = !empty($_COOKIE['night']) ? $_COOKIE['night'] : 'auto';
    		$book_reading_mode = !empty($_COOKIE[$reading_mode]) ? $_COOKIE[$reading_mode] : 'quiet';
    		$book_bookmark = !empty($_COOKIE[$bookmark]) ? $_COOKIE[$bookmark] : '';


    		$context['params']['night'] = $night;
    		$context['params'][$reading_mode] = $book_reading_mode;
			$context['params'][$bookmark] = $book_bookmark;
        }

	}
?>