# MDoc 文档查阅系统
## 介绍

MDoc 为PHP实现的快速为Markdown格式文件部署成为web服务的一个开源库。互联网人员频繁使用Markdown格式撰写各类文档，但是由于Markdown的原始阅读体验（使用文本编辑软件阅读）比较差，因此一般人员都需要借助定制化软件进行查看或预览，比较低效。
MDoc系统可以在PHP Web服务能力上，通过简单的代码引入和执行，实现Markdown文档实现线上化，在某些场景可以作为简单文档查阅系统、博客使用。

## 快速使用

1、安装Mdoc

```shell
composer require lfphp/mdoc
```
2、建立代码

```php
<?php
    //引入代码库自动加载文件
    include "vendor/autoload.php";

	//初始化系统，指定markdown文件所在目录
    setup_mdoc([
        'root' => __DIR__.'/document', 
    ]);
```

3、定制化文档系统基本信息（可选）
通过文档根目录中 `config.ini` 文件配置，可实现博客标题、作者、版权声明等更丰富的辅助信息呈现。
配置项目说明：

```ini
# 系统标题
title = ""

# 系统作者
author = ""

# 系统联系方式
contact = ""

# 系统版权声明
copyrights = "© Copyright 2020, All Rights Reserved by PHP Coder."

# 更多系统描述
description = ""

```

## 更新

最新代码发布信息请访问 https://github.com/sasumi/mdoc。
