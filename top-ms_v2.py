"""
Версия №2;
Вход;
С учетом того, что у сервера могут быть куплены услуги или их нет совсем;
Запросы меняются в обоих случаях;
_______________________________________________________________________________________________________________
Установка библиотек:
pip install requests user-agent bs4
_______________________________________________________________________________________________________________
"""

# _____________________________________________________________________________________________________________
import re
import json
import time
import codecs
import random
import requests
from user_agent import generate_user_agent
from bs4 import BeautifulSoup
from typing import Optional
from typing import Union


# _____________________________________________________________________________________________________________


class TopMs1:
    # _________________________________________________________________________________________________________
    def __init__(self, login: str, password: str):
        """Логин от аккаунта;"""
        self.login: str = login
        """Пароль от аккаунта;"""
        self.password: str = password
        """Сессия requests;"""
        self.session: Optional[requests.Session] = None
        """Токен csrf; меняется между запросами иногда;"""
        self.csrf_token: Optional[str] = None
        """Общие заголовки постоянные;"""
        self.headers: dict = {
            "user-agent": generate_user_agent(navigator='chrome'), "sec-ch-ua-platform": '"windows"',
            "accept-encoding": "gzip, deflate, br", "host": "top-ms.ru", "connection": "keep-alive",
            "accept-language": "ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7", "sec-ch-ua-mobile": "?0",
            "sec-ch-ua": '"Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
        }
        """Будет присвоено значение выбранного сервера;"""
        self.server: Optional[int] = None
        """Будет присвоено значение выбранного периода;"""
        self.period: Optional[int] = None
        """Флаг есть или нет уже услуг у сервера;"""
        self.exists: Optional[bool] = None

    # _________________________________________________________________________________________________________
    @staticmethod
    def pause():
        time.sleep(random.uniform(0.1, 0.3))

    # _________________________________________________________________________________________________________
    def make_requests(self, method: str, url: str, headers: dict, data: Union[dict, str, None] = None):
        """Методы будет выполнять запрос; чтоб код стал более аккуратнее;"""
        while True:
            try:
                if method == 'post':
                    response = self.session.post(url=url, headers=headers, data=data)
                elif method == 'get':
                    response = self.session.post(url=url, headers=headers, data=data)
                else:
                    print(f"В запрос передан не верный метод: {method}")
                    exit(0)
                """Пауза после каждого запроса;"""
                self.pause()
                return response
            except requests.exceptions.ConnectionError:
                self.pause()

    # _________________________________________________________________________________________________________

    def get_1_main_page(self):
        """Получение с главной страницы cookies"""
        url = "https://top-ms.ru/"
        headers = {
            "accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "upgrade-insecure-requests": "1", "sec-fetch-site": "none", "sec-fetch-mode": "navigate",
            "sec-fetch-user": "?1", "sec-fetch-dest": "document"}
        headers.update(self.headers)
        self.make_requests(method='get', url=url, headers=headers)

    # _________________________________________________________________________________________________________
    def get_2_csrf_token(self):
        """Получение токена csrf_token lkz запроса на вход;"""
        url = "https://top-ms.ru/account/auth/"
        headers = {
            "accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "upgrade-insecure-requests": "1", "sec-fetch-site": "same-origin", "sec-fetch-user": "?1",
            "sec-fetch-mode": "navigate", "sec-fetch-dest": "document", "referer": "https://top-ms.ru/",
        }
        headers.update(self.headers)
        response = self.make_requests(method='get', url=url, headers=headers)
        soup = BeautifulSoup(response.text, 'lxml')
        css_csrf_token = 'head meta[name="csrf_token"]'
        self.csrf_token = soup.select_one(css_csrf_token).get('content')

    # _________________________________________________________________________________________________________
    def get_3_login(self):
        """Запрос на вход и получение cookies аккаунта;"""
        url = "https://top-ms.ru/account/auth/"
        headers = {
            'Accept': '*/*', 'Accept-Encoding': 'gzip, deflate, br', 'Referer': 'https://top-ms.ru/account/auth/',
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8', 'Origin': 'https://top-ms.ru',
            'Sec-Fetch-Dest': 'empty', 'Sec-Fetch-Mode': 'cors', 'Sec-Fetch-Site': 'same-origin',
            'X-Requested-With': 'XMLHttpRequest'}
        headers.update(self.headers)
        d = json.dumps({"login": self.login, "password": self.password, "remember": 'true'})
        data = {'action': 'auth', 'csrf_token': self.csrf_token, 'data': d}
        response = self.make_requests(method='post', url=url, headers=headers, data=data)
        if response.cookies.get_dict().get('r_m'):
            print(f"Успешный вход в аккаунт;")
            return True
        else:
            print(f"Проблемы с входом в аккаунт;")
            exit(0)

    # _________________________________________________________________________________________________________
    def get_4_balance(self):
        """Вывести баланс на аккаунте;"""
        url = "https://top-ms.ru/cabinet/"
        headers = {
            "accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "upgrade-insecure-requests": "1", "sec-fetch-site": "same-origin", "sec-fetch-mode": "navigate",
            "sec-fetch-user": "?1", "sec-fetch-dest": "document", "referer": "https://top-ms.ru/account/auth/"}
        headers.update(self.headers)
        response = self.make_requests(method='get', url=url, headers=headers)
        soup = BeautifulSoup(response.text, 'lxml')
        css = 'div.cont_text div.uk-grid div[class^="uk-width"]'
        balance = soup.select_one(css).text.strip().rsplit(' ', 1)[0]
        print(balance)

    # _________________________________________________________________________________________________________
    def get_5_update_csrf_token(self):
        """Обновление csrf_token;"""
        url = "https://top-ms.ru/cabinet/services/"
        headers = {
            "accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "upgrade-insecure-requests": "1", "sec-fetch-site": "same-origin", "sec-fetch-mode": "navigate",
            "sec-fetch-user": "?1", "sec-fetch-dest": "document", "referer": "https://top-ms.ru/cabinet/"}
        headers.update(self.headers)
        response = self.make_requests(method='get', url=url, headers=headers)
        soup = BeautifulSoup(response.text, 'lxml')
        css_csrf_token = 'head meta[name="csrf_token"]'
        self.csrf_token = soup.select_one(css_csrf_token).get('content')

    # _________________________________________________________________________________________________________
    def get_6_get_user_servers(self):
        """Получение доступных у пользователя серверов;"""
        url = "https://top-ms.ru/cabinet/services/"
        data = f"action=get_user_servers&csrf_token={self.csrf_token}"
        headers = {
            "accept": "*/*", "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
            "x-requested-with": "XMLHttpRequest", "origin": "https://top-ms.ru", "sec-fetch-site": "same-origin",
            "sec-fetch-mode": "cors", "sec-fetch-dest": "empty", "referer": "https://top-ms.ru/cabinet/services/"}
        headers.update(self.headers)
        response = self.make_requests(method='post', url=url, headers=headers, data=data)
        soup = BeautifulSoup(response.json().get('html'), 'lxml')
        css = 'tbody tr td.tms-services-select'
        c = 1
        servers = list()
        for i in soup.select(css):
            server_id = i.select_one('input').get('value')
            server_name = re.sub(r"\s+", ' ', i.select_one('p').text.strip())
            servers.append([c, server_id, server_name])
            c += 1
        for i in servers:
            print(f"{i[0]} - {i[2]}")
        while True:
            choice = input(f"Введите порядковый номер сервера: ")
            if choice.isdigit() and 1 <= int(choice) <= c:
                """Выбор id сервера;"""
                self.server = servers[int(choice) - 1][1]
                print(f"Server: {self.server}")
                return True
            else:
                print(f"Порядковый номер сервера должен быть от 1 до {c}: ")

    # _________________________________________________________________________________________________________
    def get_7_get_server_services(self):
        """Получение данные по подключенным услугам на сервере; они или есть, или их нет;"""
        url = "https://top-ms.ru/cabinet/services/"
        data = f"action=get_server_services&id={self.server}&csrf_token={self.csrf_token}"
        headers = {
            "accept": "*/*", "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
            "x-requested-with": "XMLHttpRequest", "origin": "https://top-ms.ru", "sec-fetch-site": "same-origin",
            "sec-fetch-mode": "cors", "sec-fetch-dest": "empty", "referer": "https://top-ms.ru/cabinet/services/"}
        headers.update(self.headers)
        response = self.make_requests(method='post', url=url, headers=headers, data=data)
        response_1 = codecs.decode(response.text.replace(r"\/", r'/'), 'unicode_escape')
        """Нужно получить два значения: купленные услуги; ничего нету; и по ним выбирать дальнейшие запросы;"""
        self.exists = 'Список услуг сервера пуст' in response_1
        """Если ничего нету, то True; Если уже что-то куплено, то False;"""
        if self.exists:
            print(f"На сервере {self.server} нет услуг;")
        else:
            print(f"На сервере {self.server} есть услуги;")
        return True

    # _________________________________________________________________________________________________________
    def get_7_1_load_management_section(self):
        """Если покупки есть у сервера; Если get_7_get_server_services == False;"""
        """Нажатие на кнопку boost;"""
        url = "https://top-ms.ru/cabinet/services/"
        data = f"action=load_management_section&service=boost&id={self.server}&csrf_token={self.csrf_token}"
        headers = {
            "accept": "*/*", "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
            "x-requested-with": "XMLHttpRequest", "origin": "https://top-ms.ru", "sec-fetch-site": "same-origin",
            "sec-fetch-mode": "cors", "sec-fetch-dest": "empty", "referer": "https://top-ms.ru/cabinet/services/"}
        headers.update(self.headers)
        response = self.make_requests(method='post', url=url, headers=headers, data=data)
        response_1 = codecs.decode(response.text.replace(r"\/", r'/'), 'unicode_escape')
        result = 'Продлить услугу сейчас' in response_1
        if not result:
            print(f"Услуга будет куплена;")
        else:
            print(f"Услуга будет продлена;")
        self.pause()
        return True

    # _________________________________________________________________________________________________________
    def get_8_periods(self):
        """Получение доступных периодов; Тело запроса меняется в зависимости от наличия услуг;"""
        url = 'https://top-ms.ru/cabinet/services/'
        headers = {
            'accept': '*/*', 'content-type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'origin': 'https://top-ms.ru', 'referer': 'https://top-ms.ru/cabinet/services/', 'sec-fetch-dest': 'empty',
            'sec-fetch-mode': 'cors', 'sec-fetch-site': 'same-origin', 'x-requested-with': 'XMLHttpRequest'}
        headers.update(self.headers)
        if self.exists:
            data = {'action': 'load_buy_section', 'service': 'boost', 'id': self.server, 'csrf_token': self.csrf_token}
        else:
            d = json.dumps({'service': 'boost', 'id': self.server})
            data = f"action=service_prolong&data={d}&csrf_token={self.csrf_token}"
        response = self.make_requests(method='post', url=url, headers=headers, data=data)
        soup = BeautifulSoup(response.json().get('html'), 'lxml')
        css = 'div#service_period tbody tr td'
        periods = list()
        c = 1
        for i in soup.select(css):
            value = i.select_one('input[name="service_period"]').get('value')
            text = i.select_one('p[id$="_period"]').text.strip()
            periods.append([c, value, text])
            c += 1
        for i in periods:
            print(f"{i[0]}. {i[2]}")
        while True:
            choice = input(f"Введите порядковый номер 'количества кругов': ")
            if choice.isdigit() and 1 <= int(choice) <= c:
                """Выбор количества кругов;"""
                self.period = periods[int(choice) - 1][1]
                return True
            else:
                print(f"Порядковый номер 'количества кругов' должен быть от 1 до {c}: ")

    # _________________________________________________________________________________________________________
    def get_9_calculate_sum(self):
        """Вернется текст с указанием оплаты;"""
        url = "https://top-ms.ru/cabinet/services/"
        d = json.dumps({'type': 'prolong', 'service': 'boost', 'period': self.period, 'payment_method': 'balance'})
        data = f"action=calculate_sum&data={d}&csrf_token={self.csrf_token}"
        headers = {
            "accept": "*/*", "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
            "x-requested-with": "XMLHttpRequest", "origin": "https://top-ms.ru", "sec-fetch-site": "same-origin",
            "sec-fetch-mode": "cors", "sec-fetch-dest": "empty", "referer": "https://top-ms.ru/cabinet/services/"}
        headers.update(self.headers)
        response = self.make_requests(method='post', url=url, headers=headers, data=data)
        status = response.json().get('status')
        message = response.json().get('message')  # !!!
        if status == "0" and 'На Вашем балансе недостаточно средств' not in message:
            print(response.json().get('message'))
            return True
        elif status == '0' and 'На Вашем балансе недостаточно средств' in message:
            print(message)
            print(f"No money! EXIT!!!")
            exit(0)
        else:
            print(f"get_9_calculate_sum: {response.text}")
            exit(0)

    # _________________________________________________________________________________________________________
    def get_10_prolong_pay(self):
        """Выполнение оплаты заказа;"""
        url = "https://top-ms.ru/cabinet/services/"
        """Если boost нет, то купиться, если есть, то будет продлен;"""
        action = 'buy_pay' if self.exists else 'prolong_pay'
        d = json.dumps({'service': 'boost', 'id': self.server, 'period': self.period, 'payment_method': 'balance'})
        data = f"action={action}&data={d}&csrf_token={self.csrf_token}"
        headers = {
            "accept": "*/*", "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
            "x-requested-with": "XMLHttpRequest", "origin": "https://top-ms.ru", "sec-fetch-site": "same-origin",
            "sec-fetch-mode": "cors", "sec-fetch-dest": "empty", "referer": "https://top-ms.ru/cabinet/services/"}
        headers.update(self.headers)
        response = self.make_requests(method='post', url=url, headers=headers, data=data)
        if response.json().get('status') == "0" or response.json().get('status') == 0:
            print(f"Success buy")
            return True
        else:
            print(f"get_10_prolong_pay: {response.text}")
            exit(0)

    # _________________________________________________________________________________________________________
    def get_11_invoices(self):
        """Проверка баланса после покупки;"""
        url = "https://top-ms.ru/cabinet/invoices/"
        headers = {
            "accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "upgrade-insecure-requests": "1", "sec-fetch-site": "same-origin", "sec-fetch-mode": "navigate",
            "sec-fetch-user": "?1", "sec-fetch-dest": "document", "referer": "https://top-ms.ru/cabinet/services/"}
        headers.update(self.headers)
        response = self.make_requests(method='post', url=url, headers=headers)
        soup = BeautifulSoup(response.text, 'lxml')
        css = 'div.cont div.cont_text'
        balance = '\n'.join(soup.select_one(css).text.strip().replace('Пополнить', '').split('\n')[:2])
        print(balance)
        return True

    # _________________________________________________________________________________________________________
    def __call__(self, *args, **kwargs):
        try:
            self.session = requests.Session()
            self.get_1_main_page()
            self.get_2_csrf_token()
            self.get_3_login()
            self.get_4_balance()
            self.get_5_update_csrf_token()
            self.get_6_get_user_servers()
            self.get_7_get_server_services()
            if not self.exists:
                self.get_7_1_load_management_section()
            self.get_8_periods()
            self.get_9_calculate_sum()
            self.get_10_prolong_pay()
            self.get_11_invoices()
        except KeyboardInterrupt:
            exit(0)
    # _________________________________________________________________________________________________________


# _____________________________________________________________________________________________________________

# _____________________________________________________________________________________________________________
if __name__ == '__main__':
    login_1 = 'Akimoff1'
    password_1 = 'rh45hHRHE354erf'
    TopMs1(login=login_1, password=password_1)()
# _____________________________________________________________________________________________________________
