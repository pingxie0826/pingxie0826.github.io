<?php
class fileup{

        //文件路径
        private $filepath='./';
        //准许MIME
        private $allowmime=array(
                                'image/jpeg',
                                'image/jpg',
                                'image/pjpeg',
                                'image/gif',
                                'image/png',
                                'image/x-png'
                                );
        //准许后缀      
        private $allowsub=array('jpg','jpeg','png','gif');
        //准许大小
        private $maxsize=2000000;
        //当前的后缀名
        private $subfix;
        //是否启用随机文件名
        private $israndname=true;
        //当前的大小
        private $size;
        //文件原名
        private $orgname;
        //当前的MIME
        private $mime;
        //临时文件名
        private $tmpname;
        //错误号
        private $errornum;
        //错误信息
        private $errorinfo;
        //前缀
        private $prefix='';
        //组合得到的新文件名
        private $newfilename;

        /*      =====================初始化成员方法=====================
         *      通过这里的修改方法我们可以在初始化的时候，方便修改成员属性
         *      $arr=array(
         *      'filepath'=>'images',
         *      'allowsize'=>'3000000',
         *      'allowmime'=>array('传一个数组'),
         *      'allowsub'=>array('传一个数组'),
         *      'israndname'=>'1',
         *      'prefix'=>'wx_'
         *      );
         * 具体使用：
         * include 'Upload.class.php'; 
         * $arr=array('filepath'=>'项目中的那个文件夹');
         * $up=new fileup($arr);
         * $path=$up->up("上传组件的名字");
         * */

        public function __construct($array=array('filepath'=>'images/')){

                foreach($array as $key=>$val){

                        $key=strtolower($key);
 
						// in_array(值,数组);
                        //array_key_exists(键名或索引,数组) -- 检查给定的键名或索引是否存在于数组中
                        //get_class_vars(对象) --  返回由类的默认属性组成的数组
                        //get_class() 返回对象的类名 
                        if(!array_key_exists($key,get_class_vars(get_class($this)))){

                                //如果不存在成员属性的数组里，设置错误号
                                $this->setoption('error','-8');
                                //报错提示错误号
                                echo $this->geterror();
                                continue;
                        }

                        //调用成员方法然后修改成员属性
                        $this->setoption($key,$val);
                }
        }

        //修改成员属性的方法
        private function setoption($key,$val){

                $this->$key=$val;

        }


        //文件上传方法
		/*
			$field 文件域的名字 name<input type="file" name="up">
		
		*/
        public function up($field){
		
				if(!in_array($field,array_keys($_FILES))){
				
					$this->setoption('errornum',-8);
					return false;
				}

                //第一步，检测路径是否存在
               
                if(!$this->checkpath()){
                        return false;
                }
                //第二步，执行多文件上传的时候做一个标记位置
                $flag=false;
              

                //第三步，获得上传文件属性值，赋给变量
                $name=$_FILES[$field]['name'];// 
                $size=$_FILES[$field]['size'];
                $tmpname=$_FILES[$field]['tmp_name'];
                $mime=$_FILES[$field]['type'];
                $error=$_FILES[$field]['error'];



                //第四步，判断是不是多文件上传
                if(is_array($name)){
                     
                        //处理多文件上传处理
                        for($i=0,$j=count($name);$i<$j;$i++){

                                // 对类属性赋值
                                if($this->setfiles($name[$i],$size[$i],$tmpname[$i],$mime[$i],$error[$i])){
                                        // 检查文件大小  后缀名是否合法  mine类型是否合法
                                        if(!$this->checksize()|!$this->checksub()|!$this->checkmime()){
                                                $errorinfo[]=$this->geterror();
                                                $flag=false;
                                        }
                                }else{

                                        $errorinfo[]=$this->geterror().'<br />';

                                        $falg=false;
                                }

                        }


                    
                        for($i=0,$j=count($name);$i<$j;$i++){
                                    
                                if($this->setfiles($name[$i],$size[$i],$tmpname[$i],$mime[$i],$error[$i])){
                                        // 检查文件大小  后缀名是否合法  mine类型是否合法
                                        if($this->checksize()&&$this->checksub()&&$this->checkmime()){
                                                // 将文件名放入数组中
                                                $newfilename[]=$this->filepath.$this->newfilename;
                                                // 临时文件移动
                                                $this->move();
                                        }
                                }
                        }
                        $this->newfilename=$newfilename;

                        if(!$flag){
                                $this->errorinfo=$errorinfo;
                        }

                        return $this->newfilename;


                }else{


					
                        //执行单文件上传
                        //将这些的文件的属性赋值给成员属性
                        if($this->setfiles($name,$size,$tmpname,$mime,$error)){
                                //检测文件相关信息是否符合
								
								//var_dump($this->checksub());
                                if($this->checksize() && $this->checksub() && $this->checkmime()){
                                        
										//移动文件
											
                                        if($this->move()){
										
                                                //返回上传的新文件名
                                                return $this->newfilename;
                                        }else{

                                                return false;
                                        }

                                }else{
                                        return false;

                                }
                        }else{

                                return false;
                        }

                }
        }
        //将上传文件的属性赋值给成员属性 $_FILES 
        private function setfiles($name,$size,$tmpname,$mime,$error){
                //如果存在错误号 // 只有错误号等译0的时候是上传成功
                if($error){
                        $this->setoption('errornum',$error);
                        return false;
                }
                $this->orgname=$name;
                $this->size=$size;
                $this->tmpname=$tmpname;
                $this->mime=$mime;
                $subfix=explode('.',$this->orgname);
                $this->subfix=strtolower(array_pop($subfix));// 文件的后缀名
                //检测是否开启随机文件名，将文件名赋值给成员属性
                if(!$this->israndname){
                        //不启用的话新文件名为前缀拼接原文件名
                        $this->newfilename=$this->prefix.$this->orgname;

                }else{
                        //启用的话,调用创建新文件名的方法
                        $this->newfilename=$this->prefix.$this->createnewname();
                }

                return true;
        }
        //检测文件后缀是否符合
        private function checksub(){

				
                if(in_array($this->subfix,$this->allowsub)){

                        return true;
                }else{
						$this->setoption('errornum',-4);
                        return false;

                }
        }
        //检测文件的MIME类型
        private function checkmime(){
                if(in_array($this->mime,$this->allowmime)){

                        return true;
                }else{
						 $this->setoption('errornum',-5);
                        return false;
                }
        }
        //检测文件的大小是否符合
        private function checksize(){
		
                if($this->size>$this->maxsize){

                        $this->setoption('errornum',-3);
                        return false;
                }else{

                        return true;
                }
				
        }
        //文件的移动的方法
        private function move(){
                //检测是不是上传文件

                if(is_uploaded_file($this->tmpname)){
                        //检测文件移动是否成功
                        if(move_uploaded_file($this->tmpname,$this->filepath.$this->newfilename)){

                               // echo '文件上传成功';
                                return true;
                        }else{

                                $this->setoption('errornum',-7);

                                return false;
                        }

                }else{

                        $this->setoption('errornum',-6);

                        return false;
                }



        }

        //创建新文件名的方法
        private function createnewname(){

                return uniqid().'.'.$this->subfix;


        }
        //检测的路径
        private function checkpath(){

                if(empty($this->filepath)){
                        $this->setoption('errornum',-1);
                        return false;
                }
                // images/
                // images
                //
                // 保证用户传进来的参数 不管有没有/ 最后都要加上'/'
                $this->filepath=rtrim($this->filepath,'/').'/';// rtirm('abc','c') 清楚2侧空格

                if(!file_exists($this->filepath) || !is_writeable($this->filepath)){
                        if(mkdir($this->filepath,0777,true)){

                                return true;
                        }else{

                                $this->setoption('errornum',-2);
                                return false;
                        }
                }else{

                        return true;

                }

        }
        public function geterror(){

                switch($this->errornum){

                        case -1:
                                $string='请指定上传目录';
                                break;
                        case -2:
                                $string='文件路径穿件失败';
                                break;
                        case -3:
                                $string='文件超过了指定的大小';
                                break;
                        case -4:
                                $string='文件后缀不允许';
                                break;
                        case -5:
                                $string='文件的MIME类型不允许';
                                break;
                        case -6:
                                $string='不是上传文件';
                                break;
                        case -7:
                                $string='移动文件失败';
                                break;
						case -8:
                                $string='文件域名错误';
                                break;
                        case 1:
                                $string='超过了PHP指定的大小';
                                break;
                        case 2:
                                $string='超过了表单准许的大小';
                                break;
                        case 3:
                                $string='文件只有部分被上传';
                                break;
                        case 4:
                                $string='没有文件被上传';
                                break;
                        case 6:
                                $string='没有找到临时文件夹';
                                break;
                        case 7:
                                $string='文件写入失败';
                                break;
                }
                return $string;
        }



}

?>
