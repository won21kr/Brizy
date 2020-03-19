<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

class Brizy_Editor_Post3 extends Brizy_Admin_Serializable {


	use Brizy_Editor_AutoSaveAware;

	const BRIZY_POST = 'brizy-post';
	const BRIZY_POST_NEEDS_COMPILE_KEY = 'brizy-need-compile';
	const BRIZY_POST_SIGNATURE_KEY = 'brizy-post-signature';
	const BRIZY_POST_HASH_KEY = 'brizy-post-hash';
	const BRIZY_POST_EDITOR_VERSION = 'brizy-post-editor-version';
	const BRIZY_POST_COMPILER_VERSION = 'brizy-post-compiler-version';
	const BRIZY_POST_PLUGIN_VERSION = 'brizy-post-plugin-version';

	static protected $instance = null;

	/**
	 * @var Brizy_Editor_API_Page
	 */
	protected $api_page;

	/**
	 * @var int
	 */
	protected $wp_post_id;

	/**
	 * @var WP_Post
	 */
	protected $wp_post;

	/**
	 * @var string
	 */
	protected $compiled_html;

	/**
	 * @var string
	 */
	protected $compiled_html_body;

	/**
	 * @var string
	 */
	protected $compiled_html_head;

	/**
	 * @var bool
	 */
	protected $needs_compile;

	/**
	 * Json for the editor.
	 *
	 * @var string
	 */
	protected $editor_data;

	/**
	 * @var string
	 */
	protected $uid;

	/**
	 * @var bool
	 */
	protected $uses_editor;

	/**
	 * @var string
	 */
	protected $editor_version;

	/**
	 * @var string
	 */
	protected $compiler_version;

	/**
	 * @var string
	 */
	protected $plugin_version;

	/**
	 * @var Brizy_Editor_CompiledHtml
	 */
	static private $compiled_page;


	public static function cleanClassCache() {
		self::$instance = array();
	}

	/**
	 * Brizy_Editor_Post constructor.
	 *
	 * @param $wp_post_id
	 *
	 * @throws Brizy_Editor_Exceptions_NotFound
	 */
	public function __construct( $wp_post_id ) {

		self::checkIfPostTypeIsSupported( $wp_post_id );
		$this->wp_post_id = (int) $wp_post_id;

		if ( $this->wp_post_id ) {
			$this->wp_post = get_post( $this->wp_post_id );
		}

		// get the storage values
		$storage = $this->storage();
		//$storageData          = $storage->get_storage();
		$using_editor_old = $storage->get( Brizy_Editor_Constants::USES_BRIZY, false );
		$storage_post     = $storage->get( self::BRIZY_POST, false );

		// check for deprecated forms of posts
		if ( $storage_post instanceof self ) {
			$this->set_editor_data( $storage_post->editor_data );
			$this->set_needs_compile( true );
			$this->set_uses_editor( $using_editor_old );
			$this->save();
		} else if ( is_array( $storage_post ) ) {
			$this->loadStorageData( $storage_post );
		}

		// check if the old flag is set
		if ( ! is_null( $using_editor_old ) ) {
			$this->uses_editor = (bool) $using_editor_old;
			$storage->delete( Brizy_Editor_Constants::USES_BRIZY );
			$this->save();
		}

		if ( $this->uses_editor() ) {
			$this->create_uid();
		}
	}

	/**
	 * @param $apost
	 *
	 * @return Brizy_Editor_Post|null
	 * @throws Brizy_Editor_Exceptions_NotFound
	 */
	public static function get( $apost ) {

		$wp_post_id = $apost;
		if ( $apost instanceof WP_Post ) {
			$wp_post_id = $apost->ID;
		}

		if ( isset( self::$instance[ $wp_post_id ] ) ) {
			return self::$instance[ $wp_post_id ];
		}

		return self::$instance[ $wp_post_id ] = new self( $wp_post_id );

	}

	public static function getAutoSavePost( $postId, $userId ) {
		$postParentId = self::getPostParent( $postId );
		$autosave     = wp_get_post_autosave( $postParentId, $userId );

		if ( ! $autosave ) {
			return;
		}

		$post = get_post( $postId );

		$postDate     = new DateTime( $post->post_modified );
		$autosaveDate = new DateTime( $autosave->post_modified );

		if ( $postDate > $autosaveDate ) {
			return null;
		}

		return $autosave->ID;
	}

	/**
	 * @param $wp_post_id
	 * @param bool $throw
	 *
	 * @return bool
	 * @throws Brizy_Editor_Exceptions_UnsupportedPostType
	 */
	public static function checkIfPostTypeIsSupported( $wp_post_id, $throw = true ) {
		$type = get_post_type( $wp_post_id );

		$supported_post_types   = Brizy_Editor::get()->supported_post_types();
		$supported_post_types[] = 'revision';

		if ( ! in_array( $type, $supported_post_types ) ) {

			if ( $throw ) {
				throw new Brizy_Editor_Exceptions_UnsupportedPostType(
					"Brizy editor doesn't support '{$type}' post type"
				);
			} else {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return string
	 */
	public function serialize() {
		$get_object_vars = get_object_vars( $this );

		unset( $get_object_vars['wp_post_id'] );
		unset( $get_object_vars['wp_post'] );
		unset( $get_object_vars['api_page'] );
		unset( $get_object_vars['store_assets'] );
		unset( $get_object_vars['assets'] );

		return serialize( $get_object_vars );
	}

	/**
	 * @param $data
	 */
	public function unserialize( $data ) {
		parent::unserialize( $data ); // TODO: Change the autogenerated stub

		if ( $this->get_api_page() ) {
			$save_data = $this->get_api_page()->get_content();
			$this->set_editor_data( $save_data );
		}

		unset( $this->api_page );
	}

	public function convertToOptionValue() {

		return array(
			'compiled_html'                    => $this->get_encoded_compiled_html(),
			'compiled_html_body'               => $this->get_compiled_html_body(),
			'compiled_html_head'               => $this->get_compiled_html_head(),
			'editor_version'                   => $this->editor_version,
			'compiler_version'                 => $this->compiler_version,
			'plugin_version'                   => $this->plugin_version,
			'editor_data'                      => $this->editor_data,
			Brizy_Editor_Constants::USES_BRIZY => $this->uses_editor
		);
	}

	public function loadStorageData( $data ) {
		if ( isset( $data['compiled_html'] ) ) {
			$this->set_encoded_compiled_html( $data['compiled_html'] );
		}

		$this->set_editor_data( $data['editor_data'] );
		$data_needs_compile       = isset( $data['needs_compile'] ) ? $data['needs_compile'] : true;
		$this->needs_compile      = metadata_exists( 'post', $this->wp_post_id, self::BRIZY_POST_NEEDS_COMPILE_KEY ) ? (bool) get_post_meta( $this->wp_post_id, self::BRIZY_POST_NEEDS_COMPILE_KEY, true ) : $data_needs_compile;
		$this->editor_version     = isset( $data['editor_version'] ) ? $data['editor_version'] : null;
		$this->compiler_version   = isset( $data['compiler_version'] ) ? $data['compiler_version'] : null;
		$this->plugin_version     = isset( $data['plugin_version'] ) ? $data['plugin_version'] : null;
		$this->compiled_html_head = isset( $data['compiled_html_head'] ) ? $data['compiled_html_head'] : null;
		$this->compiled_html_body = isset( $data['compiled_html_body'] ) ? $data['compiled_html_body'] : null;
		$this->uses_editor        = (bool) ( isset( $data[ Brizy_Editor_Constants::USES_BRIZY ] ) ? $data[ Brizy_Editor_Constants::USES_BRIZY ] : false );
	}

	/**
	 *  Mark all brizy post that needs compile
	 */
	public static function mark_all_for_compilation() {
		global $wpdb;
		$wpdb->update( $wpdb->postmeta, array( 'meta_value' => 1 ), array( 'meta_key' => self::BRIZY_POST_NEEDS_COMPILE_KEY ) );
	}

//	/**
//	 * @return Brizy_Editor_Post[]
//	 * @throws Brizy_Editor_Exceptions_NotFound
//	 * @throws Brizy_Editor_Exceptions_UnsupportedPostType
//	 * @todo: We need to move this method from here
//	 */
//	public static function foreach_brizy_post( $callback ) {
//		global $wpdb;
//		$posts = $wpdb->get_results(
//			$wpdb->prepare( "SELECT p.post_type, p.ID as post_id FROM {$wpdb->postmeta} pm
//									JOIN {$wpdb->posts} p ON p.ID=pm.post_id and p.post_type <> 'revision' and p.post_type<>'attachment'
//									WHERE pm.meta_key = %s ", Brizy_Editor_Storage_Post::META_KEY )
//		);
//
//		$result = array();
//		foreach ( $posts as $p ) {
//			if ( in_array( $p->post_type, Brizy_Editor::get()->supported_post_types() ) ) {
//
//				if ( is_callable( $callback ) ) {
//					$callback( $p );
//				}
//			}
//		}
//
//		return $result;
//	}

	/**
	 * @return Brizy_Editor_Post[]
	 * @throws Brizy_Editor_Exceptions_NotFound
	 * @throws Brizy_Editor_Exceptions_UnsupportedPostType
	 */
	public static function get_all_brizy_post_ids() {
		global $wpdb;
		$posts = $wpdb->get_results(
			$wpdb->prepare( "SELECT p.ID FROM {$wpdb->postmeta} pm 
									JOIN {$wpdb->posts} p ON p.ID=pm.post_id and p.post_type <> 'revision'  and p.post_type<>'attachment'
									WHERE pm.meta_key = %s ", Brizy_Editor_Storage_Post::META_KEY )
		);

		return array_map( function ( $o ) {
			return (int) $o->ID;
		}, $posts );
	}

	/**
	 * @param $project
	 * @param $post
	 *
	 * @return Brizy_Editor_Post
	 * @throws Brizy_Editor_Exceptions_UnsupportedPostType
	 * @throws Exception
	 */
	public static function create( $project, $post ) {
		if ( ! in_array( ( $type = get_post_type( $post->ID ) ), Brizy_Editor::get()->supported_post_types() ) ) {
			throw new Brizy_Editor_Exceptions_UnsupportedPostType(
				"Brizy editor doesn't support '$type' post type 2"
			);
		}
		Brizy_Logger::instance()->notice( 'Create post', array( $project, $post ) );

		$post = new self( $post->ID );
		$post->set_plugin_version( BRIZY_VERSION );

		return $post;
	}

	private function getLastAutosave( $postParentId, $user_id ) {
		global $wpdb;

		$postParentId = (int) $postParentId;
		$user_id      = (int) $user_id;

		$query = sprintf( "SELECT ID FROM {$wpdb->posts} WHERE  post_parent = %d AND post_type= 'revision' AND post_status= 'inherit'AND post_name LIKE '%d-autosave%%'", $postParentId, $postParentId );

		if ( is_integer( $user_id ) ) {
			$query .= " AND post_author={$user_id}";
		}

		$query .= " ORDER BY post_date DESC";

		return (int) $wpdb->get_var( $query );

	}

//	public function auto_save_post_deperecated() {
//		try {
//			$user_id                   = get_current_user_id();
//			$post                      = $this->getWpPost();
//			$postParentId              = $this->get_parent_id();
//			$old_autosave              = $this->get_last_autosave( $postParentId, $user_id );
//			$post_data                 = get_object_vars( $post );
//			$post_data['post_content'] .= "\n<!-- " . time() . "-->";
//			$autosavePost              = null;
//
//			if ( $old_autosave ) {
//				$autosavePost = self::get( $old_autosave );
//			}
//
//			if ( $old_autosave ) {
//				$new_autosave                = _wp_post_revision_data( $post_data, true );
//				$new_autosave['ID']          = $old_autosave;
//				$new_autosave['post_author'] = $user_id;
//
//				// If the new autosave has the same content as the post, delete the autosave.
//				$autosave_is_different = false;
//
//				foreach ( array_intersect( array_keys( $new_autosave ), array_keys( _wp_post_revision_fields( $post ) ) ) as $field ) {
//					if ( normalize_whitespace( $new_autosave[ $field ] ) != normalize_whitespace( $post->$field ) ) {
//						$autosave_is_different = true;
//						break;
//					}
//				}
//
//				if ( ! $autosave_is_different ) {
//					wp_delete_post_revision( $old_autosave );
//
//					return new WP_Error( 'rest_autosave_no_changes', __( 'There is nothing to save. The autosave and the post content are the same.' ), array( 'status' => 400 ) );
//				}
//
//				/**
//				 * This filter is documented in wp-admin/post.php.
//				 */
//				do_action( 'wp_creating_autosave', $new_autosave );
//
//				// wp_update_post expects escaped array.
//				wp_update_post( wp_slash( $new_autosave ) );
//
//			} else {
//				// Create the new autosave as a special post revision.
//				$revId        = _wp_put_post_revision( $post_data, true );
//				$autosavePost = self::get( $revId );
//			}
//
//			$autosavePost = $this->populateAutoSavedData( $autosavePost );
//			$autosavePost->save();
//
//		} catch ( Exception $exception ) {
//			Brizy_Logger::instance()->exception( $exception );
//
//			return false;
//		}
//	}

	public function save_wp_post() {

		$post_type        = $this->get_wp_post()->post_type;
		$post_type_object = get_post_type_object( $post_type );

		if ( ! $post_type_object ) {
			Brizy_Logger::instance()->critical( 'Invalid post type provided on save_wp_post', [ 'post_type' => $post_type ] );
			return;
		}

		$can_publish = current_user_can( $post_type_object->cap->publish_posts );
		$post_status = $can_publish ? 'publish' : 'pending';

		$brizy_compiled_page = $this->get_compiled_page();

		$this->deleteOldAutoSaves( $this->get_parent_id() );

		$body = $brizy_compiled_page->get_body();


		wp_update_post( array(
			'ID'           => $this->get_parent_id(),
			'post_status'  => $post_status,
			'post_content' => $body
		) );
	}


	/**
	 * This saves ony data.. it does not touch the wordpress post
	 *
	 *
	 * @return bool
	 */
	public function save( $autosave = 0 ) {

		try {

			if ( $autosave == 0 ) {
				$value = $this->convertToOptionValue();
				$this->storage()->set( self::BRIZY_POST, $value );
			} else {
				$this->auto_save_post( $this->get_wp_post(), function ( $autosaveObject ) {
					$autosavePost = $this->populateAutoSavedData( $autosaveObject );
					$autosavePost->save();
				} );
			}

		} catch ( Exception $exception ) {
			Brizy_Logger::instance()->exception( $exception );

			return false;
		}
	}


	/**
	 * @return bool
	 * @throws Brizy_Editor_Exceptions_ServiceUnavailable
	 * @throws Exception
	 */
	public function compile_page() {

		Brizy_Logger::instance()->notice( 'Compile page', array( $this ) );

		$compiled_html = Brizy_Editor_User::get()->compile_page( Brizy_Editor_Project::get(), $this );
		$compiled_html = Brizy_SiteUrlReplacer::hideSiteUrl( $compiled_html );

		$this->set_compiled_html( $compiled_html );

		$this->set_compiled_html_head( null );
		$this->set_compiled_html_body( null );

		$this->set_needs_compile( false );
		$this->set_compiler_version( BRIZY_EDITOR_VERSION );

		return true;
	}

	/**
	 * @return Brizy_Editor_CompiledHtml
	 */
	public function get_compiled_page() {

		if ( self::$compiled_page ) {
			return self::$compiled_page;
		}

		return new Brizy_Editor_CompiledHtml( $this->get_compiled_html() );
	}

	public function isCompiledWithCurrentVersion() {
		return $this->get_compiler_version() === BRIZY_EDITOR_VERSION;
	}

	/**
	 * @deprecated;
	 */
	public function get_api_page() {

		if ( isset( $this->api_page ) ) {
			return $this->api_page;
		}

		return null;
	}

	/**
	 * @return mixed
	 */
	public function get_id() {
		return $this->wp_post_id;
	}

	/**
	 * A unique id assigned when brizy is enabled for this post
	 *
	 * @return string
	 */
	public function create_uid() {

		if ( $this->uid ) {
			return $this->uid;
		}

		$this->uid = get_post_meta( $this->get_parent_id(), 'brizy_post_uid', true );

		if ( ! $this->uid ) {
			$this->uid = md5( $this->get_parent_id() . time() );
			update_post_meta( $this->get_parent_id(), 'brizy_post_uid', $this->uid );
		}

		return $this->uid;
	}

	/**
	 * @return string
	 */
	public function get_uid() {
		return $this->uid;
	}

	/**
	 * @return string
	 */
	public function get_editor_data() {

		if ( ( $decodedData = base64_decode( $this->editor_data, true ) ) !== false ) {
			return $decodedData;
		}

		return $this->editor_data;
	}

	/**
	 * @param $content
	 *
	 * @return $this
	 */
	public function set_editor_data( $content ) {

		if ( base64_decode( $content, true ) !== false ) {
			$this->editor_data = $content;
		} else {
			$this->editor_data = base64_encode( $content );
		}

		return $this;
	}

	/**
	 * @return false|int|mixed
	 */
	public function get_parent_id() {
		return self::getPostParent( $this->get_id() );
	}

	protected static function getPostParent( $postId ) {
		$id = wp_is_post_revision( $postId );

		if ( ! $id ) {
			$id = $postId;
		}

		return $id;
	}

	/**
	 * @return string
	 */
	public function get_compiled_html() {
		return $this->compiled_html;
	}

	/**
	 * @param string $compiled_html
	 *
	 * @return Brizy_Editor_Post
	 */
	public function set_compiled_html( $compiled_html ) {
		$this->compiled_html = $compiled_html;

		return $this;
	}


	/**
	 * @param $compiled_html
	 *
	 * @return $this
	 */
	public function set_encoded_compiled_html( $compiled_html ) {

		if ( ( $decodedData = base64_decode( $compiled_html, true ) ) !== false ) {
			$this->set_compiled_html( $decodedData );
		} else {
			$this->set_compiled_html( $compiled_html );
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_encoded_compiled_html() {

		return base64_encode( $this->get_compiled_html() );
	}

	/**
	 * @return string
	 * @deprecated use get_compiled_html
	 */
	public function get_compiled_html_body() {
		return $this->compiled_html_body;
	}

	/**
	 * @return string
	 * @deprecated use get_compiled_html
	 */
	public function get_compiled_html_head() {
		return $this->compiled_html_head;
	}

	/**
	 * @param $html
	 *
	 * @return $this
	 * @deprecated use set_compiled_html
	 *
	 */
	public function set_compiled_html_body( $html ) {
		$this->compiled_html_body = $html;

		return $this;
	}

	/**
	 * @param $html
	 *
	 * @return $this
	 * @deprecated use set_compiled_html
	 *
	 */
	public function set_compiled_html_head( $html ) {
		// remove all title and meta tags.
		$this->compiled_html_head = $html;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function can_edit_posts() {
		return current_user_can( "edit_posts" );
	}

	/**
	 * @return $this
	 * @throws Brizy_Editor_Exceptions_AccessDenied
	 */
	public function enable_editor() {
		if ( ! $this->can_edit_posts() ) {
			throw new Brizy_Editor_Exceptions_AccessDenied( 'Current user cannot edit page' );
		}
		$this->uses_editor = true;

		return $this;
	}

	/**
	 *
	 */
	public function disable_editor() {
		$this->uses_editor = false;

		return $this;
	}

	/**
	 * @return Brizy_Editor_Storage_Post
	 */
	public function storage() {
		return Brizy_Editor_Storage_Post::instance( $this->wp_post_id );
	}

	/**
	 * @return array|null|WP_Post
	 */
	public function get_wp_post() {
		return $this->wp_post;
	}

	/**
	 * @return bool
	 */
	public function uses_editor() {
		return $this->uses_editor;
	}

	/**
	 * @param $val
	 *
	 * @return $this
	 */
	public function set_uses_editor( $val ) {
		$this->uses_editor = $val;

		return $this;
	}


	/**
	 * @return string
	 */
	public function edit_url() {
		return add_query_arg(
			array( Brizy_Editor::prefix('-edit') => '' ),
			get_permalink( $this->get_parent_id() )
		);
	}

	/**
	 * @param $text
	 * @param string $tags
	 * @param bool $invert
	 *
	 * @return null|string|string[]
	 * @todo: We need to move this method from here
	 *
	 */
	function strip_tags_content( $text, $tags = '', $invert = false ) {

		preg_match_all( '/<(.+?)[\s]*\/?[\s]*>/si', trim( $tags ), $tags );
		$tags = array_unique( $tags[1] );

		if ( is_array( $tags ) AND count( $tags ) > 0 ) {
			if ( $invert == false ) {
				return preg_replace( '@<(?!(?:' . implode( '|', $tags ) . ')\b)(\w+)\b.*?>(.*?</\1>)?@si', '', $text );
			} else {
				return preg_replace( '@<(' . implode( '|', $tags ) . ')\b.*?>(.*?</\1>)?@si', '', $text );
			}
		} elseif ( $invert == false ) {
			return preg_replace( '@<(\w+)\b.*?>.*?</\1>@si', '', $text );
		}

		return $text;
	}

	/**
	 * @return array
	 */
	public function get_templates() {

		$type      = get_post_type( $this->get_id() );
		$templates = array(
			array(
				'id'    => '',
				'title' => __( 'Default', 'brizy' )
			)
		);

		foreach ( wp_get_theme()->get_page_templates( null, $type ) as $key => $title ) {
			$templates[] = [
				'id'    => $key,
				'title' => $title
			];
		}

		return apply_filters( "brizy:$type:templates", $templates );
	}

	/**
	 * @param string $aTemplate
	 *
	 * @return $this
	 */
	public function set_template( $aTemplate ) {

		$aTemplate = apply_filters( 'brizy_post_template', $aTemplate );

		if ( $aTemplate == '' ) {
			delete_post_meta( $this->get_id(), '_wp_page_template' );
		} else {
			update_post_meta( $this->get_id(), '_wp_page_template', $aTemplate );
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_template() {
		return get_post_meta( $this->get_id(), '_wp_page_template', true );
	}

	/**
	 * @param string $editor_version
	 */
	public function set_editor_version( $editor_version ) {
		$this->editor_version = $editor_version;
		update_metadata( 'post', $this->wp_post_id, self::BRIZY_POST_EDITOR_VERSION, $editor_version );
	}

	/**
	 * @param string $compiler_version
	 */
	public function set_compiler_version( $compiler_version ) {
		$this->compiler_version = $compiler_version;
		update_metadata( 'post', $this->wp_post_id, self::BRIZY_POST_COMPILER_VERSION, $compiler_version );
	}

	/**
	 * @param string $plugin_version
	 */
	public function set_plugin_version( $plugin_version ) {
		$this->plugin_version = $plugin_version;
		update_metadata( 'post', $this->wp_post_id, self::BRIZY_POST_PLUGIN_VERSION, $plugin_version );
	}

	/**
	 * @param $v
	 *
	 * @return $this
	 */
	public function set_needs_compile( $v ) {
		$this->needs_compile = (bool) $v;
		update_metadata( 'post', $this->wp_post_id, self::BRIZY_POST_NEEDS_COMPILE_KEY, (bool) $v );

		return $this;
	}

	/**
	 * @return bool
	 */
	public function get_needs_compile() {
		return $this->needs_compile;
	}

	/**
	 * @return string
	 */
	public function get_compiler_version() {
		return $this->compiler_version;
	}

	/**
	 * @return string
	 */
	public function get_editor_version() {
		return $this->editor_version;
	}

	protected function populateAutoSavedData( $autosave ) {
		$autosave->set_template( $this->get_template() );
		$autosave->set_editor_data( $this->get_editor_data() );
		$autosave->set_editor_version( $this->get_editor_version() );

		return $autosave;
	}
}

