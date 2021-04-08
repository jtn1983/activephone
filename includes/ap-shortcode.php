<?php
require_once('ap-function.php');
add_shortcode('activephone', 'activePhone');

function activePhone()
{
    if (is_user_logged_in()) {
        if(jobsearch_user_is_candidate()){
            if (showPhoneNumber()){
                echo '<a href="tel:'.getPhoneNumber().'" class="activephone-btn">'.getPhoneNumber().'</a>';
            } else {
                echo '<a href="javascript:void(0);" class="activephone-btn has-spinner">Телефон работодателя</a>';
            }
        } else {
            echo '<a href="javascript:void(0);" class="activephone-btn actiphone-no-candidate">Телефон работодателя</a>';
        }
    }else {
        echo '<a href="javascript:void(0);" class="activephone-btn jobsearch-open-signin-tab">Телефон работодателя</a>';
    }    
}