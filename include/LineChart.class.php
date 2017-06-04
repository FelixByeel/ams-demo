<?php
/**
 * 折线图
 *
 *通过给定的参数画出对应的折线图表
 *
 *@name     LineChar
 *@author   Felix
 *@date     2017-06-04
 *@param    resource    $_image     图像资源
 *@param    string      $_title     图表标题
 *@param    array       $_xDataArr  横轴数据
 *@param    array       $_yDataArr  纵轴数据
 *@param    int         $_width     图像宽度
 *@param    int         $_height    图像高度
 *@param    int         $_color     线条颜色
 *@param    int         $_backgroundColor
 *@param    int         $_space     横轴间隔
 */
class LineChart
{
    private $_image;
    private $_title;
    private $_xDataArr;
    private $_yDataArr;
    private $_width;
    private $_height;
    private $_lineColor;
    private $_backgroundColor;
    private $_space;
    private $_imageUri;

    /**
    *初始化图像资源
    *
    *创建图像资源，并设置默认的前景色和背景色。
    *
    *@param string $title       图像标题
    *@param string $xDataArr
    */
    function __construct($title, $xDataArr, $yDataArr, $width, $height, $imageUri = 'public/images/checkoutChart/checkoutChart.png')
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
        $this->_lineColor           = imagecolorallocate($this->_image, 0, 0, 0);           //默认线条颜色为黑色
        $this->_backgroundColor     = imagecolorallocate($this->_image, 255, 255, 255);     //默认背景色为白色
    }

    public function setLineColor($red, $green, $blue)
    {
        $this->_lineColor = imagecolorallocate($this->_image, $red, $green, $blue);
    }

    public function setBackgroundColor($red, $green, $blue)
    {
        $this->_backgroundColor = imagecolorallocate($this->_image, $red, $green, $blue);
    }

    //开始画图
    public function drawLineChart()
    {
        //指定中文字体
        $font = '../../public/font/msyh.ttc';
        imagettftext($this->_image, 12, 0, 10, 20, $this->_lineColor, $font, $this->_title . ' ' . date('Y-m-d H:i:s'));
        imagepng($this->_image, APP_ROOT . $this->_imageUri);
        imagedestroy($this->_image);
    }
}
