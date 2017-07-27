<?php
return [
    'url_common_param'  =>  true,
    'template'  =>  [
	    'layout_on'     =>  true,
	    'layout_name'   =>  'layout',
	    'layout_item'   =>  '{__CONTENT__}',
	    'tpl_cache'     =>	false,
	],

	'paginate'			=>	[
		'type'     	=>	'admin',
		'var_page'	=>	'page',
		'list_rows'	=>	10,
	],
];
?>