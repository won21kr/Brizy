<?php

class Brizy_Editor_Asset_Crop_FastService implements Brizy_Editor_Asset_Crop_ServiceInterface {

	private $actions = [];
	private $sourcePath;
	private $targetPath;

	/**
	 * Brizy_Editor_Asset_Crop_FastService constructor.
	 *
	 * @param $sourcePath
	 * @param $targetPath
	 *
	 * @throws Exception
	 */
	public function __construct( $sourcePath, $targetPath ) {

		if ( ! file_exists( $sourcePath ) ) {
			throw new Exception( 'Unable to crop media. Source file not found.' );
		}

		$this->sourcePath  = $sourcePath;
		$this->targetPath  = $targetPath;
	}

	/**
	 * @param int $offsetX
	 * @param int $offsetY
	 * @param int $width
	 * @param int $height
	 *
	 * @return bool
	 */
	public function crop( $offsetX, $offsetY, $width, $height ) {

		$this->actions['crop'] = [ $width, $height, $offsetX, $offsetY ];

		return true;
	}

	/**
	 * @param int $width
	 * @param int $height
	 *
	 * @return bool
	 */
	public function resize( $width, $height ) {

		$this->actions['resize'] = [ $width, $height ];

		return true;
	}

	/**
	 * @return bool
	 */
	public function saveTargetImage() {
		$args = [
			'imgSource' => $this->sourcePath,
			'actions'   => $this->actions
		];

		$call = wp_remote_post( 'http://brizy.local/?brz-fast', [
				//'blocking'  => true,
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'body'    => wp_json_encode( $args )
			]
		);
		echo '<pre style="background:#23282d;z-index:99999999;color:#78FF5B;font-size:14px;position:relative;">';
		print_r( wp_remote_retrieve_body( $call ) );
		echo '</pre>';
		die();
		$body = json_decode( wp_remote_retrieve_body( $call ), true );

		if ( is_wp_error( $call ) || 200 !== wp_remote_retrieve_response_code( $call ) || empty( $body ) || isset( $body['error'] ) || empty( $body['img'] ) ) {
			return false;
		}

		$saveImg = file_put_contents( $this->targetPath, $body['img'] );

		return (bool) $saveImg;
	}

}