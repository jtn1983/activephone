<?php
require_once('ap-function.php');
add_shortcode('activephone', 'activePhone');

function activePhone()
{
    if (showPhoneNumber()){
        echo '<a href="tel:'.getPhoneNumber().'" class="activephone-btn">'.getPhoneNumber().'</a>';
    } else {
        echo '<a href="javascript:void(0);" class="activephone-btn activephone-open">Телефон работодателя</a>';
    }    
}