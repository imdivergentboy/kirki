<?php

// No need to proceed if Redux exists.
if ( class_exists( 'Redux' ) ) {
	return;
}

class Redux {

	public static $config   = array();
	public static $fields   = array();
	public static $panels   = array();
	public static $sections = array();

	/**
	 * the class constructor
	 */
	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'add_to_customizer' ), 1 );
	}

	public static function setArgs( $opt_name = '', $args = array() ) {
		Kirki::add_config( $opt_name, array(
			'option_type' => 'option',
			'option_name' => $args['opt_name'],
		) );
	}

	public static function setSection( $config_id, $args = array() ) {

		if ( ! isset( $args['fields'] ) || ! isset( $args['subsection'] ) || ( isset( $args['subsection'] ) && ! $args['subsection'] ) ) { // This is a panel
			Kirki::$panels[] = array(
				'id'          => isset( $args['id'] ) ? sanitize_key( $args['id'] ) : substr(str_shuffle("abcdefghijklmnopqrstuvwxyz-_"), 0, 7),
				'title'       => isset( $args['title'] ) ? $args['title'] : '',
				'priority'    => ( isset( $args['priority'] ) ) ? $args['priority'] : 10,
				'description' => ( isset( $args['desc'] ) ) ? $args['desc'] : '',
			);
		} else { // This is a section
			// Get the section ID
			if ( isset( $args['subsection'] ) && $args['subsection'] ) {
				$panel    = end( array_values( Kirki::$panels ) );
				$panel_id = $panel['id'];
			}

			Kirki::$sections[] = array(
				'id'          => isset( $args['id'] ) ? sanitize_key( $args['id'] ) : substr(str_shuffle("abcdefghijklmnopqrstuvwxyz-_"), 0, 7),
				'title'       => $args['title'],
				'priority'    => ( isset( $args['priority'] ) ) ? $args['priority'] : 10,
				'panel'       => ( isset( $panel_id ) ) ? $panel_id : '',
				'description' => ( isset( $args['desc'] ) ) ? $args['desc'] : '',
			);

			foreach ( $args['fields'] as $field ) {

				$field['section']     = isset( $args['id'] ) ? sanitize_key( $args['id'] ) : substr(str_shuffle("abcdefghijklmnopqrstuvwxyz-_"), 0, 7);
				$field['settings']    = $field['id'];
				$field['help']        = ( isset( $field['desc'] ) ) ? $field['desc'] : '';
				$field['description'] = ( isset( $field['subtitle'] ) ) ? $field['subtitle'] : '';
				$field['choices']     = ( isset( $field['options'] ) ) ? $field['options'] : '';
				$field['label']       = ( isset( $field['title'] ) ) ? $field['title'] : '';

				switch ( $field['type'] ) {

					case 'ace_editor' :
						$field['type'] = 'textarea';
						break;
					case 'background' :
						// TODO
						break;
					case 'border' :
						// TODO
						break;
					case 'button_set' :
						$field['type'] = 'radio-buttonset';
						break;
					case 'checkbox' :
						if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
							$field['type'] = 'multicheck';
						}
					case 'color_gradient' :
						// TODO
						break;
					case 'color_rgba' :
						$field['type'] = 'color-alpha';
						if ( isset( $field['default'] ) && is_array( $field['default'] ) ) {
							$field['default']['color'] = isset( $field['default']['color'] ) ? Kirki_Color::sanitize_hex( $field['default']['color'], true ) : '#ffffff';
							$field['default']['alpha'] = isset( $field['default']['alpha'] ) ? $field['default']['alpha'] : '1';
							$field['default'] = Kirki_Color::get_rgba( $field['default']['color'], $field['default']['alpha'] );
						}
						break;
					case 'date' :
						// TODO
						break;
					case 'dimensions' :
						// TODO
						break;
					case 'divide' :
						// TODO
						break;
					case 'gallery' :
						// TODO
						break;
					case 'image_select' :
						$field['type'] = 'radio-image';
						break;
					case 'import_export' :
						// TODO
						break;
					case 'info' :
						$fiel['label'] = '';
						$field['help'] = '';
						$field['type'] = 'custom';
						$background_color = '#fcf8e3';
						$border_color     = '#faebcc';
						$text_color       = '#8a6d3b';
						if ( isset( $field['style'] ) ) {
							if ( 'success' == $field['style'] ) {
								$background_color = '#dff0d8';
								$border_color     = '#d6e9c6';
								$text_color       = '#3c763d';
							} elseif ( 'critical' == $field['style'] ) {
								$background_color = '#f2dede';
								$border_color     = '#ebccd1';
								$text_color       = '#a94442';
							}
						}
						$field['default']  = '<div style="padding: 10px;background:' . $background_color . ';border-radius:4px;border:1px solid ' . $border_color . ';color:' . $text_color . ';">';
						$field['default'] .= ( isset( $field['title'] ) ) ? '<h4>' . $field['title'] . '</h4>' : '';
						$field['default'] .= ( isset( $field['desc'] ) ) ? $field['desc'] : '';
						$field['default'] .= '</div>';
						break;
					case 'link_color' :
						// TODO
						break;
					case 'media' :
						// TODO
						break;
					case 'multi_text' :
						// TODO
						break;
					case 'palette' :
						$field['choices'] = $field['palettes'];
						break;
					case 'password' :
						// TODO
						break;
					case 'raw' :
						$field['default'] = $field['content'];
						break;
					case 'section' :
						// TODO
						break;
					case 'select' :
						if ( is_array( $field['choices'] ) ) {
							foreach ( $field['choices'] as $key => $value ) {
								if ( is_array( $value ) ) {
									foreach ( $value as $child_key => $child_value ) {
										$field['choices'][$child_key] = $child_value;
									}
									unset( $field['choices'][$key] );
								}
							}
						}
						break;
					case 'select_image' :
						// TODO
						break;
					case 'slider' :
						$field['choices'] = array(
							'min'  => $field['min'],
							'max'  => $field['max'],
							'step' => $field['step'],
						);
						break;
					case 'slides' :
						// TODO
						break;
					case 'spinner' :
						$field['type'] = 'number';
						break;
					case 'sortable' :
						// TODO
						break;
					case 'sorter' :
						// TODO
						break;
					case 'spacing' :
						// TODO
						break;
					case 'spinner' :
						// TODO
						break;
					case 'switch' :
						// TODO
						break;
					case 'typography' :
						// TODO
						break;

				}

				Kirki::add_field( $config_id, $field );

			}

		}

	}

	public static function setHelpTab() {}

	public static function setHelpSidebar() {}

	/**
	 * Helper function that adds the fields, sections and panels to the customizer.
	 */
	public function add_to_customizer( $wp_customize ) {
		add_filter( 'kirki/fields', array( $this, 'merge_fields' ) );
		add_action( 'customize_register', array( $this, 'add_panels' ), 998 );
		add_action( 'customize_register', array( $this, 'add_sections' ), 999 );
	}

}
