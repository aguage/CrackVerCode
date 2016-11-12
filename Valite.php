<?php

define('WORD_WIDTH',9);//验证码单个字符在图片中的宽度
define('WORD_HIGHT',13);//验证码单个字符在图片中的高度
define('OFFSET_X',7);
define('OFFSET_Y',3);
define('WORD_SPACING',4);

//破解验证码步骤
/**
 * 1.二值化。
 * 2.去噪。
 * 3.切割字符,粘连字符切割
 * 4.匹配字符
 */
class valite
{
	//设置图片路径
	public function setImage($Image)
	{
		$this->ImagePath = $Image;
	}
	public function getData()
	{
		return $data;
	}
	public function getResult()
	{
		return $DataArray;
	}
	
	//根据图片，获得二值对 还不是很完美
	public function getHec()
	{
		$res = imagecreatefrompng($this->ImagePath);
		$size = getimagesize($this->ImagePath);
		$data = array();
		//var_dump($size);exit;
		for($i=0; $i < $size[1]; ++$i)//高度
		{
			for($j=0; $j < $size[0]; ++$j)//宽度
			{
				$rgb = imagecolorat($res,$j,$i);
			//	echo $res;exit;
				$rgbarray = imagecolorsforindex($res, $rgb);
				//var_dump($rgbarray);exit;
				if($rgbarray['red'] > 125 || $rgbarray['green'] > 125
				|| $rgbarray['blue'] > 125)
				{
					$data[$i][$j]=0;
				}else{
					$data[$i][$j]=1;
				}
			}
		}
		//var_dump($data);exit;
		$this->DataArray = $data;
		
		
		$data = $this -> quzao($data,$size);
		
		for($m=0;$m<count($data);$m++){
			for($n=0;$n<count($data[$m]);$n++){
				echo $data[$m][$n];
			}
			echo '<br />';
				
		}
		
		
		$this->ImageSize = $size;
	}
	
	//去噪 还不是很完美
	public function quzao($data=NULL,$size=NULL)
	{
		
		// 如果1的周围数字不为1，修改为了0
		for($i=0; $i < $size[1]; ++$i)
		{
			for($j=0; $j < $size[0]; ++$j)
			{
				$num = 0;
				if($data[$i][$j] == 1)
				{
					// 上
					if(isset($data[$i-1][$j])){
						$num = $num + $data[$i-1][$j];
					}
					// 下
					if(isset($data[$i+1][$j])){
						$num = $num + $data[$i+1][$j];
					}
					// 左
					if(isset($data[$i][$j-1])){
						$num = $num + $data[$i][$j-1];
					}
					// 右
					if(isset($data[$i][$j+1])){
						$num = $num + $data[$i][$j+1];
					}
					// 上左
					if(isset($data[$i-1][$j-1])){
						$num = $num + $data[$i-1][$j-1];
					}
					// 上右
					if(isset($data[$i-1][$j+1])){
						$num = $num + $data[$i-1][$j+1];
					}
					// 下左
					if(isset($data[$i+1][$j-1])){
						$num = $num + $data[$i+1][$j-1];
					}
					// 下右
					if(isset($data[$i+1][$j+1])){
						$num = $num + $data[$i+1][$j+1];
					}
				}
				if($num == 0){
					$data[$i][$j] = 0;
				}
			}
		}
		return $data;
	}
	
	/*define('WORD_WIDTH',9);//验证码单个字符在图片中的宽度
define('WORD_HIGHT',13);//验证码单个字符在图片中的高度
define('OFFSET_X',7);//字符的水平偏移
define('OFFSET_Y',3);//字符的垂直偏移
define('WORD_SPACING',4);
*/
	public function run()
	{
		$result="";
		// 查找4个数字 切割字符 这种方法是针对验证码在同一高度，且相邻是一样的宽度，如ceshi.jpeg字符相邻7个字符，离图片顶部是3个字符
		$data = array("","","","");
		for($i=0;$i<4;++$i)		// i=0 X=7 Y=3  h=3 3<字符高度13+3
		{
			$x = ($i*(WORD_WIDTH+WORD_SPACING))+OFFSET_X;
			$y = OFFSET_Y;
			for($h = $y; $h < (OFFSET_Y+WORD_HIGHT); ++ $h)		//字符的垂直偏移+字符的高度，从字符的垂直偏移开始，第一行，第二行，直到字符的高度底部位置
			{
				for($w = $x; $w < ($x+WORD_WIDTH); ++$w)
				{
					$data[$i].=$this->DataArray[$h][$w];//从字符的水平偏移开始，第一个。。直到字符的宽度右侧位置3 7 ,3 8--3 字符水平偏移+字符的宽度
				}
			}
			
		}
	var_dump($data);//exit;
		// 进行关键字匹配
		foreach($data as $numKey => $numString)
		{
			$max=0.0;
			$num = 0;
			foreach($this->Keys as $key => $value)
			{
				$percent=0.0;
				similar_text($value, $numString,$percent);
				if(intval($percent) > $max)
				{
					$max = $percent;
					$num = $key;
					if(intval($percent) > 95)
						break;
				}
			}
			$result.=$num;		
		}
		$this->data = $result;
		// 查找最佳匹配数字
		return $result;
	}

	public function Draw()
	{
		for($i=0; $i<$this->ImageSize[1]; ++$i)
		{
	        for($j=0; $j<$this->ImageSize[0]; ++$j)
		    {
			    echo $this->DataArray[$i][$j];
	        }
		    echo "\n";
		}
	}
	public function __construct()
	{
		//字符库，要多学习一些字符的二值
		$this->Keys = array(
		'0'=>'000111000011111110011000110110000011110000011110000011110000011110000011110000011110000011011000110011111110000111000',
		'1'=>'000111000011111000011111000000011000000011000000011000000011000000011000000011000000011000000011000011111111011111111',
		'2'=>'011111000111111100100000110000000111000000110000001100000011000000110000001100000011000000110000000011111110111111110',
		'3'=>'011111000111111110100000110000000110000001100011111000011111100000001110000000111000000110100001110111111100011111000',
		'4'=>'000001100000011100000011100000111100001101100001101100011001100011001100111111111111111111000001100000001100000001100',
		'5'=>'111111110111111110110000000110000000110000000111110000111111100000001110000000111000000110100001110111111100011111000',
		'6'=>'000111100001111110011000010011000000110000000110111100111111110111000111110000011110000011011000111011111110000111100',
		'7'=>'011111111011111111000000011000000010000000110000001100000001000000011000000010000000110000000110000001100000001100000',
		'8'=>'001111100011111110011000110011000110011101110001111100001111100011101110110000011110000011111000111011111110001111100',
		'9'=>'001111000011111110111000111110000011110000011111000111011111111001111011000000011000000110010000110011111100001111000',
	);
	}
	protected $ImagePath;
	protected $DataArray;
	protected $ImageSize;
	protected $data;
	protected $Keys;
	protected $NumStringArray;

}
?>