<?php
return array(
	'URL_ROUTER_ON'   => true,
	'TMPL_L_DELIM'=>'{',
	'TMPL_R_DELIM'=>'}',
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => '127.0.0.1', // 服务器地址
	'DB_NAME'   => 'duck', // 数据库名
	'DB_USER'   => 'root', // 用户名
	'DB_PWD'    => 'shai1267', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => 'duck_', // 数据库表前缀
	'DB_CHARSET'=> 'utf8', // 字符集	
	'MODULE_ALLOW_LIST' => array (
			'Home',
			'Admin',
	),
	'DEFAULT_MODULE' => 'Home', 
    'URL_MODEL' => '0',
    'URL_CASE_INSENSITIVE' => true,     //URL访问不区分大小写
    // 布局设置
    'TMPL_ENGINE_TYPE'      =>  'Think',     // 默认模板引擎 以下设置仅对使用Think模板引擎有效
    'TMPL_CACHFILE_SUFFIX'  =>  '.php',      // 默认模板缓存后缀
    'TMPL_DENY_FUNC_LIST'   =>  'echo,exit',    // 模板引擎禁用函数
    'TMPL_DENY_PHP'         =>  false, // 默认模板引擎是否禁用PHP原生代码
    'TMPL_L_DELIM'          =>  '{',            // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          =>  '}',            // 模板引擎普通标签结束标记
    'TMPL_VAR_IDENTIFY'     =>  'array',     // 模板变量识别。留空自动判断,参数为'obj'则表示对象
    'TMPL_STRIP_SPACE'      =>  true,       // 是否去除模板文件里面的html空格与换行
    'TMPL_CACHE_ON'         =>  true,        // 是否开启模板编译缓存,设为false则每次都会重新编译
    'TMPL_CACHE_PREFIX'     =>  '',         // 模板缓存前缀标识，可以动态改变
    'TMPL_CACHE_TIME'       =>  180,         // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
    'TMPL_LAYOUT_ITEM'      =>  '{__CONTENT__}', // 布局模板的内容替换标识
    'LAYOUT_ON'             =>  true, // 是否启用布局
    'LAYOUT_NAME'           =>  'layout', // 当前布局名称 默认为layout
    'DB_FIELD_CACHE'=>false,
);
