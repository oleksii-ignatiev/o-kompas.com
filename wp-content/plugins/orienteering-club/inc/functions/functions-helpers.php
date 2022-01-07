<?php

if ( ! function_exists( 'ocm_plugin_activate' ) ) {
    function ocm_plugin_activate() {

        $old_version = get_option('ocm_version');

        if( $old_version == '' ) {
            // update version
            update_option('ocm_version', OCM_VERSION);
        } else if ( OCM_VERSION > $old_version ) {
            // upgrade from earlier plugin version
            
            
            // update version
            update_option('ocm_version', OCM_VERSION);
        }
    }
}


if ( ! function_exists( 'ocm_plugin_deactivate' ) ) {
    function ocm_plugin_deactivate() {
        //delete_option('ocm_version');
    }
}


if ( ! function_exists( 'get_user_entry_data' ) ) {
    function get_user_entry_data($field) {
        global $qualifications;

        $current_user = wp_get_current_user();
        switch ($field) {
            case 'Имя':
                return $current_user->first_name;    
            case 'Фамилия':
                return $current_user->last_name;
            case 'Разряд':
                return $qualifications[ (int)get_the_author_meta( 'qualification', $current_user->ID ) ];
            case 'ФСТ':
                return get_the_author_meta( 'community', $current_user->ID );
            case 'Номер SI':
                return get_the_author_meta( 'si_number', $current_user->ID );
            case 'Клуб':
                return get_the_author_meta( 'club', $current_user->ID );
        }
    }
}


/**
	 * Return SVG markup.
	 *
	 * @param array $args {
	 *     Parameters needed to display an SVG.
	 *
	 *     @type string $icon  Required SVG icon filename.
	 *     @type string $title Optional SVG title.
	 *     @type string $desc  Optional SVG description.
	 * }
	 * @return string SVG markup.
	 */
	function get_svg( $args = array() ) {
		// Make sure $args are an array.
		if ( empty( $args ) ) {
			return __( 'Please define default parameters in the form of an array.', 'massey' );
		}
		
		// Define an icon.
		if ( false === array_key_exists( 'icon', $args ) ) {
			return __( 'Please define an SVG icon filename.', 'massey' );
		}
		
		// Set defaults.
		$defaults = array(
			'icon'        => '',
			'title'       => '',
			'desc'        => '',
			'aria_hidden' => true, // Hide from screen readers.
			'fallback'    => false,
			'class'       => '',
		);
		
		// Parse args.
		$args = wp_parse_args( $args, $defaults );
		
		// Set aria hidden.
		$aria_hidden = '';
		
		if ( true === $args['aria_hidden'] ) {
			$aria_hidden = ' aria-hidden="true"';
		}
		
		// Set ARIA.
		$aria_labelledby = '';
		
		if ( $args['title'] && $args['desc'] ) {
			$aria_labelledby = ' aria-labelledby="title desc"';
		}
		
		// Begin SVG markup.
		$svg = '<svg width="25" height="25" class="icon icon-'.esc_attr($args['icon']).' '.esc_attr($args['class']).'" '.$aria_hidden . $aria_labelledby . ' role="img">';
		
		// If there is a title, display it.
		if ( $args['title'] ) {
			$svg .= '<title>' . esc_html( $args['title'] ) . '</title>';
		}
		
		// If there is a description, display it.
		if ( $args['desc'] ) {
			$svg .= '<desc>' . esc_html( $args['desc'] ) . '</desc>';
		}
		
		$svg .= '<use xlink:href="#' . esc_html( $args['icon']) .'"></use>';
		
		// Add some markup to use as a fallback for browsers that do not support SVGs.
		if ( $args['fallback'] ) {
			$svg .= '<span class="svg-fallback icon-' . esc_attr( $args['icon'] ) .'"></span>';
		}
		
		$svg .= '</svg>';
		
		return $svg;
	}

if ( ! function_exists( 'get_edit_button' ) ) {
    function get_edit_button($event_id, $group_number) {
        $button = "<button class='edit-entry' data-event-id='{$event_id}' data-group-number='{$group_number}'>". print_svg( IMAGES . 'edit-box.svg') ."</button>";

        return $button;
        
    }
}


if ( ! function_exists( 'get_delete_button' ) ) {
    function get_delete_button($event_id) {
        $button = "<button class='delete-entry' data-event-id='{$event_id}'>". print_svg( IMAGES . 'delete.svg') ."</button>";

        return $button;
        
    }
}


if ( ! function_exists( 'print_svg' ) ) {
	function print_svg( $file = '' ) {
        $data = '';
        if ($file):
            $data = file_get_contents($file);
        endif;
        return $data;
    }
}