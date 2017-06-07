<?php
/**
 * 折线图类
 *
 *通过给定的参数画出对应的折线图表
 *
 *new LineChart($title, $xDataArr, $yDataArr, $width, $height, [$imageUri]);
 *
 *$imageUri图像输出位置，默认为“public/images/checkoutChart/checkoutChart.png”,如需指定路径，需要给出路径和完整文件名称，即包括文件后缀。
 *
 *@name     LineChar
 *@author   Felix
 *@date     2017-06-04
 *@update   2017-06-07
 *@param    resource    $_image         图像资源
 *@param    string      $_title         图表标题
 *@param    array       $_xDataArr      横轴数据
 *@param    array       $_yDataArr      纵轴数据
 *@param    int         $_width         图像宽度
 *@param    int         $_height        图像高度
 *@param    int         $_color         线条颜色
 *@param    int         $_backgroundColor
 *@param    int         $_space         横轴间隔
 */
class LineChart
{
    private $_image;
    private $_title;
    private $_xDataArr;
    private $_yDataArr;
    private $_width;
    private $_height;
    private $_color;
    private $_backgroundColor;
    private $_space;
    private $_imageUri;

    /**
    *初始化图像资源
    *
    *创建图像资源，并设置默认的前景色和背景色。
    *
    *@param string  $title          图像标题
    *@param array   $xDataArr       横轴数据
    *@param array   $yDataArr       纵轴数据
    *@param int     $width          图像宽度
    *@param int     $height         图像高度
    *@param string  $imageUri       图像输出位置
    */
    public function __construct($title, $xDataArr, $yDataArr, $width, $height, $imageUri = 'public/images/checkoutChart/checkoutChart.png')
    {
        $this->_title               = $title;
        $this->_xDataArr            = $xDataArr;
        $this->_yDataArr            = $yDataArr;
        $this->_width               = $width;
        $this->_height              = $height;
        $this->_imageUri            = $imageUri;
        //创建图像handle
        $this->_image               = imagecreatetruecolor($this->_width, $this->_height)
                                    or die('Cannot Initialize new GD image stream');
        $this->_color               = imagecolorallocate($this->_image, 0, 0, 0);           //默认颜色为黑色
        $this->_backgroundColor     = imagecolorallocate($this->_image, 255, 255, 255);     //默认背景色为白色
        imagefill($this->_image, 0, 0, $this->_backgroundColor);
    }

    //Set brush color
    public function setColor($red, $green, $blue)
    {
        $this->_color = imagecolorallocate($this->_image, $red, $green, $blue);
    }

    /**
    *Set background color
    *
    *@param int $x      填充区域起始位置X坐标
    *@param int $y      填充区域起始位置Y坐标（左上角为0，0）
    *@param int $red
    *@param int $green
    *@param int $blue
    */
    public function setBackgroundColor($x, $y, $red, $green, $blue)
    {
        $this->_backgroundColor = imagecolorallocate($this->_image, $red, $green, $blue);
        imagefill($this->_image, $x, $y, $this->_backgroundColor);
    }

    /**
    *Gets the maximum value from the array,return the value.
    */
    private function getMaxValue($arr)
    {
        $tempValue = 0;
        foreach ($arr as $key => $value) {
            if ($tempValue < $value) {
                $tempValue = $value;
            }
        }
        return $tempValue;
    }

    //开始画图
    public function drawLineChart()
    {
        //imagestring($this->_image, 5, 5, 5, $this->_title . ' ' . date('Y-m-d H:i:s'), $this->_color);
        //指定中文字体
        $font = '../../public/font/msyh.ttc';
        imagettftext($this->_image, 12, 0, 10, 20, $this->_color, $font, $this->_title . ' ' . date('Y-m-d H:i:s'));
        imagepng($this->_image, APP_ROOT . $this->_imageUri);
        imagedestroy($this->_image);
    }
}
