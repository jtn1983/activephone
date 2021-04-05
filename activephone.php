<?php
/*
 * Plugin Name: Активная кнопка телефона в вакансии
 * Version: 0.1
 * Author: Яков Тенилин
 */

require_once plugin_dir_path(__FILE__) . 'includes/ap-function.php';
require_once plugin_dir_path(__FILE__) . 'includes/ap-shortcode.php';

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

// menu with table
class Init_Active_Phone_Menu_Table_Create
{
	public function __construct()
	{
		add_action('admin_menu', array($this, 'createMenu'));
	}

	public function createMenu()
	{
		add_menu_page('Статистика кликов по активной кнопке', 'Статистика кликов по активной кнопке', 'manage_options', 'active_phone_statistic', array($this, 'createTable'), 'dashicons-code-standards', '20.5');
	}

	public function createTable()
	{
		$Table = new Active_Phone_Menu_Table_Create();
		$Table->prepare_items();

	?>
		<div class="wrap">
			<h2 class="activephone-header">Статистика кликов по активной кнопке</h2>
			<div class="text-count">Количество кликов за период: <span class="count-digit"><?php echo ($Table->getClickCount()); ?></span></div>

			<?php $Table->display(); ?>
		</div>
	<?php
	}
}

if (class_exists('WP_List_Table') == FALSE) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

// table class

class Active_Phone_Menu_Table_Create extends WP_List_Table
{
	public function prepare_items()
	{
		$columns    = $this->get_columns();
		$hidden     = $this->get_hidden_columns();
		$sortable   = $this->get_sortable_columns();
		$data       = $this->table_data();

		// table sort call function usort_reorder
		usort($data, array(&$this, 'usort_reorder'));

		$i = 0;

		$perPage        = 20;  //count elements per page
		$currentPage    = $this->get_pagenum();
		$totalItems     = count($data);
		$this->set_pagination_args(array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		));

		$data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}

	public function get_columns()
	{
		return array(
			'vacancy_name'	=> 'Вакансия',
			'employer_name'	=> 'Работодатель',
			'click_count' 	=> 'Количество кликов',
		);
	}

	public function get_hidden_columns()
	{
		return array();
	}

	public function get_sortable_columns()
	{
		return array(
			'vacancy_name' => array('vacancy_name', false),
			'employer_name' => array('employer_name', false),
			'click_count' => array('click_count', true),
		);
	}

	// get date from post massive and convert it to string for sql query
	private function getDateFromPost(){
		if (isset($_POST['dateFrom']) and ($_POST['dateTo'])) {
			$dateFrom = date("Y-m-d", strtotime($_POST['dateFrom']));
			$dateTo = date("Y-m-d", strtotime($_POST['dateTo'] . '+1 days'));
		} else {
			$dateFrom = date("Y-m-d", strtotime(date("Y-m-d") . '-1 month'));;
			$dateTo = date("Y-m-d", strtotime(date("Y-m-d") . '+1 days'));
		};
		return array(
			'dateFrom_string' => "'" . $dateFrom . "'",
			'dateTo_string' => "'" . $dateTo . "'"
		);
	}

	// get table data from database and group data by vacancy
	private function table_data() {
		global $wpdb;
		$dateFrom_string = $this -> getDateFromPost()['dateFrom_string'];
		$dateTo_string = $this -> getDateFromPost()['dateTo_string'];
		$data = $wpdb->get_results("SELECT vacancy_name, vacancy_id, employer_name, employer_id, count(vacancy_id) as click_count FROM wp_activephone WHERE date>=$dateFrom_string AND date<$dateTo_string GROUP BY vacancy_id");

		echo '<pre>';
		var_dump($data);
		echo '</pre>';

		$array = [];
		foreach ($data as $value) {
			$value -> vacancy_name = '<a href="'.get_post_permalink( $value -> vacancy_id).'">'.$value -> vacancy_name.'</a>';
			unset($value -> vacancy_id);
			$value -> employer_name = '<a href="'.get_post_permalink( $value -> employer_id).'">'.$value -> employer_name.'</a>';
			unset($value -> employer_id);
			array_push($array, (array)$value);
		};

		echo '<pre>';
		var_dump($array);
		echo '</pre>';

		return $array;
	}

	//get click count per date interval
	public function getClickCount() {
		$dateFrom_string = $this -> getDateFromPost()['dateFrom_string'];
		$dateTo_string = $this -> getDateFromPost()['dateTo_string'];
		global $wpdb;
		$data = $wpdb->get_results("SELECT * FROM wp_activephone WHERE date>=$dateFrom_string AND date<$dateTo_string");
		$count = count($data);
		return $count;
	}


	public function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'vacancy_name':
			case 'employer_name':
			case 'click_count':
				return $item[$column_name];
			default:
				return print_r($item, true);
		}
	}

	//sorting column function
	function usort_reorder($a,$b){
		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'click_count'; //If no sort, default to title
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
		$result = strnatcmp($a[$orderby], $b[$orderby]); //Determine sort order
		return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
	  }

	// navigation above table
	function extra_tablenav($which)
	{
		if ($which == "top") {

			if (isset($_POST['dateFrom']) && isset($_POST['dateFrom'])) {
				$value1 = $_POST['dateFrom'];
				$value2 = $_POST['dateTo'];
			} else {
				$value1 = date("d-m-Y", strtotime(date("Y-m-d") . '-1 month'));
				$value2 = date("d-m-Y");
			}
			?>
			<div class="teblenav alignleft actions">
				<form method="post" action="admin.php?page=active_phone_statistic">
					<p>
						<span>C:</span>
						<input type="text" id="dateFrom" name="dateFrom" value="<?php echo $value1 ?>">
						<span>По:</span>
						<input type="text" id="dateTo" name="dateTo" value="<?php echo $value2 ?>">
						<?php submit_button(__('Фильтр'), 'primary', 'submit', false); ?> 
					</p>
				</form>
			</div>
			<?php
		}
	}
}
