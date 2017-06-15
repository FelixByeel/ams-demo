<?php
/**
 * 折线图类,描绘数据走势
 *
 *功能：通过给定的参数画出对应的折线图表。
 *
 *用法：new LineChart($title, $nameDataArr, $width, $height, [$imageUri]);
 *
 *$title：       图表标题。
 *
 *$nameDataArr： 名称数据数组，$key为横轴名称，$value为对应数据。
 *
 *$width：       图表宽度。
 *
 *$height：      图表高度。
 *
 *$imageUri：    图像输出位置及保存为文件时的文件名，默认为“public/images/checkoutChart/checkoutChart.png”,
 *如需指定路径，需要给出路径和完整文件名称，即包括文件后缀。
 *
 *@name     LineChart
 *@author   Felix
 *@date     2017-06-04
 *@update   2017-06-14
 *@param    resource    $_image             图像资源
 *@param    string      $_title             图表标题
 *@param    array       $_nameDataArr       按月统计数据数组
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
    private $_nameDataArr;
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
    *@param array   $nameDataArr    按月统计数据数组
    *@param int     $width          图像宽度
    *@param int     $height         图像高度
    *@param string  $imageUri       图像输出位置
    */
    public function __construct($title, $nameDataArr, $width, $height, $imageUri = 'public/images/checkoutChart/checkoutChart.png')
    {
        $this->_title               = $title;
        $this->_nameDataArr         = $nameDataArr;
        $this->_width               = $width;
        $this->_height              = $height;
        $this->_imageUri            = $imageUri;
        //创建图像handle
        $this->_image               = imagecreatetruecolor($this->_width, $this->_height)
                                    or die('Cannot Initialize new GD image stream');
        $this->_color               = imagecolorallocate($this->_image, 0, 0, 0);           //默认颜色为黑色
        $this->_backgroundColor     = imagecolorallocate($this->_image, 255, 255, 255);     //默认背景色为白色
        imagefill($this->_image, 0, 0, $this->_backgroundColor);
        imageantialias($this->_image, true);        //开启抗锯齿
    }

    //Set brush color
    private function _setColor($red, $green, $blue)
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
    private function _setBackgroundColor($x, $y, $red, $green, $blue)
    {
        $this->_backgroundColor = imagecolorallocate($this->_image, $red, $green, $blue);
        imagefill($this->_image, $x, $y, $this->_backgroundColor);
    }

    /**
    *Gets the maximum value from the array,return the value.
    */
    private function _getMaxValue($arr)
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
        $width  = $this->_width;
        $height = $this->_height;
        $font   = '../../public/font/msyh.ttc';       //指定中文字体
        $color  = $this->_setColor(123, 191, 214);

        $this->_setBackgroundColor(0, 0, 255, 255, 255);

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
        $yDataPositionArr = $this->_drawXAxis($this->_image, $this->_nameDataArr, $borderSpace, $width, $height, $font);
        //draw Y-axis line
        $xDataPositionArr = $this->_drawYAxis($this->_image, $this->_nameDataArr, $borderSpace, $width, $height, $font);

        //draw line
        $this->_drawLine($this->_image, $xDataPositionArr, $yDataPositionArr, $this->_nameDataArr, $font);

        //draw title
        //$color = $this->_setColor(123, 3, 111);
        //imagettftext($this->_image, 12, 0, 310, 20, $color, $font, $this->_title . ' ' . date('Y-m-d H:i:s'));

        //输出图像到文件
        imagepng($this->_image, APP_ROOT . $this->_imageUri);
        imagedestroy($this->_image);
    }

    /**
    *draw Y-axis line
    *
    *画横轴上的垂直虚线，方便查看数据，返回一个存储横轴名称位置的数组
    */
    private function _drawYAxis($image, $nameDataArr, $borderSpace, $width, $height, $font)
    {
        $xAxisCount         = count($nameDataArr) + 2;  //获取横轴名称个数，加2是为了右边留间隔。
        $xAxisSpace         = floor($width / $xAxisCount);
        $xDataPositionArr   = array();

        //draw line
        $color = $this->_setColor(200, 200, 200);
        for ($i=1; $i < $xAxisCount - 1; $i++) {    //边界减1位不画X轴最右边的垂直线
            //imagedashedline($image, $borderSpace + $xAxisSpace * $i, $borderSpace - 1, $borderSpace + $xAxisSpace * $i, $height - $borderSpace, $color);
            $xDataPositionArr[$i] = $borderSpace + $xAxisSpace * $i;  //save position
        }

        //draw name
        $color = $this->_setColor(105, 105, 105);
        $i = 1;
        foreach ($nameDataArr as $key => $value) {
            $textBox = imagettfbbox(12, 0, $font, $key);
            $textWidth = abs($textBox[2] - $textBox[0]);
            imagettftext($image, 12, -25, $borderSpace + $xAxisSpace * $i - $textWidth / 2, $height - $borderSpace + 20, $color, $font, $key);
            $i++;
        }

        return $xDataPositionArr;
    }

    /**
    *draw X-axis line
    *
    *画纵轴上的水平单位分隔线，方便查看数据，返回一个存储纵轴单位数据位置的数组。
    */
    private function _drawXAxis($image, $nameDataArr, $borderSpace, $width, $height, $font)
    {
        $maxValue           = $this->_getMaxValue($nameDataArr);    //获取纵轴数据中最大值
        $yAxisCount         = 0;                                    //初始化纵轴数据单元个数
        $yDataPositionArr   = array();                              //存储纵轴单位数据位置
        $unitNumber         = 2;                                    //Y轴基础单元分隔基数，列如为2，表示分隔为2、4、6、8,为5，表示分隔为5、10、15
        //根据最大数值设定纵轴数据单元个数。
        if ($maxValue <= 10) {
            $yAxisCount = 11;   //边界加1位，不画Y轴最上面的线
        } elseif ($maxValue <= 30) {
            $yAxisCount = 31;
            $unitNumber = 5;
        } else {
            $yAxisCount = 101;
            $unitNumber = 10;
        }

        //获取单位间隔距离，减去上下边距，平分为$yAxisCount单元个数的间隔距离。
        $yAxisSpace = floor(($height - $borderSpace * 2) / $yAxisCount);

        //draw line and number
        for ($i=1; $i < $yAxisCount; $i++) {
            if (!($i % $unitNumber)) {
                $color = $this->_setColor(200, 200, 200);
                imageline($image, $borderSpace, $height - $borderSpace - $yAxisSpace * $i, $width - $borderSpace, $height - $borderSpace - $yAxisSpace * $i, $color);
                $color = $this->_setColor(105, 105, 105);
                imagettftext($image, 12, 0, $borderSpace - 25, $height - $borderSpace - $yAxisSpace * $i + 5, $color, $font, $i);
            }
            $yDataPositionArr[$i] = $height - $borderSpace - $yAxisSpace * $i;
        }
        $yDataPositionArr[0] = $height - $borderSpace;
        return $yDataPositionArr;
    }

    /**
    *draw  broken line and line point
    *
    *通过$xDataPostionArr和$yDataPositionArr来确定$nameDataArr中的数据在图表中的位置
    *
    *@param array $xDataPositionArr     横轴名称数组
    *@param array $yDataPositionArr     纵轴单位数组
    *@param array $nameDataArr          数据数组
    */
    private function _drawLine($image, $xDataPositionArr, $yDataPositionArr, $nameDataArr, $font)
    {
        $i = 1;
        foreach ($nameDataArr as $key => $value) {
            if ($i > 1) {
                $color = $this->_setColor(60, 179, 113);
                imageline($image, $xDataPositionArr[$i - 1], $yDataPositionArr[$temp], $xDataPositionArr[$i], $yDataPositionArr[$value], $color);
                //imageline($image, $xDataPositionArr[$i - 1] + 1, $yDataPositionArr[$temp] + 1, $xDataPositionArr[$i] + 1, $yDataPositionArr[$value] + 1, $color);
            }
            $color = $this->_setColor(60, 179, 113);
            imagefilledellipse($image, $xDataPositionArr[$i], $yDataPositionArr[$value], 6, 6, $color);

            //折线点上方写上数值
            if ($value > 0) {
                $color = $this->_setColor(255, 100, 0);
                imagettftext($image, 12, 0, $xDataPositionArr[$i] - 10, $yDataPositionArr[$value] - 10, $color, $font, $value);
            }

            $i++;
            $temp = $value;
        }
    }
}
