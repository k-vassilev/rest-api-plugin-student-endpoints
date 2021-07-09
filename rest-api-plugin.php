<?php
/**
 * Plugin Name: REST API Endpoints for students
 * Description: Custom endpoints for students
 * Version: 1.0.0
 * Author: Kristian Vassilev
 */



 // Callback for all students
function ob_students(){
    $args = [
        'numberposts' => 9999,
        'post_type' => 'student'
    ];

    $students = get_posts( $args );
    return $students;
}

// Callback for a single student by ID
function ob_student( $id ){
    
    $student_id = sanitize_text_field( $id['id'] );
    return get_post($student_id);
}

// REST GET Endpoints
add_action('rest_api_init', function(){

    // Route for all students
    register_rest_route( 'ob/v1', 'students', [
        'methods' => 'GET',
        'callback' => 'ob_students'
    ]);

    // Route for a single student
    register_rest_route( 'ob/v1', 'students/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'ob_student')
    );
});



// REST AUTHENTICATION
// Checks if user is administrator and can post
function authenticate_user() {
    // wpApiSettings in console to get the nonce
    
    if (current_user_can( 'administrator' )) {
        return current_user_can( 'administrator' );
    }else {
         return 'Not authorized to post';
    }
}



// REST POST Endpoint
add_action('rest_api_init', function() {

    // Route to post
    register_rest_route('/ob/v1', '/students',[
        'methods' => ['POST'],
        'callback' => 'ob_add_student',
        'permission_callback' => 'authenticate_user'
      ]);
});
  
function ob_add_student() {

    // Student data to be sent
    $student = array(
        'post_title' => sanitize_text_field( $_POST['post_title'] ),
        'post_content' => sanitize_text_field($_POST['post_content']),
        'post_excerpt' => sanitize_text_field($_POST['post_excerpt']),
        'post_status' => 'publish',
        'post_type' => 'student',
        );
    return wp_insert_post( $student );
}



// REST EDIT Endpoint
add_action('rest_api_init', function() {
    
    // Route to update
    register_rest_route('/ob/v1', '/students/update/(?P<id>\d+)',[
        'methods' => ['POST'],
        'callback' => 'ob_update_student',
        'permission_callback' => 'authenticate_user'
      ]);
});
  
function ob_update_student($args) {

    // gets the student data based on the id;
    $student_id = sanitize_text_field($args['id']);
    $student = array(
        'ID' => $student_id,
        'post_title' => sanitize_text_field( $_POST['post_title'] ),
        'post_content' => sanitize_text_field($_POST['post_content']),
        'post_excerpt' => sanitize_text_field($_POST['post_excerpt']),
        'post_status' => 'publish',
        'post_type' => 'student',
        );
    return wp_update_post( $student );
}



// REST DELETE Endpoint
function ob_delete_student($args) {

    $student_id = sanitize_text_field($args['id']);
    return wp_delete_post($student_id);
}

add_action('rest_api_init', function() {

    register_rest_route('/ob/v1', '/students/delete/(?P<id>\d+)',[
        'methods' => ['DELETE'],
        'callback' => 'ob_delete_student',
        'permission_callback' => 'authenticate_user',
    ]);
});
?>

