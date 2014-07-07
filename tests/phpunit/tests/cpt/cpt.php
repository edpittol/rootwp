<?php

class Tests_Cpt extends WP_UnitTestCase {
	
	/**
	 * Test add post-type and register functions. The two functions must be executed together.
	 */
	function test_add() {		
		$id = rand_str( 20 );
		CPT::add( $id );
		CPT::register();
		
		$post_type_obj = get_post_type_object( $id );
		
		$this->assertInstanceOf( 'stdClass', $post_type_obj );
	}
	
	/**
	 * Test add post-type function with label.
	 */
	function test_add_with_label() {		
		$id = rand_str( 20 );
		$label = rand_str();
		CPT::add( $id, $label );
		CPT::register();
		
		$post_type_obj = get_post_type_object( $id );
		
		$this->assertEquals( $label . 's', $post_type_obj->labels->name );
	}
	
	/**
	 * Test post-type delete function
	 */
	function test_delete() {		
		$id = rand_str( 20 );
		CPT::add( $id );
		CPT::delete( $id );
		CPT::register();
		
		$post_type_obj = get_post_type_object( $id );
		
		$this->assertNull( $post_type_obj );
	}
	
	
}
