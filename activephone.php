<?php
/*
 * Plugin Name: Активная кнопка телефона в вакансии
 * Version: 0.1
 * Author: Яков Тенилин
 */

require_once plugin_dir_path(__FILE__) . 'includes/ap-function.php';
require_once plugin_dir_path(__FILE__) . 'includes/ap-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/ap-table-create.php';
require_once plugin_dir_path(__FILE__) . 'includes/ap-create-menu.php';

function activePhoneStyles()
{
	wp_register_style('activephone', plugins_url('/css/activephone.css', __FILE__));
	wp_enqueue_style('activephone');
}

function activePhoneScripts()
{
	wp_enqueue_script('newscript', plugins_url('/js/activephone.js', __FILE__), array('jquery'));
}

add_action('wp_enqueue_scripts', 'activePhoneStyles');
add_action('wp_enqueue_scripts', 'activePhoneScripts');

function admin_style()
{
	wp_enqueue_style('admin-styles', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
	wp_enqueue_style('admin_activephone', plugins_url('/css/admin_activephone.css', __FILE__));

}


function activePhoneScriptsForAdmin()
{
	wp_enqueue_script('newscript_admin', plugins_url('/js/admin_activephone.js', __FILE__), 'admin_activephone.js');
}


add_action('admin_enqueue_scripts', 'admin_style');
add_action('admin_enqueue_scripts', 'activePhoneScriptsForAdmin');


add_action('wp_ajax_get_phone_number_ajax', 'get_phone_number_ajax');
add_action('wp_ajax_nopriv_get_phone_number_ajax', 'get_phone_number_ajax');


// Table in admin panel
if (is_admin() == TRUE) {
	new Init_Active_Phone_Menu_Table_Create();
}

//add modal to footer
add_action('wp_footer', 'jobsearch_activephone_candidate_role_warning');

function jobsearch_activephone_candidate_role_warning()
{ ?>
    <div class="jobsearch-modal fade" id="JobSearchActivePhoneJobWarning">
        <div class="modal-inner-area">&nbsp</div>
        <div class="modal-content-area">
            <div class="modal-box-area">
                <span class="modal-close"><i class="fa fa-times"></i></span>
                <div class="jobsearch-send-message-form">
                    <div class="send-message-warning">
                        <span>Необходима учетная запись кандидата, чтобы посмотреть номер.</span>
                        <span>Нажмите <a href="<?php echo wp_logout_url(get_permalink()); ?>">здесь</a> для выхода</span>
                        <span>и попробуйте снова</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}