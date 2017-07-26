<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

class Brizy_Editor_CompiledHtml {

	/**
	 * @var Brizy_Editor_Helper_Dom
	 */
	private $dom;

	public function __construct( $html ) {

		$this->dom = new Brizy_Editor_Helper_Dom( $html );
	}

	public function strip_regions() {
		$this->dom->strip_regions();

		return $this;
	}

	public function get_content() {
		return $this->strip_regions()->dom->get_content();
	}

	public function get_body() {
		return $this->dom->get_body()->get_content();
	}

	public function get_head_scripts() {
		$script_tags = $this->dom->get_head()->get_scripts();

		return $this->dom->get_attributes( $script_tags, 'src' );
	}

	public function get_footer_scripts() {
		$script_tags = $this->dom->get_body()->get_scripts();

		return $this->dom->get_attributes( $script_tags, 'src' );
	}


//	public function get_scripts() {
//
//		$script_tags = $this->dom->get_tags( '/(<script(.*?)<\/script>)/is' );
//		$tags        = array();
//
//		if ( is_array( $script_tags ) ) {
//			foreach ( $script_tags as $tag ) {
//				$ignore = $tag->get_attr( 'ignore' );
//
//				if ( $ignore == 1 ) {
//					continue;
//				}
//
//				$tags[] = $tag;
//			}
//		}
//
//		return $this->dom->get_attributes( $tags, 'src' );
//	}

	public function get_links_tags() {
		$link_tags = $this->dom->get_head()->get_links();

		return $link_tags;

		return $this->dom->get_attributes( $link_tags, 'href' );
	}


	public function get_styles() {
		$style_tags = $this->dom->get_styles();
		$list       = array();

		if ( is_array( $style_tags ) ) {
			foreach ( $style_tags as $tag ) {

				$content = $tag->get_content();
				$list[]  = $content;
			}
		}

		return $list;
	}

}