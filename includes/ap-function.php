<?php 
function getPhoneNumber(){
    global $post;
    $authorID = $post -> post_author;
    $phone = phone_format(get_user_meta( $authorID, 'jobsearch_field_user_phone', true));
    return $phone;
}

function showPhoneNumber(){
    global $wpdb;
    global $post;
    $vacancyId = $post -> ID;

    if (is_user_logged_in()){
        $userId = wp_get_current_user() -> ID;
        $data = $wpdb -> get_results("SELECT * FROM wp_activephone WHERE (user_id = $userId AND vacancy_id = $vacancyId)");
        return count($data) == 0 ? false : true;
    }

    $userSession = "'".session_id()."'";
    $data = $wpdb -> get_results("SELECT * FROM wp_activephone WHERE session_id LIKE $userSession AND vacancy_id = $vacancyId");
    return count($data) == 0 ? false : true;
}

function phone_format($phone) 
{
	$phone = trim($phone);
 
	$res = preg_replace(
		array(
			'/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{3})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
			'/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
			'/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
			'/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',	
			'/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{3})/',
			'/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{3})[-|\s]?(\d{3})/',					
		), 
		array(
			'+7 $2 $3-$4-$5', 
			'+7 $2 $3-$4-$5', 
			'+7 $2 $3-$4-$5', 
			'+7 $2 $3-$4-$5', 	
			'+7 $2 $3-$4', 
			'+7 $2 $3-$4', 
		), 
		$phone
	);
 
	return $res;
}

function getEmployerNameById ($id){
    $authorID = get_post_field('jobsearch_field_job_posted_by', $id);
    $employerID = get_post($authorID)->post_author;
    
}

function getVacancyNameById ($id){
    return get_post_field( 'post_title', $id);
}

function get_phone_number_ajax(){
    global $wpdb;
    $postId = $_POST['postId'];
    $userSession = $_POST['cookie'];
    
    $phone = phone_format(get_user_meta($authorID, 'jobsearch_field_user_phone', true));
    $userId = is_user_logged_in() ? wp_get_current_user()->ID : 0;
    if ($phone != ''){
        $wpdb -> insert (
            'wp_activephone',
            array(
                'employer_id' => $authorID,
                'employer_name' => getEmployerNameById($authorID),
                'vacancy_id' => $postId,
                'vacancy_name' => getVacancyNameById($postId),
                'user_id' => $userId,
                'session_id' => $userSession,
            )
        );
    }
    echo $phone;
    wp_die();
}