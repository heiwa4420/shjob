#encoding=utf-8

from DrissionPage import SessionPage
from bs4 import BeautifulSoup
import html, json,os
from datetime import datetime

WORK_DIR = os.path.dirname(__file__)
DIR_DATA = os.path.join(WORK_DIR,'data/')

def get_today_name():
    """ 获取日期格式 0808
    """
    now_date = datetime.now()
    mon_str = str(now_date.month) if now_date.month > 9 else '0'+str(now_date.month)
    day_str = str(now_date.day) if now_date.day > 9 else '0'+str(now_date.day)
    return mon_str+day_str

# 处理结果文件夹
DIR_Today = os.path.join(DIR_DATA,get_today_name())
if not os.path.exists(DIR_Today):
    os.mkdir(DIR_Today)

fpath_suc_urls = os.path.join(DIR_Today, 'suc_urls.md')
fpath_fail_urls = os.path.join(DIR_Today, 'fail_urls.md')

def read_urls():
    # return ['http://lybt.fangac.cn/']
    """ 读取 urls.md文件中的所有域名，使用url为了避免.txt被加密
    """
    with open('./urls.md','r',encoding='utf-8') as f:
        urls = [x.strip() for x in f.readlines() if len(x)>3]
    print(f'./urls.md 读取到 {len(urls)} 条链接')
    return urls

def check_url(url):
    page = SessionPage()
    # headers = {
    #     'User-Agent:': 'Mozilla/5.0 (Linux;u;Android 4.2.2;zh-cn;) AppleWebKit/534.46 (KHTML,like Gecko)Version/5.1 Mobile Safari/10600.6.3 (compatible; Baiduspider/2.0;+http://www.baidu.com/search/spider.html)',
    #     'Referer': 'baidu.com'
    #     }
    # 这里使用header之后会请求失败
    if 'http' not in url[:5]:
        url = 'http://'+url
    page.get(url=url,timeout=10)

    if not page.html:
        print('--- 请求失败，html为空 ---')
        return False, '请求失败，html为空'

    jump_link = 'js.oss-aliyun.cn'
    if jump_link not in page.html:
        msg = '跳转链接不存在'
        print(msg)
        return False, msg
    
    ## 确定在的时候找tdk
    soup = BeautifulSoup(page.html, 'html.parser')
    noscript_tag = None
    if soup.head:
        noscript_tag = soup.head.noscript
    if not noscript_tag:
        msg = '跳转存在，TDK noscript 不存在'
        print(msg)
        return False, msg
    # title = noscript_tag.title.decode_contents
    if noscript_tag.title:
        title = html.unescape(noscript_tag.title).encode('utf-8').decode('utf-8')
    else:
        return False, 'title 不存在'
    
    kwd_tag = noscript_tag.find('meta', attrs={'name': 'keywords'})
    keyword_content = None
    if kwd_tag:
        keyword_content = kwd_tag.get('content')

    desc_tag = noscript_tag.find('meta', attrs={'name': 'description'})
    description_content = None
    if desc_tag:
        description_content = desc_tag.get('content')
    return True, (title, description_content, keyword_content)


if __name__ =='__main__':
    urls_to_check = read_urls()
    suc_urls = []
    fail_urls = []
    # 删除之前的
    if os.path.exists(fpath_fail_urls):
        os.remove(fpath_fail_urls)
    if os.path.exists(fpath_suc_urls):
        os.remove(fpath_suc_urls)

    file_suc = open(fpath_suc_urls,'a')
    file_fail = open(fpath_fail_urls,'a')
    for aurl in urls_to_check:
        print('checking ...🚥 ' + aurl)
        is_suc, data = check_url(aurl)
        if is_suc:
            tdk = data
            title, desc, keyword = tdk
            # print(title)
            # print(desc)
            # print(keyword)
            element = f'{aurl}, title: {title}'
            suc_urls.append(element)
            file_suc.write(element+'\n')
            print('done checking ... ✅ ' + aurl)
        else:
            msg = data
            element = f'{aurl} - {msg}'
            fail_urls.append(element)
            file_fail.write(element+'\n')
            print('done checking ... ❌ ' + aurl)
            
    print(json.dumps(suc_urls, indent=4, ensure_ascii=False))
    print(f'---- ⬆️ 成功的url一共: {len(suc_urls)}个')

    print(json.dumps(fail_urls, indent=4, ensure_ascii=False))
    print(f'---- ⬆️ 失败的url一共: {len(fail_urls)}个')
    
        
