YAF扩展框架
==================

基于YAF框架扩展，新增Service层和引入单元测试。目标为可自动化测试，可扩展的基础框架。


```
    location / {
        index index.php index.html index.htm;
        root /data/www/yaf-phpframe/webroot/;
    }

    if (!-e $request_filename) {
        rewrite  ^/(.*)  /index.php?$1  last;
        break;
    }
```

```
	进入composer.json所在目录执行
	composer install

	不更新库
	composer update nothing

	修改composer国内镜像
	composer.json:
	{
    "repositories": [
        {"type": "composer", "url": "http://pkg.phpcomposer.com/repo/packagist/"},
        {"packagist": false}
    ]
	}
```

```
/usr/local/php-5.4.40/bin/php /data/www/vendor/phpunit/phpunit/phpunit --bootstrap /data/www/test/bootstrap.php /data/www/test/
```

