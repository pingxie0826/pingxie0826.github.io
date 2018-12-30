<?php
//本页功能：
session_start ();
//初始化一个验证码类的对象：
$ci = new CodeImage ( );
//调用它的生成验证码函数：
$ci->SetImage ( 0, 4, 200, 80, 2, 1, true, "220,168,1" );
//执行完毕之后可以使用$_SESSION["code"]读取验证码数值

//PHP的两种特殊用途：
//1 可以模拟1张图片
//2 可以作为中转页实现文件下载时的身份验证和次数统计


/**
 * 验证码类，生成一张验证码图片，并把验证码存在$_SESSION["code"]中。
 * 日期：2011-09-27
 * 作者：turingPHP-ZY(修改)
 * 使用：注意 字体文件夹font
 */
class CodeImage {
	
	private $mode; //1：数字模式，2：字母模式，3：数字字母模式，其他：数字字母优化模式（去掉容易混淆的字符）
	private $v_num; //验证码个数
	private $img_w; //验证码图像宽度
	private $img_h; //验证码图像高度
	private $int_num_pixels; //干扰像素个数
	private $int_num_lines; //干扰线条数
	private $border; //图像边框
	private $border_color; //图像边框颜色
	private $font_dir; //字体文件相对路径
	

	function SetImage($mode, $v_num, $img_w, $img_h, $int_num_pixels, $int_num_lines, $border = true, $border_color = '255,200,85', $font_dir = 'font') {
		if (! isset ( $_SESSION ['code'] )) {
			//session_register ( 'code' );
			$_SESSION ['code'] = "";
		}
		
		
		$this->mode = $mode;
		$this->v_num = $v_num;
		$this->img_w = $img_w;
		$this->img_h = $img_h;
		$this->int_num_pixels = $int_num_pixels;
		$this->int_num_lines = $int_num_lines;
		$this->font_dir = $font_dir;
		$this->border = $border;
		$this->border_color = $border_color;
		$this->GenerateImage ();
	}
	
	function GetChar($mode) {
		if ($mode == "1") {
			$ychar = "0,1,2,3,4,5,6,7,8,9";
		} else if ($mode == "2") {
			$ychar = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
		} else if ($mode == "3") {
			$ychar = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
		} else
			$ychar = "3,4,5,6,7,8,9,A,B,C,D,H,K,P,R,S,T,W,X,Y";
		return $ychar;
	}
	
	function RandColor($rs, $re, $gs, $ge, $bs, $be) {
		$r = mt_rand ( $rs, $re ); // mt_rand()  随机数  和rand() 使用方式一样 比rand() 更优化  速度块四倍
		$g = mt_rand ( $gs, $ge );
		$b = mt_rand ( $bs, $be );
		return array ($r, $g, $b );
	}
	
	function GenerateImage() {
		$im = imagecreate ( $this->img_w, $this->img_h ); // imagecretetruecolor()
		
		//$black = imagecolorallocate ( $im, 0, 0, 0 );
		//$white = imagecolorallocate ( $im, 255, 255, 255 );
		$bgcolor = imagecolorallocate ( $im, 255, 250, 250 );
		
		imagefill ( $im, 0, 0, $bgcolor );
		
		$fonts = ScanDir ( $this->font_dir );
		$fmax = count ( $fonts ) - 2;
		
		$ychar = $this->GetChar ( $this->mode );
		$list = explode ( ",", $ychar );
		
		$x = mt_rand ( 2, $this->img_w / ($this->v_num + 2) );
		$cmax = count ( $list ) - 1;
		
		$code = '';
		
		for($i = 0; $i < $this->v_num; $i ++) //验证码
{
			$randnum = mt_rand ( 0, $cmax );
			$this_char = $list [$randnum];
			$code .= $this_char;
			$size = mt_rand ( intval ( $this->img_w / 5 ), intval ( $this->img_w / 4 ) );
			$angle = mt_rand ( - 20, 20 );
			$y = mt_rand ( ($size + 2), ($this->img_h - 2) );
			if ($this->border)
				$y = mt_rand ( ($size + 3), ($this->img_h - 3) );
			$rand_color = $this->RandColor ( 0, 200, 0, 100, 0, 250 );
			$randcolor = imagecolorallocate ( $im, $rand_color [0], $rand_color [1], $rand_color [2] );
			$fontrand = mt_rand ( 2, $fmax );
			$font = "$this->font_dir/" . $fonts [$fontrand];
			imagettftext ( $im, $size, $angle, $x, $y, $randcolor, $font, $this_char );
			$x = $x + intval ( $this->img_w / ($this->v_num + 1) );
		}
		
		for($i = 0; $i < $this->int_num_pixels; $i ++) { //干扰像素
			$rand_color = $this->RandColor ( 50, 250, 0, 250, 50, 250 );
			$rand_color_pixel = imagecolorallocate ( $im, $rand_color [0], $rand_color [1], $rand_color [2] );
			imagesetpixel ( $im, mt_rand () % $this->img_w, mt_rand () % $this->img_h, $rand_color_pixel );
		}
		
		for($i = 0; $i < $this->int_num_lines; $i ++) { //干扰线
			$rand_color = $this->RandColor ( 0, 250, 0, 250, 0, 250 );
			$rand_color_line = imagecolorallocate ( $im, $rand_color [0], $rand_color [1], $rand_color [2] );
			imageline ( $im, mt_rand ( 0, intval ( $this->img_w / 3 ) ), mt_rand ( 0, $this->img_h ), mt_rand ( intval ( $this->img_w - ($this->img_w / 3) ), $this->img_w ), mt_rand ( 0, $this->img_h ), $rand_color_line );
		}
		
		if ($this->border) //画出边框
{
			if (preg_match ( "/^\\d{1,3},\\d{1,3},\\d{1,3}$/", $this->border_color )) {
				$border_color = explode ( ',', $this->border_color );
			}
			$border_color_line = imagecolorallocate ( $im, $border_color [0], $border_color [1], $border_color [2] );
			imageline ( $im, 0, 0, $this->img_w, 0, $border_color_line ); //上横
			imageline ( $im, 0, 0, 0, $this->img_h, $border_color_line ); //左竖
			imageline ( $im, 0, $this->img_h - 1, $this->img_w, $this->img_h - 1, $border_color_line ); //下横
			imageline ( $im, $this->img_w - 1, 0, $this->img_w - 1, $this->img_h, $border_color_line ); //右竖
		}
		
		imageantialias ( $im, true ); //抗锯齿
		

		$time = time ();
		$_SESSION ['code'] = $code . "|" . $time; //把验证码和生成时间赋值给$_SESSION['code']
		

		//生成图像给浏览器
		if (function_exists ( "imagegif" )) {
			header ( "Content-type: image/gif" );
			imagegif ( $im );
		} elseif (function_exists ( "imagepng" )) {
			header ( "Content-type: image/png" );
			imagepng ( $im );
		} elseif (function_exists ( "imagejpeg" )) {
			header ( "Content-type: image/jpeg" );
			imagejpeg ( $im, "", 80 );
		} elseif (function_exists ( "imagewbmp" )) {
			header ( "Content-type: image/vnd.wap.wbmp" );
			imagewbmp ( $im );
		} else
			die ( "No Image Support On This Server !" );
		
		imagedestroy ( $im );
	}
}
?> 
