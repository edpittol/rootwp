<?php

class Tests_Code extends WP_UnitTestCase {

	/**
	 * List of media thumbnail ids
	 * @var array
	 */
	protected $_ids = array();

	/**
	 * Tear down the test fixture.
	 */
	public function tearDown() {
		// Cleanup
		foreach ($this->_ids as $id){
			wp_delete_attachment($id, true);
		}

		$uploads = wp_upload_dir();
		foreach ( scandir( $uploads['basedir'] ) as $file )
			_rmdir( $uploads['basedir'] . '/' . $file );

		parent::tearDown();
	}
	
	/**
	 * The default add test callback function
	 * 
	 * @param string|array $attr The shortcode attributes. Default: ''
	 * @param string $content The shortcode content. Default: ''
	 * @return string
	 */
	function shortcode_callback( $attr = '', $content = '' ) {
		$shortcode_content = 'shortcode content';
		
		if( is_array( $attr ) ) {
			foreach( $attr as $name => $value ) {
				$shortcode_content .= "|{$name}|{$value}|";
			}
		}
		
		if( $content ) {
			$shortcode_content .= "@{$content}@";
		}
		
		return $shortcode_content;
	}

	/**
	 * Test add function without attr and content
	 */
	function test_add() {
		$shortcode_name = rand_str();
		
		Code::add( $shortcode_name, array( $this, 'shortcode_callback' ) );
		$shortcode = "[{$shortcode_name}]";
		$shortcode_output = do_shortcode( $shortcode );
				
		$this->assertEquals( $this->shortcode_callback(), $shortcode_output );
	}
	
	/**
	 * Test if the list of attr is empty, the value of callback must be a string empty.
	 */
	function test_add_empty_attr() {
		$shortcode_name = rand_str();
				
		Code::add( $shortcode_name, array( $this, '_test_add_empty_attr_callback' ) );
		$shortcode = "[{$shortcode_name}]";
		$shortcode_output = do_shortcode( $shortcode );
				
		$this->assertEquals( '', $shortcode_output );
	}
	
	function _test_add_empty_attr_callback( $attr, $content ) {
		return $attr;
	}
	
	/**
	 * Test add function with a attr
	 */
	function test_add_attr() {
		$shortcode_name = rand_str();
		$shortcode_attr = array(
			'name' => rand_str()
		);
		$shortcode_attr_value = rand_str();
		
		Code::add( $shortcode_name, array( $this, 'shortcode_callback' ), array( $shortcode_attr ) );
		$shortcode = "[{$shortcode_name}  {$shortcode_attr['name']}={$shortcode_attr_value}]";
		$shortcode_output = do_shortcode( $shortcode );
				
		$this->assertEquals( 
			$this->shortcode_callback( 
				array( 
					$shortcode_attr['name'] => $shortcode_attr_value 
				) 
			), 
			$shortcode_output
		);
	}
	
	/**
	 * Test add function without pass the attr value
	 */
	function test_add_attr_std() {
		$shortcode_name = rand_str();
		$shortcode_attr = array(
			'name' => rand_str(),
			'std' => rand_str()
		);
		
		Code::add( $shortcode_name, array( $this, 'shortcode_callback' ), array( $shortcode_attr ) );
		$shortcode = "[{$shortcode_name} {$shortcode_attr['name']}]";
		$shortcode_output = do_shortcode( $shortcode );
		
		$this->assertEquals(
			$this->shortcode_callback(
				array(
					$shortcode_attr['name'] => $shortcode_attr['std']
				)
			),
			$shortcode_output
		);
	}
	
	/**
	 * Test add function without pass a required attr.
	 */
	function test_add_attr_req() {
		$shortcode_name = rand_str();
		$shortcode_attr = array(
			'name' => rand_str(),
			'req' => true
		);
		
		Code::add( $shortcode_name, array( $this, 'shortcode_callback' ), array( $shortcode_attr ) );
		$shortcode = "[{$shortcode_name}]";
		$shortcode_output = do_shortcode( $shortcode );
		
		$this->assertEquals( '', $shortcode_output );
	}
	
	/**
	 * Test add function without pass a required attr.
	 */
	function test_add_content() {
		$shortcode_name = rand_str();
		$content = 'SHORTCODE CONTENT';
		
		Code::add( $shortcode_name, array( $this, 'shortcode_callback' ) );
		$shortcode = "[{$shortcode_name}]{$content}[/{$shortcode_name}]";
		$shortcode_output = do_shortcode( $shortcode );
				
		$this->assertEquals( $this->shortcode_callback( '', $content ), $shortcode_output );
	}
	
	/**
	 * Test if gallery is wrapped by an ul element
	 */
	function test_gallery() {
		$filename = DIR_TESTDATA . '/images/canola.jpg';
		$contents = file_get_contents( $filename );
		
		$upload = wp_upload_bits( basename( $filename ), null, $contents );
		$id = $this->_make_attachment( $upload );
		
		$html = Code::gallery( array( "ids" => array( $id ) ) );
		
		$this->assertRegExp( '/^<ul class="gallery">.*<\/ul>$/i', $html );
	}

	/**
	 * Function snagged from wordpress tests (./tests/post/attachments.php)
	 */
	function _make_attachment($upload, $parent_post_id = 0) {
		$type = '';
		if ( !empty($upload['type']) ) {
			$type = $upload['type'];
		} else {
			$mime = wp_check_filetype( $upload['file'] );
			if ($mime)
				$type = $mime['type'];
		}

		$attachment = array(
			'post_title' => basename( $upload['file'] ),
			'post_content' => '',
			'post_type' => 'attachment',
			'post_parent' => $parent_post_id,
			'post_mime_type' => $type,
			'guid' => $upload[ 'url' ],
		);

		// Save the data
		$id = wp_insert_attachment( $attachment, $upload[ 'file' ], $parent_post_id );
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
		return $this->_ids[] = $id;
	}
	
}
