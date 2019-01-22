<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/22
 * Time: 15:15
 */

namespace Qyk\Mm\Utils;


use Qyk\Mm\Traits\ConnectServiceTrait;
use Qyk\Mm\Traits\SingletonTrait;

class HttpCurl
{
    use SingletonTrait, ConnectServiceTrait;

    /**
     * curl的参数
     * @var array
     */
    protected $curlOptions;
    /**
     * 头部信息
     * @var array
     */
    protected $appendHeaders;

    /**
     * cookie
     * @var array
     */
    protected $appendCookie;
    /**
     * 设置cookie文件地址
     * @var string
     */
    protected $appendCookieFile;

    /**
     * cURL资源
     */
    protected $ch;

    /**
     * 刷新重置
     */
    protected function refresh()
    {
        $this->curlOptions      = [
            CURLOPT_RETURNTRANSFER => true,     //屏蔽返回结果
            CURLOPT_HEADER         => 0,        //设定是否输出页面内容
        ];
        $this->appendHeaders    = [];
        $this->appendCookie     = [];
        $this->appendCookieFile = null;
        if ($this->ch == null) {
            $this->ch = curl_init();
        }
        return $this;
    }

    /**
     * @param string $url 查询
     * @return string  定向后的url的真实url
     */
    public function getRealUrl(string $url)
    {
        $header = get_headers($url, 1);
        if (strpos($header[0], '301') || strpos($header[0], '302')) {
            if (is_array($header['Location'])) {
                return $header['Location'][count($header['Location']) - 1];
            } else {
                return $header['Location'];
            }
        } else {
            return $url;
        }
    }

    /**
     * 设置代理，防止真实IP
     * @param string $proxy
     * @return HttpCurl
     */
    public function setProxy(string $proxy)
    {
        return $this->setCurlOptions(CURLOPT_PROXY, $proxy);
    }

    /**
     * 设置cookie文件地址
     * @param string $cookieFile
     * @return HttpCurl
     */
    protected function setCookieFile(string $cookieFile)
    {
        //todo
        $this->appendCookieFile = $cookieFile;
        return $this;
    }

    /**
     * 设置连接超时
     * @param int $timeOut
     * @return HttpCurl
     */
    public function setConnectTimeOut($timeOut = 5)
    {
        return $this->setCurlOptions(CURLOPT_CONNECTTIMEOUT, $timeOut);
    }


    /**
     * 设置用户代理
     * @param string $userAgent
     * @return HttpCurl
     */
    public function setUserAgent(string $userAgent)
    {
        return $this->setCurlOptions(CURLOPT_USERAGENT, $userAgent);
    }

    /**
     * 使用随机国内ip地址
     */
    public function useRandClientIp()
    {
        $ipLong  = [
            ['607649792', '608174079'], //36.56.0.0-36.63.255.255
            ['1038614528', '1039007743'], //61.232.0.0-61.237.255.255
            ['1783627776', '1784676351'], //106.80.0.0-106.95.255.255
            ['2035023872', '2035154943'], //121.76.0.0-121.77.255.255
            ['2078801920', '2079064063'], //123.232.0.0-123.235.255.255
            ['-1950089216', '-1948778497'], //139.196.0.0-139.215.255.255
            ['-1425539072', '-1425014785'], //171.8.0.0-171.15.255.255
            ['-1236271104', '-1235419137'], //182.80.0.0-182.92.255.255
            ['-770113536', '-768606209'], //210.25.0.0-210.47.255.255
            ['-569376768', '-564133889'], //222.16.0.0-222.95.255.255
        ];
        $randKey = mt_rand(0, 9);
        $ip      = long2ip(mt_rand($ipLong[$randKey][0], $ipLong[$randKey][1]));
        return $this->setClientIp($ip);
    }

    /**
     * 设置当前访问者IP地址
     * @param mixed $clientIp
     * @return HttpCurl
     */
    public function setClientIp($clientIp)
    {
        $this->setHeaders('X-FORWARDED-FOR', $clientIp);
        $this->setHeaders('X-REAL-IP', $clientIp);
        $this->setHeaders('REMOTE-ADDR', $clientIp);
        $this->setHeaders('CLIENT-IP', $clientIp);
        return $this;
    }

    /**
     * 执行post请求
     * @param string $uri
     * @param array  $params
     * @return array ['rt'=>false,'msg'=>''] | ['rt'=>true,'data'=>mixed];
     */
    public function doPost(string $uri, array $params = [])
    {
        $this->setCurlOptions(CURLOPT_URL, $uri);

        $this->setCurlOptions(CURLOPT_POST, true);
        $this->setCurlOptions(CURLOPT_POSTFIELDS, $params);
        return $this->execute();
    }

    /**
     * 执行get请求
     * @param string $uri
     * @param array  $params
     * @return array ['rt'=>false,'msg'=>''] | ['rt'=>true,'data'=>mixed];
     */
    public function doGet(string $uri, array $params = [])
    {
        $this->setCurlOptions(CURLOPT_URL, $this->getUrl($uri, $params));
        $this->setCurlOptions(CURLOPT_HTTPGET, true);
        return $this->execute();
    }


    /**
     * 设置请求头部参数
     * @param string $key
     * @param string $val
     * @return $this
     */
    public function setHeaders(string $key, string $val)
    {
        $this->appendHeaders[] = $key . ':' . $val;
        return $this;
    }


    /**
     * 设置referer
     * @param string $referer
     * @return HttpCurl
     */
    public function setReferer(string $referer)
    {
        return $this->setCurlOptions(CURLOPT_REFERER, $referer);
    }


    /**
     * 设置curl的配置参数
     * @param string $key
     * @param        $val
     * @return HttpCurl
     */
    protected function setCurlOptions(string $key, $val)
    {
        if ($val) {
            $this->curlOptions[$key] = $val;
        }
        return $this;
    }

    /**
     * @param string $url
     * @param array  $params
     * @return string
     */
    protected function getUrl(string $url, array $params = [])
    {
        $urlData = parse_url($url);
        if (isset($urlData['scheme']) && strtolower($urlData['scheme']) == 'https') {
            $this->userSSL();
        }
        if (!empty($params)) {
            $url .= strpos($url, '?') === false ? '?' : '&';
            $url .= http_build_query($params);
        }
        return $url;
    }


    /**
     * 使用ssl
     */
    protected function userSSL()
    {
        $this->setCurlOptions(CURLOPT_SSL_VERIFYHOST, 3);
        $this->setCurlOptions(CURLOPT_SSL_VERIFYPEER, false);
    }

    /**
     * 析构，退出
     */
    protected function close()
    {
        if ($this->ch) {
            curl_close($this->ch);
            $this->ch = null;
        }
    }

    /**
     * 执行
     * @return array ['rt'=>false,'msg'=>''] | ['rt'=>true,'data'=>mixed];
     */
    protected function execute()
    {
        if ($this->appendHeaders) {
            $this->setCurlOptions(CURLOPT_HTTPHEADER, $this->appendHeaders);
        }
        curl_setopt_array($this->ch, $this->curlOptions);
        $response = curl_exec($this->ch);
        $errorNo  = curl_errno($this->ch);
        if ($errorNo) {
            return ['rt' => false, 'msg' => curl_error($this->ch)];
        }
        $responseInfo = curl_getinfo($this->ch);
        if ($responseInfo['http_code'] != 200) {
            return ['rt' => false, 'msg' => 'response info ,http code is not 200'];

        }
        return ['rt' => true, 'data' => $response];
    }

}