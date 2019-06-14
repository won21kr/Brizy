<?php


class Brizy_Admin_Fonts_Manager {


	/**
	 * Get all fonts as arrays
	 *
	 * @return array
	 */
	public function getAllFonts() {
		global $wpdb;

		$fonts = get_posts( array(
			'post_type'   => Brizy_Admin_Fonts_Main::CP_FONT,
			'post_status' => 'publish',
			'numberposts' => - 1,
			'orderby'     => 'ID',
			'order'       => 'ASC',
		) );

		$result = array();

		foreach ( $fonts as $font ) {

			$weights = $wpdb->get_results( $wpdb->prepare(
				"SELECT m.meta_value FROM {$wpdb->posts} p 
						JOIN {$wpdb->postmeta} m ON  m.post_id=p.ID  && m.meta_key='brizy-font-weight'
						WHERE p.post_parent=%d
						",
				array( $font->ID ) ), ARRAY_A
			);

			$result[] = array(
				'family'  => $font->post_title,
				'type'    => get_post_meta( $font->ID, 'brizy-font-type', true ),
				'weights' => array_map( function ( $v ) {
					return $v['meta_value'];
				}, $weights )
			);
		}

		return $result;
	}

	/**
	 * @param $family
	 * @param $fontType
	 *
	 * @return array|null
	 */
	public function getFont( $family, $fontType ) {

		global $wpdb;

		$fonts = get_posts( array(
			'title'       => $family,
			'post_type'   => Brizy_Admin_Fonts_Main::CP_FONT,
			'post_status' => 'publish',
			'meta_key'    => 'brizy-font-type',
			'meta_value'  => $fontType,
			'numberposts' => - 1,
			'orderby'     => 'ID',
			'order'       => 'DESC',
		) );

		if ( isset( $fonts[0] ) ) {
			$font = $fonts[0];
		} else {
			return null;
		}

		$weights = $wpdb->get_results( $wpdb->prepare(
			"SELECT m.meta_value FROM {$wpdb->posts} p 
						JOIN {$wpdb->postmeta} m ON  m.post_id=p.ID && p.post_parent=%d && m.meta_key='brizy-font-weight'",
			array( $font->ID ) ), ARRAY_A
		);


		return array(
			'family'  => $font->post_title,
			'type'    => get_post_meta( $font->ID, 'brizy-font-type', true ),
			'weights' => array_map( function ( $v ) {
				return $v['meta_value'];
			}, $weights )
		);
	}

	/**
	 * @param $family
	 * @param $fontWeights
	 * @param $fontType
	 *
	 * @return int
	 * @throws Exception
	 */
	public function createFont( $family, $fontWeights, $fontType ) {
		global $wpdb;

		if ( $family == '' ) {
			throw new Exception( 'Invalid font family' );
		}

		if ( $fontType == '' ) {
			throw new Exception( 'Invalid font type' );
		}

		if ( ! is_array( $fontWeights ) || empty( $fontWeights ) ) {
			throw new Exception( 'Invalid font weights' );
		}

		$font = $this->getFont( $family, $fontType );

		if ( $font ) {
			throw new Exception( 'This font already exists' );
		}

		// Need to require these files
		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
		}

		try {
			$wpdb->query( 'START TRANSACTION' );
			// create font post
			$fontId = wp_insert_post( [
				'post_title'  => $family,
				'post_name'   => $family,
				'post_type'   => Brizy_Admin_Fonts_Main::CP_FONT,
				'post_status' => 'publish',

			] );

			if ( ! $fontId || is_wp_error( $fontId ) ) {

				throw new Exception( 'Unable to create font' );
			}

			$uid = md5( $fontId . time() );
			update_post_meta( $fontId, 'brizy_post_uid', $uid );
			update_post_meta( $fontId, 'brizy-font-type', $fontType );

			// create font attachments
			foreach ( $fontWeights as $weight => $weightType ) {

				if ( count( $weightType ) == 0 ) {
					throw new Exception( 'No font files provided' );
				}

				foreach ( $weightType as $type => $file ) {

					$id = media_handle_sideload( $file, $fontId, "Attached font file" );

					if ( ! $id || is_wp_error( $id ) ) {
						throw new Exception( 'Unable to handle font sideload' );
					}

					update_post_meta( $id, 'brizy-font-weight', $weight );
					update_post_meta( $id, 'brizy-font-file-type', $type );
				}
			}

			$wpdb->query( 'COMMIT' );

			return (int) $fontId;

		} catch ( Exception $e ) {
			$wpdb->query( 'ROLLBACK' );
			Brizy_Logger::instance()->debug( 'Create font ERROR', [ $e ] );
			throw new Exception( 'Unable to create font' );
		}
	}

	/**
	 * @param $family
	 * @param $fontType
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function deleteFont( $family, $fontType ) {
		global $wpdb;

		if ( $family == '' ) {
			throw new Exception( 'Invalid font family' );
		}

		if ( $fontType == '' ) {
			throw new Exception( 'Invalid font type' );
		}

		$font = get_posts( [
			'post_type'   => Brizy_Admin_Fonts_Main::CP_FONT,
			'title'  => $family,
			'meta_query' => array(
				array(
					'key' => 'brizy-font-type',
					'value' => $fontType
				)
			),
			'posts_per_page' => -1,
			'orderby'     => 'ID',
			'order'       => 'DESC',
		] );

		if ( count( $font ) > 0 ) {
			$font = $font[0];
		} else {
			$font = null;
		}

		if ( ! $font ) {
			throw new Exception( 'Font not found', 404 );
		}


		try {
			$wpdb->query( 'START TRANSACTION ' );
			// delete all attachments first

			$attachments = get_attached_media( '', $font->ID );

			foreach ( $attachments as $attachment ) {
				wp_delete_attachment( $attachment->ID, 'true' );
			}

			// delete font
			wp_delete_post( $font->ID );

			$wpdb->query( 'COMMIT' );

		} catch ( Exception $e ) {
			$wpdb->query( 'ROLLBACK' );
			Brizy_Logger::instance()->debug( 'Delete font ERROR', [ $e ] );
			throw new Exception( 'Failed to delete font', 400 );
		}

		return true;
	}
}