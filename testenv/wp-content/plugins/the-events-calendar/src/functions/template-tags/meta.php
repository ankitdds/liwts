<?php

/**
 * Meta Factory Classes
 *
 * @uses  Tribe__Events__Meta_Factory
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Tribe__Events__Main' ) ) {

	/**
	 * register a meta group
	 *
	 * @uses Tribe__Events__Meta_Factory::register()
	 *
	 * @param string $meta_group_id
	 * @param array  $args
	 *
	 * @return bool $success
	 */
	function tribe_register_meta_group( $meta_group_id, $args = array() ) {
		// setup default for registering a meta group
		$defaults = array( 'register_type' => 'meta_group', 'register_overwrite' => true );

		// parse the $default and $args into the second param for registering a meta item
		return Tribe__Events__Meta_Factory::register( $meta_group_id, wp_parse_args( $args, $defaults ) );
	}

	/**
	 * register a meta item
	 *
	 * @uses Tribe__Events__Meta_Factory::register()
	 *
	 * @param int   $meta_id
	 * @param array $args
	 *
	 * @return bool $success
	 */
	function tribe_register_meta( $meta_id, $args = array() ) {
		return Tribe__Events__Meta_Factory::register( $meta_id, $args );
	}

	/**
	 * Get the meta group.
	 *
	 * @param      $meta_group_id
	 * @param bool $is_the_meta
	 *
	 * @return bool|mixed|void
	 */
	function tribe_get_meta_group( $meta_group_id, $is_the_meta = false ) {

		do_action( 'tribe_get_meta_group', $meta_group_id, $is_the_meta );

		$type = 'meta_group';

		// die silently if the requested meta group is not registered
		if ( ! Tribe__Events__Meta_Factory::check_exists( $meta_group_id, $type ) ) {
			return false;
		}

		$meta_group = Tribe__Events__Meta_Factory::get_args( $meta_group_id, $type );
		$meta_ids   = Tribe__Events__Meta_Factory::get_order( $meta_group_id );
		$group_html = '';

		// internal check for hiding items in the meta
		if ( ! $meta_group['show_on_meta'] ) {
			return false;
		}
		$meta_pos_int     = 0;
		$total_meta_items = tribe_count_hierarchical( $meta_ids );
		foreach ( $meta_ids as $meta_id_group ) {
			foreach ( $meta_id_group as $meta_id ) {
				$meta_pos_int ++;

				$group_html = tribe_separated_field( $group_html, $meta_group['wrap']['meta_separator'], tribe_get_meta( $meta_id, $is_the_meta ) );
			}
		}

		$params = array( $meta_group_id );

		if ( ! empty( $meta['filter_callback'] ) ) {
			return call_user_func_array( $meta['filter_callback'], $params );
		}

		if ( ! empty( $meta['callback'] ) ) {
			$value = call_user_func_array( $meta['callback'], $params );
		}

		$value = empty( $value ) ? $group_html : $value;

		$html = ! empty( $group_html ) ? Tribe__Events__Meta_Factory::template( $meta_group['label'], $value, $meta_group_id, 'meta_group' ) : '';

		return apply_filters( 'tribe_get_meta_group', $html, $meta_group_id );
	}

	/**
	 * Get the meta.
	 *
	 * @param      $meta_id
	 * @param bool $is_the_meta
	 *
	 * @return bool|mixed|void
	 */
	function tribe_get_meta( $meta_id, $is_the_meta = false ) {

		do_action( 'tribe_get_meta', $meta_id, $is_the_meta );

		// die silently if the requested meta item is not registered
		if ( ! Tribe__Events__Meta_Factory::check_exists( $meta_id ) ) {
			return false;
		}

		$meta = Tribe__Events__Meta_Factory::get_args( $meta_id );

		// internal check for hiding items in the meta
		if ( ! $meta['show_on_meta'] ) {
			return false;
		}

		$params = array( $meta_id );

		if ( ! empty( $meta['filter_callback'] ) ) {
			return call_user_func_array( $meta['filter_callback'], $params );
		}

		if ( ! empty( $meta['callback'] ) ) {
			$value = call_user_func_array( $meta['callback'], $params );
		}

		$value = empty( $value ) ? $meta['meta_value'] : $value;

		// if we have a value let's build the html template
		$html = ! empty( $value ) ? Tribe__Events__Meta_Factory::template( $meta['label'], $value, $meta_id ) : '';

		return apply_filters( 'tribe_get_meta', $html, $meta_id );
	}

	/**
	 * Get the args for a meta object.
	 *
	 * @param        $meta_id
	 * @param        $arg_key
	 * @param string $type
	 *
	 * @return bool
	 */
	function tribe_get_meta_arg( $meta_id, $arg_key, $type = 'meta' ) {

		// die silently if the requested meta group is not registered
		if ( ! Tribe__Events__Meta_Factory::check_exists( $meta_id, $type ) ) {
			return false;
		}

		$args = Tribe__Events__Meta_Factory::get_args( $meta_id, $type );

		// check if the arg exists
		if ( isset( $args[ $arg_key ] ) ) {
			return $args[ $arg_key ];
		} else {
			return false;
		}
	}

	/**
	 * Get the template part for the meta object.
	 *
	 * @param        $meta_id
	 * @param        $template_key
	 * @param string $type
	 *
	 * @return bool
	 */
	function tribe_get_meta_template_part( $meta_id, $template_key, $type = 'meta' ) {

		// die silently if the requested meta group is not registered
		if ( ! Tribe__Events__Meta_Factory::check_exists( $meta_id, $type ) ) {
			return false;
		}

		$template = tribe_get_meta_arg( $meta_id, 'wrap', $type );

		if ( isset( $template[ $template_key ] ) ) {
			return $template[ $template_key ];
		} else {
			return false;
		}
	}

	/**
	 * Set the visibility of the meta object.
	 *
	 * @param        $meta_id
	 * @param bool   $status
	 * @param string $type
	 */
	function tribe_set_the_meta_visibility( $meta_id, $status = true, $type = 'meta' ) {
		Tribe__Events__Meta_Factory::set_visibility( $meta_id, $type, $status );
	}

	/**
	 * Set the template for the meta object.
	 *
	 * @param        $meta_id
	 * @param array  $template
	 * @param string $type
	 *
	 * @return bool
	 */
	function tribe_set_the_meta_template( $meta_id, $template = array(), $type = 'meta' ) {
		if ( is_array( $meta_id ) ) {
			foreach ( $meta_id as $id ) {
				tribe_set_the_meta_template( $id, $template, $type );
			}
		} else {
			global $_tribe_meta_factory;

			// die silently if the requested meta group is not registered
			if ( ! Tribe__Events__Meta_Factory::check_exists( $meta_id, $type ) ) {
				return false;
			}

			if ( ! empty( $template ) ) {
				$_tribe_meta_factory->{$type}[ $meta_id ]['wrap'] = wp_parse_args( $template, $_tribe_meta_factory->{$type}[ $meta_id ]['wrap'] );
			}
		}

	}

	/**
	 * Set the meta priority to manage positioning.
	 *
	 * @param        $meta_id
	 * @param int    $priority
	 * @param string $type
	 *
	 * @return bool
	 */
	function tribe_set_meta_priority( $meta_id, $priority = 100, $type = 'meta' ) {
		if ( is_array( $meta_id ) ) {
			foreach ( $meta_id as $id => $priority ) {
				tribe_set_meta_priority( $id, $priority, $type );
			}
		} else {
			global $_tribe_meta_factory;

			// die silently if the requested meta group is not registered
			if ( ! Tribe__Events__Meta_Factory::check_exists( $meta_id, $type ) ) {
				return false;
			}

			if ( ! empty( $priority ) ) {
				$_tribe_meta_factory->{$type}[ $meta_id ]['priority'] = $priority;
			}
		}
	}

	/**
	 * Set meta value for meta object.
	 *
	 * @param        $meta_id
	 * @param        $value
	 * @param string $value_type
	 * @param string $type
	 *
	 * @return bool
	 */
	function tribe_set_meta_value( $meta_id, $value, $value_type = 'meta_value', $type = 'meta' ) {
		if ( is_array( $meta_id ) ) {
			foreach ( $meta_id as $id ) {
				tribe_set_meta_value( $id, $value, $value_type, $type );
			}
		} else {
			global $_tribe_meta_factory;

			// die silently if the requested meta group is not registered
			if ( ! Tribe__Events__Meta_Factory::check_exists( $meta_id, $type ) ) {
				return false;
			}

			$_tribe_meta_factory->{$type}[ $meta_id ][ $value_type ] = $value;
		}
	}

	/**
	 * Set the meta label for the meta object.
	 *
	 * @param        $meta_id
	 * @param string $label
	 * @param string $type
	 *
	 * @return bool
	 */
	function tribe_set_meta_label( $meta_id, $label = '', $type = 'meta' ) {
		if ( is_array( $meta_id ) ) {
			foreach ( $meta_id as $id => $label ) {
				tribe_set_meta_label( $id, $label, $type );
			}
		} else {
			global $_tribe_meta_factory;

			// die silently if the requested meta group is not registered
			if ( ! Tribe__Events__Meta_Factory::check_exists( $meta_id, $type ) ) {
				return false;
			}

			$_tribe_meta_factory->{$type}[ $meta_id ]['label'] = $label;
		}
	}

	/**
	 * Get the event meta
	 *
	 * @return mixed|void
	 */
	function tribe_get_the_event_meta() {
		$html = '';
		foreach ( Tribe__Events__Meta_Factory::get_order() as $meta_groups ) {
			foreach ( $meta_groups as $meta_group_id ) {
				$html .= tribe_get_meta_group( $meta_group_id, true );
			}
		}

		return apply_filters( 'tribe_get_the_event_meta', $html );
	}

	/**
	 * Simple display of meta group tag
	 *
	 * @uses tribe_get_meta_group()
	 * @return echo tribe_get_meta_group( $meta_group_id )
	 */
	function tribe_display_the_event_meta() {
		echo apply_filters( 'tribe_display_the_event_meta', tribe_get_the_event_meta() );
	}

	/**
	 *  Simple diplay of meta group tag
	 *
	 * @uses tribe_get_meta_group()
	 *
	 * @param string $meta_group_id
	 *
	 * @return echo tribe_get_meta_group( $meta_group_id )
	 */
	function tribe_display_meta_group( $meta_group_id ) {
		echo apply_filters( 'tribe_display_meta_group', tribe_get_meta_group( $meta_group_id ) );
	}

	/**
	 *  Simple diplay of meta tag
	 *
	 * @uses tribe_get_meta()
	 *
	 * @param string $meta_id
	 *
	 * @return echo tribe_get_meta( $meta_id )
	 */
	function tribe_display_meta( $meta_id ) {
		echo apply_filters( 'tribe_display_meta', tribe_get_meta( $meta_id ) );
	}

	/**
	 * Utility function to compile separated lists.
	 *
	 * @param string $body
	 * @param string $separator
	 * @param string $field
	 *
	 * @return string
	 */
	function tribe_separated_field( $body, $separator, $field ) {
		$body_and_separator = $body ? $body . $separator : $body;

		return $field ? $body_and_separator . $field : $body;
	}
}
