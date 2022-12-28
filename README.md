# MDoc 文档查阅系统
## 快速使用
1.安装Mdoc
```shell
composer require lfphp/mdoc
```
2.建立代码

```php
<?php
    //引入代码库自动加载文件
    include "vendor/autoload.php";

	//初始化系统，指定markdown文件所在目录
    setup_mdoc([
        'root' => __DIR__.'/document', 
    ]);
```
