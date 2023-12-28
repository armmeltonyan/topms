<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class TopMsServiceV2
{
    private $login;
    private $password;
    private $session;
    private $csrfToken;
    private $headers;
    private $server;
    private $period;
    private $exists;

    public function __construct()
    {
        $this->login = 'Akimoff1';
        $this->password = 'rh45hHRHE354erf';
        $this->session = new Client();
        $this->headers = [
            'user-agent' => 'your_user_agent',
            'sec-ch-ua-platform' => '"windows"',
            'accept-encoding' => 'gzip, deflate, br',
            'host' => 'top-ms.ru',
            'connection' => 'keep-alive',
            'accept-language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua' => '"Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
        ];
    }

    private function pause()
    {
        usleep(rand(100000, 300000));
    }

    private function makeRequest($method, $url, $headers, $data = null)
    {
        while (true) {
            try {
                $options = ['headers' => $headers];
                if ($method === 'post') {
                    $options['form_params'] = $data;
                }

                $response = $this->session->request($method, $url, $options);

                $this->pause();
                return $response;
            } catch (Exception $e) {
                $this->pause();
            }
        }
    }

    private function get1MainPage()
    {
        $url = "https://top-ms.ru/";
        $headers = [
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'upgrade-insecure-requests' => '1',
            'sec-fetch-site' => 'none',
            'sec-fetch-mode' => 'navigate',
            'sec-fetch-user' => '?1',
            'sec-fetch-dest' => 'document',
        ];
        $headers = array_merge($headers, $this->headers);
        $this->makeRequest('get', $url, $headers);
    }

    private function get2CsrfToken()
    {
        $url = "https://top-ms.ru/account/auth/";
        $headers = [
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'upgrade-insecure-requests' => '1',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-user' => '?1',
            'sec-fetch-mode' => 'navigate',
            'sec-fetch-dest' => 'document',
            'referer' => 'https://top-ms.ru/',
        ];
        $headers = array_merge($headers, $this->headers);
        $response = $this->makeRequest('get', $url, $headers);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);
        $this->csrfToken = $crawler->filter('head meta[name="csrf_token"]')->attr('content');
    }

    private function get3Login()
    {
        $url = "https://top-ms.ru/account/auth/";
        $headers = [
            'Accept' => '*/*',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Referer' => 'https://top-ms.ru/account/auth/',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Origin' => 'https://top-ms.ru',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        $headers = array_merge($headers, $this->headers);
        $data = [
            'action' => 'auth',
            'csrf_token' => $this->csrfToken,
            'data' => json_encode(['login' => $this->login, 'password' => $this->password, 'remember' => 'true']),
        ];

        $response = $this->makeRequest('post', $url, $headers, $data);

        $cookies = $response->getHeader('Set-Cookie');
        $cookiesArray = $this->parseCookies($cookies);

        if (isset($cookiesArray['r_m'])) {
            echo "Успешный вход в аккаунт;";
            return true;
        } else {
            echo "Проблемы с входом в аккаунт;";
            exit(0);
        }
    }

    private function parseCookies($cookies)
    {
        $cookiesArray = [];

        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie, 2);
            $name = trim($parts[0]);
            $value = isset($parts[1]) ? trim($parts[1], " \n\r\t\0\x0B\"") : null;
            $cookiesArray[$name] = $value;
        }

        return $cookiesArray;
    }

    private function get4Balance()
    {
        $url = "https://top-ms.ru/cabinet/";
        $headers = [
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'upgrade-insecure-requests' => '1',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'navigate',
            'sec-fetch-user' => '?1',
            'sec-fetch-dest' => 'document',
            'referer' => 'https://top-ms.ru/account/auth/',
        ];
        $headers = array_merge($headers, $this->headers);

        $response = $this->makeRequest('get', $url, $headers);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        $balance = trim($crawler->filter('div.cont_text div.uk-grid div[class^="uk-width"]')->text());
        $balance = explode(' ', $balance)[0];

        echo $balance;
    }

    private function get5UpdateCsrfToken()
    {
        $url = "https://top-ms.ru/cabinet/services/";
        $headers = [
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'upgrade-insecure-requests' => '1',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'navigate',
            'sec-fetch-user' => '?1',
            'sec-fetch-dest' => 'document',
            'referer' => 'https://top-ms.ru/cabinet/',
        ];
        $headers = array_merge($headers, $this->headers);

        $response = $this->makeRequest('get', $url, $headers);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        $csrfToken = $crawler->filter('head meta[name="csrf_token"]')->attr('content');

        $this->csrfToken = $csrfToken;
    }

    private function get6GetUserServers()
    {
        $url = "https://top-ms.ru/cabinet/services/";
        $data = ['action' => 'get_user_servers', 'csrf_token' => $this->csrfToken];
        $headers = [
            'accept' => '*/*',
            'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'x-requested-with' => 'XMLHttpRequest',
            'origin' => 'https://top-ms.ru',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-dest' => 'empty',
            'referer' => 'https://top-ms.ru/cabinet/services/',
        ];
        $headers = array_merge($headers, $this->headers);

        $response = $this->makeRequest('post', $url, $headers, $data);
        $jsonData = json_decode($response->getBody()->getContents(), true);
        dd($response->getBody()->getContents());
        $html = $jsonData['html'];

        $crawler = new Crawler($html);
        $servers = $crawler->filter('tbody tr td.tms-services-select')->each(function (Crawler $node, $i) {
            $serverId = $node->filter('input')->attr('value');
            $serverName = preg_replace('/\s+/', ' ', $node->filter('p')->text());

            return [$i + 1, $serverId, $serverName];
        });

        foreach ($servers as $server) {
            echo "{$server[0]} - {$server[2]}\n";
        }

        while (true) {
            $choice = readline("Введите порядковый номер сервера: ");
            if (ctype_digit($choice) && 1 <= (int) $choice && (int) $choice <= count($servers)) {
                $this->server = $servers[(int) $choice - 1][1];
                echo "Server: {$this->server}\n";
                return true;
            } else {
                echo "Порядковый номер сервера должен быть от 1 до " . count($servers) . ": ";
            }
        }
    }

    private function get7GetServerServices()
    {
        $url = "https://top-ms.ru/cabinet/services/";
        $data = http_build_query(['action' => 'get_server_services', 'id' => $this->server, 'csrf_token' => $this->csrfToken]);
        $headers = [
            'accept' => '*/*',
            'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'x-requested-with' => 'XMLHttpRequest',
            'origin' => 'https://top-ms.ru',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-dest' => 'empty',
            'referer' => 'https://top-ms.ru/cabinet/services/',
        ];
        $headers = array_merge($headers, $this->headers);

        $response = $this->makeRequest('post', $url, $headers, $data);
        $response1 = html_entity_decode(str_replace('\/', '/', $response->getBody()->getContents()));

        // You can use $response1 as needed.

        // Need to get two values: purchased services; nothing is there; and based on them, choose further requests.
        $this->exists = strpos($response1, 'Список услуг сервера пуст') !== false;

        if ($this->exists) {
            echo "На сервере {$this->server} нет услуг;\n";
        } else {
            echo "На сервере {$this->server} есть услуги;\n";
        }

        return true;
    }

    private function get71LoadManagementSection()
    {
        $url = "https://top-ms.ru/cabinet/services/";
        $data = http_build_query(['action' => 'load_management_section', 'service' => 'boost', 'id' => $this->server, 'csrf_token' => $this->csrfToken]);
        $headers = [
            'accept' => '*/*',
            'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'x-requested-with' => 'XMLHttpRequest',
            'origin' => 'https://top-ms.ru',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-dest' => 'empty',
            'referer' => 'https://top-ms.ru/cabinet/services/',
        ];
        $headers = array_merge($headers, $this->headers);

        $response = $this->makeRequest('post', $url, $headers, $data);
        $response1 = html_entity_decode(str_replace('\/', '/', $response->getBody()->getContents()));

        $result = strpos($response1, 'Продлить услугу сейчас') !== false;
        if (!$result) {
            echo "Услуга будет куплена;\n";
        } else {
            echo "Услуга будет продлена;\n";
        }

        $this->pause();

        return true;
    }

    private function get8Periods()
    {
        $url = 'https://top-ms.ru/cabinet/services/';
        $headers = [
            'accept' => '*/*',
            'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'origin' => 'https://top-ms.ru',
            'referer' => 'https://top-ms.ru/cabinet/services/',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            'x-requested-with' => 'XMLHttpRequest',
        ];
        $headers = array_merge($headers, $this->headers);

        if ($this->exists) {
            $data = [
                'action' => 'load_buy_section',
                'service' => 'boost',
                'id' => $this->server,
                'csrf_token' => $this->csrfToken,
            ];
        } else {
            $d = json_encode(['service' => 'boost', 'id' => $this->server]);
            $data = "action=service_prolong&data={$d}&csrf_token={$this->csrfToken}";
        }

        $response = $this->makeRequest('post', $url, $headers, $data);
        $responseJson = json_decode($response->getBody()->getContents(), true);
        $html = $responseJson['html'];

        $soup = new \Symfony\Component\DomCrawler\Crawler($html);
        $periods = [];
        $c = 1;

        foreach ($soup->filter('div#service_period tbody tr td') as $i) {
            $value = $i->filter('input[name="service_period"]')->attr('value');
            $text = $i->filter('p[id$="_period"]')->text();
            $periods[] = [$c, $value, $text];
            $c++;
        }

        foreach ($periods as $i) {
            echo "{$i[0]}. {$i[2]}\n";
        }

        while (true) {
            $choice = readline("Введите порядковый номер 'количества кругов': ");
            if (is_numeric($choice) && 1 <= $choice && $choice <= $c) {
                $this->period = $periods[$choice - 1][1];
                return true;
            } else {
                echo "Порядковый номер 'количества кругов' должен быть от 1 до {$c}: ";
            }
        }
    }

    private function get9CalculateSum()
    {
        $url = 'https://top-ms.ru/cabinet/services/';
        $data = json_encode([
            'type' => 'prolong',
            'service' => 'boost',
            'period' => $this->period,
            'payment_method' => 'balance',
        ]);
        $data = "action=calculate_sum&data={$data}&csrf_token={$this->csrfToken}";
        $headers = [
            'accept' => '*/*',
            'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'x-requested-with' => 'XMLHttpRequest',
            'origin' => 'https://top-ms.ru',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-dest' => 'empty',
            'referer' => 'https://top-ms.ru/cabinet/services/',
        ];
        $headers = array_merge($headers, $this->headers);

        $response = $this->makeRequest('post', $url, $headers, $data);
        $status = $response->json()['status'];
        $message = $response->json()['message'];

        if ($status == '0' && strpos($message, 'На Вашем балансе недостаточно средств') === false) {
            echo $response->json()['message'] . PHP_EOL;
            return true;
        } elseif ($status == '0' && strpos($message, 'На Вашем балансе недостаточно средств') !== false) {
            echo $message . PHP_EOL;
            echo "No money! EXIT!!!" . PHP_EOL;
            exit(0);
        } else {
            echo "get_9_calculate_sum: {$response->getBody()->getContents()}" . PHP_EOL;
            exit(0);
        }
    }

    private function get10ProlongPay()
    {
        $url = 'https://top-ms.ru/cabinet/services/';
        $action = $this->exists ? 'prolong_pay' : 'buy_pay';
        $data = json_encode([
            'service' => 'boost',
            'id' => $this->server,
            'period' => $this->period,
            'payment_method' => 'balance',
        ]);
        $data = "action={$action}&data={$data}&csrf_token={$this->csrfToken}";
        $headers = [
            'accept' => '*/*',
            'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'x-requested-with' => 'XMLHttpRequest',
            'origin' => 'https://top-ms.ru',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-dest' => 'empty',
            'referer' => 'https://top-ms.ru/cabinet/services/',
        ];
        $headers = array_merge($headers, $this->headers);

        $response = $this->makeRequest('post', $url, $headers, $data);
        if ($response->json()['status'] == '0' || $response->json()['status'] == 0) {
            echo "Success buy" . PHP_EOL;
            return true;
        } else {
            echo "get_10_prolong_pay: {$response->getBody()->getContents()}" . PHP_EOL;
            exit(0);
        }
    }

    private function get11Invoices()
    {
        $url = 'https://top-ms.ru/cabinet/invoices/';
        $headers = [
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'upgrade-insecure-requests' => '1',
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'navigate',
            'sec-fetch-user' => '?1',
            'sec-fetch-dest' => 'document',
            'referer' => 'https://top-ms.ru/cabinet/services/',
        ];
        $headers = array_merge($headers, $this->headers);

        $response = $this->makeRequest('post', $url, $headers);
        $html = $response->getBody()->getContents();
        $soup = new SimpleHtmlDom();
        $soup->load($html);
        $css = 'div.cont div.cont_text';
        $balance = implode("\n", array_slice(explode("\n", $soup->find($css, 0)->plaintext), 0, 2));
        echo $balance . PHP_EOL;
        return true;
    }

    // Add other methods...

    public function execute()
    {
        try {
            $this->session = new Client();
            $this->get1MainPage();
            $this->get2CsrfToken();
            $this->get3Login();
//            $this->get4Balance();
            $this->get5UpdateCsrfToken();
            $this->get6GetUserServers();
            // Add other method calls...
        } catch (Exception $e) {
            exit(0);
        }
    }
}

// Usage
$login = 'Akimoff1';
$password = 'rh45hHRHE354erf';
$topMs = new TopMsServiceV2($login, $password);
$topMs->execute();

