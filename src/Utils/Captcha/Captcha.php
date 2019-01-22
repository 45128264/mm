<?php

namespace Qyk\Mm\Utils\Captcha;
//验证码类
use Qyk\Mm\Stage;

class Captcha
{
    protected $code;//验证码
    protected $width      = 240;//宽度
    protected $height     = 50;//高度
    protected $fontSize   = 30;//字体大小
    protected $fontColor;//指定字体颜色
    protected $font;//指定字体
    protected $img;//输出图层
    protected $codeLength = 4;//验证码字体个数
    protected $sessionKey = 'imgValidateCode'; //session对应的key
    protected $timeOut    = 180; // 失效时间
    protected $fontRgb; // 字体颜色

    /**
     * 设置session对应的key
     * @param $key
     * @return $this
     */
    public function setSessionKey($key)
    {
        $this->sessionKey = $key;
        return $this;
    }

    /**
     * 设置宽度
     * @param int $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * 设置失效时间
     * @param int $timeOut
     * @return $this
     */
    public function setTimeOut(int $timeOut)
    {
        $this->timeOut = $timeOut;
        return $this;
    }

    /**
     * 设置宽度
     * @param int $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * 设置字体大小
     * @param $size
     * @return $this
     */
    public function setFontSize($size)
    {
        $this->fontSize = $size;
        return $this;
    }

    /**
     * 设置字体颜色
     * @param array $RGB
     * @return $this
     */
    public function setFontColor(array $RGB)
    {
        $parse = function ($color) {
            if (!is_numeric($color) || $color < 0 || $color > 255) {
                return mt_rand(0, 255);
            }
            return $color;
        };
        list($red, $green, $blue) = $RGB;
        $this->fontColor = imagecolorallocacte($this->img, $parse($red), $parse($green), $parse($blue));
        return $this;
    }

    /**
     * 设置字体路径
     * 注意字体路径要写对，否则显示不了图片
     * @param $fontPath
     * @return $this
     */
    public function setFont($fontPath)
    {
        $this->font = $fontPath;
        return $this;
    }

    /**
     * 指定验证码字体个数
     * @param int $length
     * @return $this
     */
    public function setCodeLength(int $length)
    {
        $this->codeLength = $length;
        return $this;
    }

    /**
     * 获取code
     * @return mixed
     */
    public function getCode()
    {
        if ($this->code) {
            return $this->code;
        }
    }

    /**
     *随机因子
     */
    protected function getCharset()
    {
        return 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ123456789';
    }

    /**
     * 获取字体路径
     * @return string
     */
    public function getFont()
    {
        return $this->font ?: $this->font = dirname(__FILE__) . '/font/Elephant.ttf';
    }

    /**
     * 设置属性
     * @param $key
     * @param $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $key    = ucwords(str_replace(['-', '_'], ' ', $key));
        $method = 'set' . str_replace(' ', '', $key);
        if (method_exists($this, $method)) {
            $this->$method($value);
        };
        return $this;
    }

    /**
     * 批量设置属性
     * @param array $key_values
     * @return $this
     */
    public function mulSetAttribute(array $key_values)
    {
        foreach ($key_values as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    /**
     *获取验证码
     */
    protected function initCode()
    {
        $charset = $this->getCharset();
        $_len    = strlen($charset) - 1;
        for ($i = 0; $i < $this->codeLength; $i++) {
            $this->code .= $charset[mt_rand(0, $_len)];
        }
    }

    /**
     * 验证验证码是否正确
     * @param $code
     * @return bool
     */
    public function validate(string $code): bool
    {
        $rt   = false;
        $info = Stage::app()->session->get($this->sessionKey);
        if (strtolower($info['code']) == strtolower($code)) {
            if ($info['expireAt'] > time()) {
                $rt = true;
            }
            Stage::app()->session->unset($this->sessionKey);
        }
        return $rt;
    }

    /**
     *创建背景
     */
    protected function createBg()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color     = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    /**
     *创建下噪点
     */
    protected function createNoise()
    {
        $line  = mt_rand(2, 3);
        $snow  = mt_rand($this->width * 2, $this->width * 8);
        $pixel = mt_rand($this->width * 2, $this->width * 8);
        //像素
        for ($i = 0; $i < $pixel; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagesetpixel($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        //雪花
        for ($i = 0; $i < $snow; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
        //线条
        for ($i = 0; $i < $line; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
    }

    /**
     * 添加同色噪点
     */
    protected function addSameFontColorNoise()
    {
        $line  = mt_rand(2, 3);
        $snow  = mt_rand(2, 8);
        $pixel = mt_rand(2, 20);
        //像素
        for ($i = 0; $i < $pixel; $i++) {
            $color = imagecolorallocate($this->img, ...array_rand($this->fontRgb));
            imagesetpixel($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        //雪花
        for ($i = 0; $i < $snow; $i++) {
            $color = imagecolorallocate($this->img, ...array_rand($this->fontRgb));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
        //线条
        for ($i = 0; $i < $line; $i++) {
            $color = imagecolorallocate($this->img, ...array_rand($this->fontRgb));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
    }

    /**
     *添加文字
     */
    protected function createFont()
    {
        $_x           = ($this->width + $this->fontSize * 0.8) / $this->codeLength;
        $getFontColor = function () {
            if ($this->fontColor) {
                return $this->fontColor;
            }
            $this->fontRgb[] = [mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156)];
            return imagecolorallocate($this->img, ...end($this->fontRgb));
        };
        $this->initCode();
        $font = $this->getFont();
        for ($i = 0; $i < $this->codeLength; $i++) {
            imagettftext($this->img, $this->fontSize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 3), $this->height / 1.2, $getFontColor(), $font, $this->code[$i]);
        }
    }

    /**
     *添加session
     */
    protected function createSession()
    {
        Stage::app()->session->set($this->sessionKey, ['code' => $this->getCode(), 'expireAt' => time() + $this->timeOut]);
    }

    /**
     *输出图片
     */
    protected function outPut()
    {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

    /**
     *生成img
     */
    public function outPutImg()
    {
        $this->createBg();
        $this->createNoise();
        $this->createFont();
        $this->addSameFontColorNoise();
        $this->createSession();
        $this->outPut();
    }
}

?>