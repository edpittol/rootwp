<?php

class Tests_Form extends WP_UnitTestCase {

	/**
	 * Test form construction
	 */
	function test_construct() {
		$form = new Form();
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$this->assertEquals( '', $html );
	}
	
	/**
	 * Test form construction with action
	 */
	function test_construct_with_action() {
		$action = rand_str();
		$form = new Form( $action );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/^<form action="%s" method="POST"><\/form>$/i', $action );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test form construction using method GET
	 */
	function test_construct_with_method() {
		$action = rand_str();
		$method = 'GET';
		$form = new Form( $action, $method );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/^<form action="%s" method="%s"><\/form>$/i', $action, $method );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test form construction using allowed and disallowed attributes
	 */
	function test_construct_with_attrs() {
		$action = rand_str();
		$method = 'POST';
		$allowed_attrs = array( 'class', 'id', 'tabindex', 'enctype' );
		
		foreach( $allowed_attrs as $attr ) {
			$value = rand_str();
			$form = new Form( $action, $method, array( $attr => $value ) );
			
			ob_start();
			$form->render();
			$html = ob_get_clean();
			
			$regex = sprintf( '/^<form action="%s" method="%s" %s="%s"><\/form>$/i', $action, $method, $attr, 
					$value );
			$this->assertRegExp( $regex, $html );
		}
		
		$disallowed_attr = rand_str();
		$form = new Form( $action, $method, array( $disallowed_attr => $value ) );
			
		ob_start();
		$form->render();
		$html = ob_get_clean();
			
		$regex = sprintf( '/^<form action="%s" method="%s" %s="%s"><\/form>$/i', $action, $method, $disallowed_attr, 
				$value );
		$this->assertNotRegExp( $regex, $html);
	}
	
	/**
	 * Test form construction using allowed and disallowed attributes
	 */
	function test_construct_with_multiple_attrs() {
		$action = rand_str();
		$method = 'POST';
		$attrs = array(
			'class' => rand_str(),
			'id' => rand_str()
		);
		
		$form = new Form( $action, $method, $attrs );
			
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$attr_strings = array();
		foreach( $attrs as $name => $value ) {
			$attr_strings[] = sprintf('%s="%s"', $name, $value);
		}
		$attr_string = implode( ' ', $attr_strings );
		
		$regex = sprintf( '/^<form action="%s" method="%s" %s><\/form>$/i', $action, $method, $attr_string );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test add the input types
	 */
	function test_add_input() {
		$types = array( 'text', 'password', 'number', 'date', 'time', 'email', 'reset', 'submit', 'hidden', 'button' );
		
		foreach( $types as $type ) {
			$field = array(
				'id' => rand_str(),
				'type' => $type,
				'name' => rand_str()
			);
				
			$form = new Form();
			$form->add_field( $field );
			
			ob_start();
			$form->render();
			$html = ob_get_clean();
			
			$regex = sprintf( '/<input name="%s" type="%s" id="%s" \/>/i', $field['name'], $field['type'], 
					$field['id'] );
			$this->assertRegExp( $regex, $html );
		}
	}
	
	/**
	 * Test add the input type with value
	 */
	function test_add_input_value() {
		$field = array(
			'id' => rand_str(),
			'name' => rand_str(),
			'value' => rand_str()
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<input name="%s" value="%s" type="text" id="%s" \/>/i', $field['name'], $field['value'], 
				$field['id'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test add a textarea
	 */
	function test_add_textarea() {
		$field = array(
			'id' => rand_str(),
			'type' => 'textarea',
			'name' => rand_str()
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<textarea name="%s" id="%s" cols="50" rows="5"><\/textarea>/i', $field['name'], 
				$field['id'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test add a textarea with value
	 */
	function test_add_textarea_value() {
		$field = array(
			'id' => rand_str(),
			'type' => 'textarea',
			'name' => rand_str(),
			'value' => rand_str()
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<textarea name="%s" id="%s" cols="50" rows="5">%s<\/textarea>/i', $field['name'], 
				$field['id'], $field['value'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test add a select
	 */
	function test_add_select() {
		$field = array(
			'id' => rand_str(),
			'type' => 'select',
			'name' => rand_str(),
			'opt' => array(
				'option-1' => rand_str(),
				'option-2' => rand_str(),	
			)
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();		
		
		$regex = sprintf( '/<select name="%s" id="%s"><option value="">Choose<\/option>' .
				'<option value="option-1">%s<\/option><option value="option-2">%s<\/option><\/select>/i', 
				$field['name'],  $field['id'], $field['opt']['option-1'], $field['opt']['option-2'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test if return a message error if select hasn't option.
	 */
	function test_add_select_without_options() {
		$field = array( 
			'name' => rand_str(),
			'type' => 'select'
		);
		
		$form = new Form();
		$return = $form->add_field( $field );
		
		$this->assertEquals( 'No options to the select field.', $return );
	}
	
	/**
	 * Test add a select with a selected value
	 */
	function test_add_select_value() {
		$field = array(
			'id' => rand_str(),
			'type' => 'select',
			'name' => rand_str(),
			'opt' => array(
				'option-1' => rand_str(),
				'option-2' => rand_str(),	
			),
			'value' => 'option-1'
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();		
		
		$regex = sprintf( '/<option value="option-1" selected="selected">%s<\/option>/i', $field['opt']['option-1'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test add a radio
	 */
	function test_add_radio() {
		$field = array(
			'id' => rand_str(),
			'type' => 'radio',
			'name' => rand_str(),
			'opt' => array(
				'option-1' => rand_str(),
				'option-2' => rand_str(),	
			)
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<label><input name="%s" value="option-1" type="radio" id="%s" \/>%s<\/label><label>' .
				'<input name="%s" value="option-2" type="radio" id="%s" \/>%s<\/label>/i', $field['name'], 
				$field['id'], $field['opt']['option-1'], $field['name'], $field['id'], $field['opt']['option-2'], 
				$field['name'], $field['id'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test add a radio with a checked value
	 */
	function test_add_radio_value() {
		$field = array(
			'id' => rand_str(),
			'type' => 'radio',
			'name' => rand_str(),
			'opt' => array(
				'option-1' => rand_str(),
				'option-2' => rand_str(),	
			),
			'value' => 'option-1'
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<input name="%s" value="option-1" type="radio" id="%s" checked="checked" \/>/i', 
				$field['name'], $field['id'], $field['opt']['option-1'], $field['opt']['option-1'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test if return a blank string
	 */
	function test_add_radio_without_options() {
		$field = array( 
			'name' => rand_str(),
			'type' => 'radio'
		);
		
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$this->assertEquals( '', $return );
	}
	
	/**
	 * Test if return a message error if add a field without name.
	 */
	function test_add_field_without_name() {
		$field = array();
		
		$form = new Form();
		$form->add_field( $field );
		$return = $form->add_field( $field );
		
		$this->assertEquals( 'You need to define a name to the field!', $return );
	}
	
	/**
	 * Test add a field that is anonymous (doesn't need a name).
	 */
	function test_add_field_anonymous_types() {
		$anonymous_types = array( 'sep', 'reset', 'submit', 'button' );
		
		foreach( $anonymous_types as $type ) {
			$field = array( 'type' => $type );
			
			$form = new Form();
			$return = $form->add_field( $field );
			
			$this->assertTrue( $return );
		}
	}
	
	/**
	 * Test add a field that is anonymous (doesn't need a name).
	 */
	function test_add_field_required() {
		$field = array(
			'name' => rand_str(),
			'req' => true 
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<input name="%s" .* required="required"/i', $field['name'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test add a field that is anonymous (doesn't need a name).
	 */
	function test_add_field_not_required() {
		$field = array( 'name' => rand_str() );
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<input name="%s".*required="required"/i', $field['name'] );
		$this->assertNotRegExp( $regex, $html );
	}
	
	/**
	 * Test the generated name of fields added without name.
	 */
	function test_add_generated_names() {
		$field = array( 'type' => 'button' );
			
		$form = new Form();
		$form->add_field( $field );
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/name="field-0".*name="field-1"/i' );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test if the default type in add funtion is text.
	 */
	function test_add_default_type() {
		$field = array( 'name' => rand_str() );
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<input.*type="text"/i' );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test the generated name of fields added without name.
	 */
	function test_add_generated_id() {
		$field = array( 
			'type' => 'text',
			'name' => 'some-name'
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<input.*id="%s\-%s"/i', $field['type'], $field['name'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test add function with placeholder.
	 */
	function test_add_placeholder() {
		$field = array( 
			'type' => 'text',
			'name' => rand_str(),
			'ph' => rand_str()
		);
			
		$form = new Form();
		$form->add_field( $field );
		
		ob_start();
		$form->render();
		$html = ob_get_clean();
		
		$regex = sprintf( '/<input.*placeholder="%s"/i', $field['ph'] );
		$this->assertRegExp( $regex, $html );
	}
	
	/**
	 * Test add function with label
	 */
	function test_add_label() {
		
	}
}
