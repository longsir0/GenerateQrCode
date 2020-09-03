<?php
	/*
	@function 关于PHP生成二维码的一些方法

    @Author LongSir QQ:519585292

    @Date 2019-07-16
	*/
	/**
     * 生成二维码
     *
     * @access public
     * @param  id 产品ID
     * @param  titles 产品标题
     * @param  grade 二维码背景图
     * @return string
     */
	public function phpqrcode($id, $titles, $grade){
		include './phpqrcode/phpqrcode.php';
		$value = "http://www.XXX.com/default.php?s=articles/".$id.".html";//二维码内容
		$errorCorrectionLevel = 'R';//容错级别
		$matrixPointSize = 15;//生成图片大小
		//路径组合
		$times = time();
		$time = date("Y-m-d",$times);//以时间为目录
		$title = $this->pinyin1($titles);
		mkdir('./qrcodeImg/'.$time.'');
		chmod('./qrcodeImg/'.$time.'',0777);//创建目录并给权限
		//生成二维码图片
		QRcode::png($value,'./Public/qrcodeImg/'.$time.'/'.$title.'_'.$time.'_code.png',$errorCorrectionLevel,$matrixPointSize,2);

		$logo = './qrcodeImg/'.$time.'/'.$title.'_'.$time.'_code.png';//要拼入的二维码
		$QR   = './image/bakimg'.$grade.'.png';//背景
		if ($logo !== FALSE){
		  $QR = imagecreatefromstring(file_get_contents($QR));
		  $logo = imagecreatefromstring(file_get_contents($logo));
		  $QR_width = imagesx($QR);//背景图片宽度
		  $QR_height = imagesy($QR);//背景图片高度
		  $logo_width = imagesx($logo);//二维码图片宽度
		  $logo_height = imagesy($logo);//二维码图片高度
		  
		  $logo_qr_width = $QR_width / 1.5;
		  $scale = $logo_width/$logo_qr_width;
		  
		  $logo_qr_height = $logo_height/$scale;
		  $from_width = ($QR_width - $logo_qr_width) / 2;
		  //重新组合图片并调整大小
		  imagecopyresampled($QR, $logo, $from_width, 100, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
		}
		//输出图片
		//Header("Content-type: image/png");
		ImagePng($QR,'./Public/qrcodeImg/'.$time.'/'.$title.'_'.$time.'_code.png');
		return '/Public/qrcodeImg/'.$time.'/'.$title.'_'.$time.'_code.png';
	}

	/**
     * 中文转换拼音
     *
     * @access public
     * @param  zh 中文
     * @return string
     */
	public function pinyin1($zh){
		$ret = "";
		$s1 = iconv("UTF-8","gb2312", $zh);
		$s2 = iconv("gb2312","UTF-8", $s1);
		if($s2 == $zh){$zh = $s1;}
		for($i = 0; $i < strlen($zh); $i++){
			$s1 = substr($zh,$i,1);
			$p = ord($s1);
			if($p > 160){
				$s2 = substr($zh,$i++,2);
				$ret .= $this->getfirstchar($s2);
			}else{
				$ret .= $s1;
			}
		}
		return $ret;
	}

	/**
     * 获取汉字首字母
     *
     * @access public
     * @param  s0 中文
     * @return string
     */
	public function getfirstchar($s0){
		$fchar = ord($s0{0});
		if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
		$s1 = iconv("UTF-8","gb2312", $s0);
		$s2 = iconv("gb2312","UTF-8", $s1);
		if($s2 == $s0){$s = $s1;}else{$s = $s0;}
		$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
		if($asc >= -20319 and $asc <= -20284) return "A";
		if($asc >= -20283 and $asc <= -19776) return "B";
		if($asc >= -19775 and $asc <= -19219) return "C";
		if($asc >= -19218 and $asc <= -18711) return "D";
		if($asc >= -18710 and $asc <= -18527) return "E";
		if($asc >= -18526 and $asc <= -18240) return "F";
		if($asc >= -18239 and $asc <= -17923) return "G";
		if($asc >= -17922 and $asc <= -17418) return "I";
		if($asc >= -17417 and $asc <= -16475) return "J";
		if($asc >= -16474 and $asc <= -16213) return "K";
		if($asc >= -16212 and $asc <= -15641) return "L";
		if($asc >= -15640 and $asc <= -15166) return "M";
		if($asc >= -15165 and $asc <= -14923) return "N";
		if($asc >= -14922 and $asc <= -14915) return "O";
		if($asc >= -14914 and $asc <= -14631) return "P";
		if($asc >= -14630 and $asc <= -14150) return "Q";
		if($asc >= -14149 and $asc <= -14091) return "R";
		if($asc >= -14090 and $asc <= -13319) return "S";
		if($asc >= -13318 and $asc <= -12839) return "T";
		if($asc >= -12838 and $asc <= -12557) return "W";
		if($asc >= -12556 and $asc <= -11848) return "X";
		if($asc >= -11847 and $asc <= -11056) return "Y";
		if($asc >= -11055 and $asc <= -10247) return "Z";
		return null;
	}

	/**
     * 文件下载
     *
     * @access public
     * @param  s0 中文
     * @return void
     */
	public function Sendfile(){
		$article = M('article');
		$aid['aid'] = trim($_GET['aid']);
		$getfile = $article->where($aid)->find();
		$file = '/data/www/shengyuan'.$getfile['codeurl'];
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="' . basename($file) . '"');
		header("Content-Length: ". filesize($file));
		readfile($file);
	}
?>