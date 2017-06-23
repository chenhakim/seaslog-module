seaslog模块
=========

## 安装

打开 `composer.json` 找到或创建 `repositories` key，添加VCS资源库。

```
	// ...
	"repositories": [
		// ...
		{
			"type": "vcs",
			"url": "https://github.com/chenhakim/seaslog-module.git"
		}
	],
	// ...
```

添加依赖包。

```
composer require chenhakim/seaslog-module dev-master
```

## 使用

- seaslog调用

1、配置
在项目中定义全局变量
```
defined('SEASLOG_PATH') or define('SEASLOG_PATH', '/home/www/log/项目名称/'); // 项目日志目录
```
2、后台调用
```
    \Module\SeasLog\SeasLog::record($level, $message, $extra = array());

```   
