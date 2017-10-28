<?php
//	семантические ссылки, или ЧПУ
if (!defined('_PLUGSECURE_'))
  die('Прямой вызов модуля запрещен!');

class Menus
{
	//имя класса (читаемое)
	private static $className = 'Меню';
	private static $menus = array();


	public function __construct()
	{

		$query = "SELECT m.id as menu_id, m.name as menu_name, m.title as menu_title, m.`order` as menu_order, m.container, m.separator, i.id as item_id, i.parent_id as item_parent_id, i.title as item_title, i.link 
					FROM menus m
					INNER JOIN menu_items i ON m.id=i.menu_id
					ORDER BY m.id;";
		$query_menu = database::query($query);
		if($query_menu->num_rows)
		{
			self::$menus = self::genMenusTree($query_menu);
			foreach (self::$menus as $key => $menu) {
				$menu_data = database::prepareQuery("SELECT * FROM menus WHERE `order`='i:key' LIMIT 1;", array('key'=>$key))->fetch_assoc();
				config::$global_cms_vars['MENU#'.$key] = self::genMenu(self::$menus[$key]['items'], $menu_data['container'], $menu_data['separator']);
			}
		}
	}

	private static function genMenusTree($input_array)
	{
		$cat = array();
		while($row = $input_array->fetch_assoc()){
			$cat[$row['item_id']] = $row;
		}
		
		$tree = array();
		foreach ($cat as $id => &$node) {    
			//Если нет вложений
			if (!$node['item_parent_id']){
				$tree[$id] = &$node;
			}else{ 
				//Если есть потомки то перебераем массив
	            $cat[$node['item_parent_id']]['childs'][$id] = &$node;
			}
		}

		$full_tree = array();
		foreach ($tree as $value) {
			if(!isset($full_tree[$value['menu_order']]['id']))
			{
				$full_tree[$value['menu_order']]['id'] = $value['menu_id'];
			}

			if(!isset($full_tree[$value['menu_order']]['name']))
			{
				$full_tree[$value['menu_order']]['name'] = $value['menu_name'];
			}

			$full_tree[$value['menu_order']]['items'][$value['item_id']] = $value;
		}
		return $full_tree;
	}


	private static function genMenu($menu, $container='ul', $separator = false)
	{
		if($separator)
			$add_separator = '<span id="separator">'.$separator.'</span>';
		else
			$add_separator = '';

		if(empty($container)){
			foreach ($menu as $el) {
				if (isset($el['childs'])) 
				{
					$menu_html .= '<a href='.$el['link'].' class="menu_link">'.$el['item_title'].'</a>'.$add_separator;
					$menu_html .= self::genMenu($el['childs'], $container, $separator);
				}
				else 
					$menu_html .= '<a href='.$el['link'].' class="menu_link">'.$el['item_title'].'</a>'.$add_separator;
			}
		}
		else
		{
			switch ($container) {
				case 'ul':
					$block_item_container = 'li';
					break;
				case 'ol':
					$block_item_container = 'li';
					break;
				default:
					$block_item_container = $container;
					break;
			}
			$menu_html = '<'.$container.'>';
			foreach ($menu as $el) {
				if (isset($el['childs'])) 
				{
					$menu_html .= '<'.$block_item_container.' class="menu_item"><a href='.$el['link'].' class="menu_link">'.$el['item_title'].'</a>'.$add_separator.'</'.$block_item_container.'>';
					$menu_html .= self::genMenu($el['childs'], $container, $separator);
				}
				else 
					$menu_html .= '<'.$block_item_container.' class="menu_item"><a href='.$el['link'].' class="menu_link">'.$el['item_title'].'</a>'.$add_separator.'</'.$block_item_container.'>';
			}
			$menu_html .= '</'.$container.'>';
		}
		return $menu_html;
	}



	public static function getMenu($menu)
	{
		if(is_array($menu, self::$menus))
			return self::$menus[$menu];
		else
			return false;
	}

	//функция вывода имени класса (модуля)
	public static function getClassName()
	{		
		return self::$className;	
	}

}
?>