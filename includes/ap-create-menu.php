<?php
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