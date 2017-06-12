<?php
/**
 * 折线图类
 *
 *通过给定的参数画出对应的折线图表
 *
 *new LineChart($title, $countDataArr, $width, $height, [$imageUri]);
 *
 *$imageUri图像输出位置，默认为“public/images/checkoutChart/checkoutChart.png”,如需指定路径，需要给出路径和完整文件名称，即包括文件后缀。
 *
 *@name     LineChart
 *@author   Felix
 *@date     2017-06-04
 *@update   2017-06-13
 *@param    resource    $_image             图像资源
 *@param    string      $_title             图表标题
 *@param    array       $_countDataArr      纵轴数据
 *@param    int         $_width             图像宽度
 *@param    int         $_height            图像高度
 *@param    int         $_color             笔画颜色
 *@param    int         $_backgroundColor   背景颜色
 *@param    string      $_imageUri          生成图表保存路径
 */
class LineChart
{
    private $_image;
    private $_title;
    private $_countDataArr;
    private $_width;
    private $_height;
    private $_color;
    private $_backgroundColor;
    private $_imageUri;

    /**
    *初始化图像资源
    *
    *创建图像资源，并设置默认的前景色和背景色。
    *
    *@param string  $title          图像标题
    *@param array   $countDataArr   纵轴数据
    *@param int     $width          图像宽度
    *@param int     $height         图像高度
    *@param string  $imageUri       图像输出位置
    */
    public function __construct($title, $countDataArr, $width, $height, $imageUri = 'public/images/checkoutChart/checkoutChart.png')
    {
        $this->_title               = $title;
        $this->_countDataArr        = $countDataArr;
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
    private function setColor($red, $green, $blue)
    {
        return imagecolorallocate($this->_image, $red, $green, $blue);
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
    private function setBackgroundColor($x, $y, $red, $green, $blue)
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

    //draw chart start
    public function drawLineChart()
    {
        $width = $this->_width;
        $height = $this->_height;
        $font = '../../public/font/msyh.ttc';       //指定中文字体
        $color = $this->setColor(123, 191, 214);
        $this->setBackgroundColor(0, 0, 240, 240, 240);

        //画左和下的边框
        $borderSpace = 50; //边距
        //draw left border
        imageline($this->_image, $borderSpace, $borderSpace, $borderSpace, $height - $borderSpace, $color);
        //draw down border
        imageline($this->_image, $borderSpace, $height - $borderSpace, $width - $borderSpace, $height - $borderSpace, $color);
        //边框加粗
        $borderSpace = 51;
        //draw left border
        imageline($this->_image, $borderSpace, $borderSpace - 1, $borderSpace, $height - $borderSpace, $color);
        //draw down border
        imageline($this->_image, $borderSpace, $height - $borderSpace, $width - $borderSpace + 1, $height - $borderSpace, $color);

        //draw X-axis line
        $this->drawYAxis($this->_image, $this->_countDataArr, $borderSpace, $width, $height, $font);
        //draw Y-axis line
        $this->drawXAxis($this->_image, $this->_countDataArr, $borderSpace, $width, $height, $font);

        $color = $this->setColor(123, 3, 111);
        imagettftext($this->_image, 12, 0, 310, 20, $color, $font, $this->_title . ' ' . date('Y-m-d H:i:s'));
        imagepng($this->_image, APP_ROOT . $this->_imageUri);
        imagedestroy($this->_image);
    }

    //draw Y-axis line
    private function drawXAxis($image, $countDataArr, $borderSpace, $width, $height, $font)
    {
        $xAxisCount = count($countDataArr) + 1;  //获取横轴名称数，加1个是为了右边留空一个间隔。
        //$xAxisSpace = floor(($width - $borderSpace * 2) / $xAxisCount);   //除去边距
        $xAxisSpace = floor($width / $xAxisCount);

        //draw line
        $color = $this->setColor(200, 200, 200);
        for ($i=1; $i < $xAxisCount; $i++) {
            imagedashedline($image, $borderSpace + $xAxisSpace * $i, $borderSpace - 1, $borderSpace + $xAxisSpace * $i, $height - $borderSpace, $color);
        }

        //draw name
        $color = $this->setColor(105, 105, 105);
        $i = 1;
        foreach ($countDataArr as $key => $value) {
            $textBox = imagettfbbox(12, 0, $font, $key);
            $textWidth = abs($textBox[2] - $textBox[0]);
            imagettftext($image, 12, 0, $borderSpace + $xAxisSpace * $i - $textWidth / 2, $height - $borderSpace + 20, $color, $font, $key);
            $i++;
        }
    }

    //draw X-axis line
    private function drawYAxis($image, $countDataArr, $borderSpace, $width, $height, $font)
    {
        $maxValue = $this->getMaxValue($countDataArr);  //获取纵轴数据中最大值
        $yAxisCount = count($countDataArr);             //获取纵轴数据统计个数。

        //设定纵轴数据单位个数。
        if ($yAxisCount < 10) {
            $yAxisCount = 10;
        } else {
            $yAxisCount = 20;
        }

        //获取单位间隔距离
        $yAxisSpace = floor(($height - $borderSpace * 2) / $yAxisCount);

        for ($i=1; $i < $yAxisCount + 1; $i++) {
            $color = $this->setColor(200, 200, 200);
            imageline($image, $borderSpace, $height - $borderSpace - $yAxisSpace * $i, $width - $borderSpace, $height - $borderSpace - $yAxisSpace * $i, $color);
            $color = $this->setColor(105, 105, 105);
            imagettftext($image, 12, 0, $borderSpace - 25, $height - $borderSpace - $yAxisSpace * $i + 5, $color, $font, $i);
        }
    }

    //draw line
    private function drawLine($image)
    {
        # code...
    }
}
