<?php
return array(
	//'配置项'=>'配置值'
		'DEFAULT_THEME'    =>    'theme',
		'AUTH_TYPE'	=>	'2',
		'LAYOUT_ON'             =>  false, // 是否启用布局
		'TMPL_CACHE_ON'         =>  false,        // 是否开启模板编译缓存,设为false则每次都会重新编译
		'ACTION_CACHE_ON'  => false,  // 默认关闭Action 缓存
		'HTML_CACHE_ON'   => false,   // 默认关闭静态缓存
		'TMPL_ACTION_SUCCESS'   =>  'Public:error',
		'TMPL_ACTION_ERROR'     =>  'Public:error',

);

