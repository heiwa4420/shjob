# -*- coding: utf-8 -*-
#import os
#os.system("pip install selenium-wire")
#os.system("pip install blinker==1.7.0")
#

from seleniumwire import webdriver
import requests
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By

chromedriver_path="chromedriver.exe"
service = Service(chromedriver_path)

driver = webdriver.Chrome(service=service)

headers = {
    'Referer': 'https://www.baidu.com/link?url=baidu.com',
    'User-Agent':'Mozilla/5.0 (Linux; Android 13; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36',    
}

def interceptor(request):
    print(request.url)
    print(request.headers)
    
    for i,j in headers.items():
        del request.headers[i]
        request.headers[i]=j
    
    print(request.headers)



driver.request_interceptor = interceptor



shell="http://yugesuibi.cn/,mmk123"
#shell="http://142.171.227.253:8000/"
url=shell.split(',')[0]
parse=requests.utils.parse_url(shell)
url=f'{parse.scheme}://{parse.host}/'

# 打开目标页面
driver.get(url)

# 打印页面标题
print(driver.title)

# 关闭浏览器
# driver.quit()
