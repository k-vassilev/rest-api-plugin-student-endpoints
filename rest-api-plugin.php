<?php
/**
 * Plugin Name: REST API Endpoints for students
 * Description: Custom endpoints for students
 * Version: 1.0.0
 * Author: Kristian Vassilev
 *
 * @package students-rest-api-endpoints
 */

/**  Callback for all students. */
function ob_students() {
	$args = array(
		'numberposts' => 100,
		'post_type'   => 'student',
	);

	$students = get_posts( $args );

	if ( count( $students ) <= 0 ) {
		echo 'No students found';
		return;
	}
	return $students;
}



/**
 * Callback for a single student by ID
 *
 * @param object $id of the student.
 * @return void
 */
function ob_student( $id ) {
	$student_id = sanitize_text_field( $id['id'] );
	if ( null === ( get_post( $student_id ) ) ) {
		return 'No such ID exists';
	}
	return get_post( $student_id );

}



/**  REST GET Endpoints */
add_action(
	'rest_api_init',
	function () {
		// Route for all students.
		register_rest_route(
			'ob/v1',
			'students',
			array(
				'methods'  => 'GET',
				'callback' => 'ob_students',
			)
		);
		// Route for a single student.
		register_rest_route(
			'ob/v1',
			'students/(?P<id>\d+)',
			array(
				'methods'  => 'GET',
				'callback' => 'ob_student',
			)
		);
	}
);



/**  REST AUTHENTICATION
 * Checks if user is administrator and can post */
function ob_authenticate_user() {
	// wpApiSettings in console to get the nonce.
	if ( current_user_can( 'administrator' ) ) {
		return current_user_can( 'administrator' );
	} else {
		return false;
	}
}



/**  REST POST Endpoint */
add_action(
	'rest_api_init',
	function () {
		// Route to post.
		register_rest_route(
			'/ob/v1',
			'/students',
			array(
				'methods'             => array( 'POST' ),
				'callback'            => 'ob_add_student',
				'permission_callback' => 'ob_authenticate_user',
			)
		);
	}
);


/**
 * Adds new student
 *
 * @return void
 */
function ob_add_student() {

	// Student data to be sent.
	$student = array(
		'post_title'   => sanitize_text_field( $_POST['post_title'] ),
		'post_content' => sanitize_text_field( $_POST['post_content'] ),
		'post_excerpt' => sanitize_text_field( $_POST['post_excerpt'] ),
		'post_status'  => 'publish',
		'post_type'    => 'student',
	);
	if ( ! $student['post_title'] ) {
		return 'Student name is mandatory!';
	}
	echo $student['post_title'] . ' has been added!';
	wp_insert_post( $student );
}


/**  REST EDIT Endpoint */
add_action(
	'rest_api_init',
	function () {
		// Route to update.
		register_rest_route(
			'/ob/v1',
			'/students/update/(?P<id>\d+)',
			array(
				'methods'             => array( 'POST' ),
				'callback'            => 'ob_update_student',
				'permission_callback' => 'ob_authenticate_user',
			)
		);
	}
);

/**
 * Updates a single student.
 *
 * @param object $args - student fields to be updated.
 * @return void
 */
function ob_update_student( $args ) {

	// gets the student data based on the id.
	$student_id = sanitize_text_field( $args['id'] );
	$student    = array(
		'ID'           => $student_id,
		'post_title'   => sanitize_text_field( $_POST['post_title'] ),
		'post_content' => sanitize_text_field( $_POST['post_content'] ),
		'post_excerpt' => sanitize_text_field( $_POST['post_excerpt'] ),
		'post_status'  => 'publish',
		'post_type'    => 'student',
	);

	if ( ! get_post_status( $student['ID'] ) ) {
		echo 'Cannot edit non-existent student!';
		return;
	}
	if ( ! $student['post_title'] ) {
		return 'Student name is mandatory!';
	}

	echo $student['post_title'] . ' has been updated!';
	wp_update_post( $student );
}



// REST DELETE Endpoint.
/**
 * Delete single student function
 *
 * @param object $args - fields to be deleted by the function.
 * @return void
 */
function ob_delete_student( $args ) {
	$student_id = sanitize_text_field( $args['id'] );
	if ( ! get_post_status( $student_id ) ) {
		return 'Cannot delete non-existent student!';
	}
	echo get_the_title( $student_id ) . ' has been deleted!';
	wp_delete_post( $student_id );
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'/ob/v1',
			'/students/delete/(?P<id>\d+)',
			array(
				'methods'             => array( 'DELETE' ),
				'callback'            => 'ob_delete_student',
				'permission_callback' => 'ob_authenticate_user',
			)
		);
	}
);
