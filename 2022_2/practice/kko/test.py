from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.common.keys import Keys
import time
import pyperclip
import os
import urllib.request
import sys
from PyQt5.QtWidgets import *
from PyQt5.QtGui import *

class MyWindow(QMainWindow):
    def __init__(self):
        super().__init__()
        self.setWindowTitle('(LJH)Automation Program')
        self.setGeometry(800,400,600,400)
        self.setWindowIcon(QIcon("peace_stop_war_freedom_icon_219276.png"))

app= QApplication(sys.argv)

window = MyWindow()

window.show()

app.exec_()






def createFolder(directory):
    try: 
        if not os.path.exists(directory): os.makedirs(directory) 
    except OSError: 
        print ('Error: Creating directory. ' + directory)


def imageCrawling(Key):
    url = 'https://www.google.co.kr/imghp?hl=ko&tab=ri&ogbl'
    createFolder('./'+Key+'_img_download')
            
    driver = webdriver.Chrome(ChromeDriverManager().install())

    #============================================================================
    # 구글 이미지 검색 및 검색어 입력
    #============================================================================

    print(Key, '검색==============================================================')
    driver.get(url)
    keyword=driver.find_element_by_name('q')
    keyword.send_keys(Key)
    keyword.send_keys(Keys.ENTER)

    #============================================================================
    # 스크롤
    #============================================================================
    print(Key+'스크롤 중===========================================================')
    elem=driver.find_element_by_tag_name('body')
    for i in range(60):
        elem.send_keys(Keys.PAGE_DOWN)
        time.sleep(0.1)
    try:
        driver.find_element_by_css_selector('.mye4qd').click()
        for i in range(60):
            elem.send_keys(Keys.PAGE_DOWN)
            time.sleep(0.1)
    except:
        pass    
    # ============================================================================= 
    # 이미지 개수 images = driver.find_elements_by_css_selector("img.rg_i.Q4LuWd")
    # ============================================================================= 
    count = 1 
    images = driver.find_elements_by_css_selector(".rg_i.Q4LuWd")
    for img in images:
        try:
            img.click()
            time.sleep(2)
            start=time.time()
            imgUrl = driver.find_element_by_xpath('//*[@id="Sva75c"]/div/div/div[3]/div[2]/c-wiz/div/div[1]/div[1]/div[2]/div[1]/a/img').get_attribute("src")
            urllib.request.urlretrieve(imgUrl, './'+Key+'_img_download/' + Key + str(count) + ".jpg")
            print(str(count)+'개의 '+Key+'이미지 파일 다운로드 중....... Download time : '+str(time.time() - start)[:5]+' 초')
            count = count + 1
            if count >= 260:
                break
        except:
            pass

    print("=========================다운로드 완료===============================")
    time.sleep(2)
    driver.close()


def loginNaver(user_id,user_pw):
    driver = webdriver.Chrome(ChromeDriverManager().install())
    url='http://naver.com'
    
    # 1.네이버 이동
    driver.get(url)
    
    # 2.로그인 버튼 클릭
    elem = driver.find_element_by_class_name('link_login')
    elem.click()
    
    # 3. id 복사 붙여넣기
    elem_id = driver.find_element_by_id('id')
    elem_id.click()
    pyperclip.copy(user_id)
    elem_id.send_keys(Keys.CONTROL, 'v')
    time.sleep(1)

    # 4. pw 복사 붙여넣기
    elem_pw = driver.find_element_by_id('pw')
    elem_pw.click()
    pyperclip.copy(user_pw)
    elem_pw.send_keys(Keys.CONTROL, 'v')
    time.sleep(1)

    # 5. 로그인 버튼 클릭
    driver.find_element_by_id('log.login').click()
    time.sleep(10)


##loginNaver(ID,PW) 네이버 로그인
# imageCrawling(Key) : 구글에서 해당 검색어 이미지 다운.
