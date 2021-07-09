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

    $data = [];

    $i = 0;
    
    foreach($students as $student){
        $data[$i]['id'] = $student -> ID;
        $data[$i]['title'] = $student -> post_title;
        $data[$i]['content'] = $student -> post_content;
        $data[$i]['slug'] = $student -> post_name;
        $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url( $student -> ID, 'thumbnail' );
        $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url( $student -> ID, 'medium' );
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url( $student -> ID, 'large' );
        $i++;
        
    }

    return $data;
}

// Callback for a single student by ID
function ob_student( $id ){
    $args = [
        'id' => $id['id'],
        'post_type' => 'student'
    ];

    $student = get_posts( $args );

    $data['id'] = $student[0] -> ID;
    $data['title'] = $student[0] -> post_title;
    $data['content'] = $student[0] -> post_content;
    $data['slug'] = $student[0] -> post_name;
    $data['featured_image']['thumbnail'] = get_the_post_thumbnail_url( $student[0] -> ID, 'thumbnail' );
    $data['featured_image']['medium'] = get_the_post_thumbnail_url( $student[0] -> ID, 'medium' );
    $data['featured_image']['large'] = get_the_post_thumbnail_url( $student[0] -> ID, 'large' );
        
    return $data;
}


// REST GET Endpoints
add_action('rest_api_init', function(){

    // Route for all students
    register_rest_route( 'ob/v1', 'students', [
        'methods' => 'GET',
        'callback' => 'ob_students'
    ]);

    // Route for a single student
    register_rest_route( 'ob/v1', 'students/(?P<id>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'ob_student')
    );
});




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

// Checks if user is administrator and can post
function authenticate_user() {

    if (current_user_can( 'administrator' )) {
        return current_user_can( 'administrator' );
    }else {
        return 'Not authorized to post';
    }
}












?>

