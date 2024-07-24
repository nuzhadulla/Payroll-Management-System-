<?php
/**
 * Project:    Mmotop : Common Class
 * File:       class.common.php
 * @link http://www.mmotop.com/
 * @copyright 2008 EABP,.
 * @package MMotop
 * @version 1.0.0
 */
require_once(MAIN_COMMON_PATH."class.ExtentedDB.php");
class Common extends extendsClassDB{
	var $Request;
	var $SQLArray;
	function Common() {
		$this->Request 	= array();
		$this->SQLArray	= array();
		$this->extendsClassDB();
	}
	function getSelectQuery($strSQL,$uSelect=0,$queryType=false){
		$this->dbSetQuery($strSQL,"select",$uSelect);
		return $this->MakeStripSlashes($this->dbSelectQuery());
	}
	function ExecuteQry($strSQL,$strSQLType = "update")
	{
		global $objSmarty;
		$this->dbSetQuery($strSQL,$strSQLType);
		$this->dbExecuteQuery();
	}
	function doInsert($strTableName,$objFieldsArray)
	{
		global $objSmarty;
		if(is_array($objFieldsArray))
		{
			$strInsertFields = "";
			$strInsertValues = "";
			for($i=0;$i<count($objFieldsArray);$i++)
			{
				$strInsertFields.= $objFieldsArray[$i]["Field"];
				$strInsertValues.= "'".str_replace("?","",mb_convert_encoding(addslashes($objFieldsArray[$i]["Value"]), "ASCII"))."'";
				if($i<count($objFieldsArray)-1)
				{
					if($objFieldsArray[$i]["Field"]!=""){
					$strInsertFields.=", ";
					$strInsertValues.=", ";
					}
				}
			}
			$strInsertQry = "INSERT INTO $strTableName($strInsertFields) VALUES($strInsertValues)";
			$this->ExecuteQry($strInsertQry);
			$InsertId = mysqli_insert_id($this->dbLink);
			return $InsertId;
		}
		else
		{
			$objSmarty->assign("strErrorMsg","Error while adding new Data, Fields array is empty");
			return false;
		}
	}
	function doUpdate($strTableName,$objFieldsArray,$WhereClause)
	{
		if(is_array($objFieldsArray))
		{
			$strUpdateFields = "";
			for($i=0;$i<count($objFieldsArray);$i++)
			{
				
				$strUpdateFields.= $objFieldsArray[$i]["Field"]."="."'".str_replace("?","",mb_convert_encoding(addslashes($objFieldsArray[$i]["Value"]), "ASCII"))."'";
				if($i<count($objFieldsArray)-1)
				{
					if($objFieldsArray[$i]["Field"]!=""){
					$strUpdateFields.=", ";
					}
				}
			}
			$strUpdateQry = "UPDATE $strTableName SET $strUpdateFields $WhereClause";
			//print $strUpdateQry."</br>"; exit;
			$this->ExecuteQry($strUpdateQry);
			return true;
		}
		else
		{
			return false;
		}
	}
	function doDelete($strTableName,$WhereClause=''){
		if(empty($WhereClause)){
			$WhereClause = '';
		}
		$strDeleteQuery = " DELETE FROM ".$strTableName." WHERE ".$WhereClause;
		return $this->ExecuteQry($strDeleteQuery,"delete");
	}
	function AddInfoToDB($objArray,$Prefix,$TableName){
		$counter = 0;
		foreach($objArray as $key=>$value){
			$pos = strpos($key, $Prefix);
			if (!is_integer($pos)) {
			}else{
				$key = str_replace($Prefix,"",$key);
				$insertArray[$counter]["Field"] = $key;
				$insertArray[$counter]["Value"] = stripslashes($value);
				$counter++;
			}
		}
		$insert_id = $this->doInsert($TableName,$insertArray);
		return $insert_id;
	}
	function UpdateInfoToDB($objArray,$Prefix,$TableName,$Where){
		$counter = 0;
		foreach($objArray as $key=>$value){
			$pos = strpos($key, $Prefix);
			if (!is_integer($pos)) {
			}else{
				$key = str_replace($Prefix,"",$key);
				$UpdateArray[$counter]["Field"] = $key;
				$UpdateArray[$counter]["Value"] = $value;
				$counter++;
			}
		}
		$res =$this->doUpdate($TableName,$UpdateArray,$Where);
		return $res;
	}
	function prePopulateForm(){
		global $objSmarty,$globalVal;
			foreach($_REQUEST as $key=>$value)	{
			if(!is_integer($key))	{
					$$key = $value;
					 $globalVal[$key] = $value;
					$objSmarty->assign($key,$value);	
				}
			}	
	}
	function unPopulateForm(){
		global $objSmarty,$globalVal;
			foreach($_REQUEST as $key=>$value)	{
			if(!is_integer($key))	{
					$$key = $value;
					 $globalVal[$key] = $value;
					$objSmarty->assign($key,'');	
				}
			}	
	}
	function getBrowserType() {
		$ua = $_SERVER[HTTP_USER_AGENT]; 
		if (strpos($ua,'MSIE')>0)
		{
		  $B_Name="MSIE";
		  $B_Name1=1;
		}else if (strpos($ua,'Netscape')>0)
		{
		  $B_Name="Netscape";
		  $B_Name1=2;
		}else if (strpos($ua,'Safari')>0)
		{
		  $B_Name="Safari";
		  $B_Name1=2;
		}
		else
		{
		  $B_Name="Firefox";
		  $B_Name1=2;
		}
		return $B_Name;
	}
	function getRequestValues() {
			global $_GET,$_POST;
			return $_GET;
	}
	function setPerPage($intTotal,$intLimit,$intViewCount,$intPage=1,$request='',$ResPage='',$RequestPage='') {		
	global $objSmarty,$global_config;
			$intPageBreak = 5;
			if($request=="")
				$request='p';				
			if($intPage == "" || $intPage == 0)
				$intPage = 1;
			if($intTotal==0)
				return;
			$intPageCount = ceil($intTotal/$intLimit);
			$objRequest = $this->getRequestValues();	
			if($global_config["Mod_Rewrite"]=='ON') {
				$strPage ='';
				$getsearch = $_SERVER['REQUEST_URI'];
				$IsSearch = substr_count($getsearch,'search/');
			} else {
				if($RequestPage=="")
					$strPage = $_SERVER["SCRIPT_NAME"]."?";
				else
					$strPage ='';
			}	
			foreach($objRequest as $Key=>$Value)
			{
				if($Key != $request)
					$strPage = $strPage.stripslashes($Value)."/";
			}
			$strPageList = array();
			$j = 0;
			$intPageStart = floor($intPage/$intPageBreak) * $intPageBreak;
			if($intPageStart>0){
				$intPageStart = $intPage-2;
			}
			if($ResPage !=""){
				if($intPageCount > $ResPage){
				$intPageCount = $ResPage;	
				}
			}	
			for($i=$intPageStart;$i<$intPageCount;$i++)
			{
				if($j < $intPageBreak || $i==$intPageCount-1 || $i==$intPageStart)
				{
					if($i==$intPageStart){
						$strPageList[$j]["Page"] = 1;
						if($intPage != ($i+1)) {
							if($IsSearch==1)
								$strPageList[$j]["Link"] = $global_config["SiteGlobalPath"].'search/'.$strPage."$request-".(1);
							else
								$strPageList[$j]["Link"] = $global_config["SiteGlobalPath"].$strPage."$request-".(1);
									
						} else {
							$strPageList[$j]["Link"] = "";
						}	
					}else{
						$strPageList[$j]["Page"] = $i+1;
						if($intPage != ($i+1)) {
							if($IsSearch==1)
								$strPageList[$j]["Link"] = $global_config["SiteGlobalPath"].'search/'.$strPage."$request-".($i+1);
							else
								$strPageList[$j]["Link"] = $global_config["SiteGlobalPath"].$strPage."$request-".($i+1);	
						}	
						else
							$strPageList[$j]["Link"] = "";
					}
					$j++;
				}
				
			}
			if($intPageCount > $intPageBreak){
				$objSmarty->assign("BreakPage",$intPageBreak);
				$objSmarty->assign($request."BreakPage",$intPageBreak);
			}
			if($intPage != 1){
				if($IsSearch==1)
					$objSmarty->assign("PreviousPage",$global_config["SiteGlobalPath"].'search/'.$strPage."$request-".($intPage - 1));
				else
					$objSmarty->assign("PreviousPage",$global_config["SiteGlobalPath"].$strPage."$request-".($intPage - 1));
				$objSmarty->assign($request."PreviousPage",$strPage."$request=".($intPage - 1));
				$objSmarty->assign($request."PrevViewPage",($intPage - 1));
				$objSmarty->assign("PrevViewPage",($intPage - 1));
			}
			if($intPage != $intPageCount){
				if($IsSearch==1)
					$objSmarty->assign("NextPage",$global_config["SiteGlobalPath"].'search/'.$strPage."$request-".($intPage + 1));
				else
					$objSmarty->assign("NextPage",$global_config["SiteGlobalPath"].$strPage."$request-".($intPage + 1));
				$objSmarty->assign($request."NextPage",$strPage."$request=".($intPage + 1));
				$objSmarty->assign($request."NextViewPage",($intPage + 1));
				$objSmarty->assign("NextViewPage",($intPage + 1));
			}
			$objSmarty->assign("PageList",$strPageList);
			$objSmarty->assign($request."PageList",$strPageList);
		}
		function setLimitQuery($strSQL, $intStart, $intLimit){
			if($intLimit != 0) {
				if($intStart != 0) {
					if($intStart < 0)
						$intStart = 0;
					$strSQL = $strSQL." LIMIT ".$intStart.",".$intLimit;
				} else
					$strSQL = $strSQL." LIMIT ".$intLimit;
			}
			return $strSQL;
		}
	/**
		 * Apply stripslashes function for array of values 
		 * @param 	ToStripslash (array)			
		 * @return  Stripped array
	*/
	function MakeStripSlashes($array,$replaceValue='',$replaceValueTo='') {
		if($array) {
			foreach($array as $key=>$value) {
				if(is_array($value))  {
						$value=$this->MakeStripSlashes($value);
						if($replaceValue==''&&$replaceValueTo=='')
							$array_temp[$key]=str_replace("#AMP#","",$value);
						else
							$array_temp[$key]=str_replace($replaceValue,$replaceValueTo,$value);                      
				}
				else
						$array_temp[$key]=stripslashes(stripslashes($value));
			}    
		}  
		//return; 
		if(is_array($array_temp)) {
			return $array_temp;   
		}
	}
		
		function getMatches($strMatch,$strContent) {
			if(preg_match_all($strMatch,$strContent,$objMatches)){
				return $objMatches;
			}
			return "";
		}
		function doPrint($strContent) {
			print $strContent."<br>";flush();
		}
		function getFileContentByFile($objFileName) {
			return file_get_contents($objFileName);
		}
		function makeStrip($value) {
			$Data 	= ltrim($value);	
			$Data 	= rtrim($Data);	
			$Data 	= strtolower($Data);	
			$Data 	= htmlentities($Data);	
			$Data 	= stripslashes($Data);	
			$Data 	= strip_tags($Data);	
			$array1 = array(" ","'","[","]","->","<","amp;","&","--",".","gt;","@","?",",");
			$array2 = array("-","","","","","","","","-","","","","","");
			$Data 	= str_replace($array1,$array2,$Data);
			return $Data;
		}		
		function doRearrangeURlLinks($objLink){
			global $global_config;
			$strCurrrentLink = '';
			$strCurrrentPos	 = 0;
			$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
			if (eregi($urlregex, $objLink)) {
				return $objLink;
			} else {
				return  $global_config["SiteGlobalPath"].$objLink;
			}
			return $strCurrrentLink;
		}
		function printarry($str){
				print "<pre>";
				print_r($str);
				print "</pre>";
		}	
		
		function friendlyURL($string){
				$string = preg_replace("`\[.*\]`U","",$string);
				$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
				$string = htmlentities($string, ENT_COMPAT, 'utf-8');
				$string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
				$string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);
				return strtolower(trim($string, '-'));
			}
	function createCropThumb($sourceImageFile,$thumbImageFile,$newWidth,$newHeight) {
	   
		$strFileName	= $sourceImageFile;
		$cropHeight		= $newHeight;
		$cropWidth		= $newWidth;
		$fileType		= explode('.', $strFileName); 
		$fileType		= $fileType[count($fileType) -1];
		$fileType		= strtolower($fileType);
	
		$originalImageSize 	= getimagesize($strFileName);
		$originalWidth 		= $originalImageSize[0];
		$originalHeight 	= $originalImageSize[1];
	
		if($fileType=='jpg') 
		{
			$originalImageGd = imagecreatefromjpeg($strFileName);
		}
		
		if($fileType=='gif') 
		{ 
			$originalImageGd = imagecreatefromgif($strFileName);
		}	
		
		if($fileType=='png') 
		{
			$originalImageGd = imagecreatefrompng($strFileName);
		}
		
		
		if($fileType=='bmp') 
		{
			$originalImageGd = imagecreatefromjpeg($strFileName);
		}

		
		$croppedImageGd = imagecreatetruecolor($cropWidth, $cropHeight);
		
		$wm = $originalWidth /$cropWidth;
		$hm = $original_height /$cropHeight;
		$h_height = $cropHeight/2;
		$w_height = $cropWidth/2;
		
		$transparent = imagecolorallocate($croppedImageGd, 255, 255, 255);
		imagefill($croppedImageGd, 0, 0, $transparent);
		imagecolortransparent($croppedImageGd, $transparent);
	
		
		if($original_width > $original_height ) 
		{
			$adjusted_width = $originalWidth / $hm;
			$half_width 	= $adjusted_width / 2;
			$int_width 		= $half_width - $w_height;
			imagecopyresampled($croppedImageGd ,$originalImageGd ,-$int_width,0,0,0, $adjusted_width, $cropHeight, $originalWidth , $originalHeight );
		} 
		elseif(($original_width < $original_height ) || ($original_width == $original_height ))
		{
			$adjusted_height = $originalHeight / $wm;
			$half_height = $adjusted_height / 2;
			$int_height = $half_height - $h_height;
			imagecopyresampled($croppedImageGd , $originalImageGd ,0,-$int_height,0,0, $cropWidth, $adjusted_height, $originalWidth , $originalHeight );
		} 
		else {
			imagecopyresampled($croppedImageGd , $originalImageGd ,0,0,0,0, $cropWidth, $cropHeight, $originalWidth , $originalHeight );
		}
		
		if($fileType=='jpg') 
		{
			imagejpeg($croppedImageGd,$thumbImageFile); 
		}
		
		if($fileType=='gif') 
		{ 
			imagegif($croppedImageGd,$thumbImageFile); 
		}	
		
		if($fileType=='png') 
		{
			imagepng($croppedImageGd,$thumbImageFile);
		}
		
		if($fileType=='bmp') 
		{
			imagepng($croppedImageGd,$thumbImageFile);
		}
		
		imagedestroy($croppedImageGd); 
		imagedestroy($originalImageGd); 
	}	
	function resizetheUploadImage($photo){
	 //RESIZE UPLOADED IMAGE TO SAVE SPACE ON SERVER

		   $set_height   = "900"; // maximum height allowed
		   $set_width    = "600"; // maximum width allowed

		   $_GET['src'] = $photo;
			 if($ext=='png'){
			   $image = imagecreatefrompng($_GET['src']);
			}else if($ext=='gif'){
			   $image = imagecreatefromgif($_GET['src']);
			}else{
			   $image = imagecreatefromjpeg($_GET['src']);
			}
		   //$image = imagecreatefromjpeg($_GET['src']);
		   $size = getimagesize($_GET['src']);

		   $new_w = $size[0];
		   $new_h = $size[1];
		   $resized = imagecreatetruecolor($new_w, $new_h);


		   if ($size[0] > $set_width){ // resizes if max width is violated
				 $new_w = "$set_width";
				 $new_h = round(($set_width/$size[0])*$size[1]);
				 $resized = imagecreatetruecolor($new_w, $new_h); 

		   }

		   if ($size[1] > $set_height){ // resizes if max height is violated
				 $new_h = "$set_height";
				 $new_w = round(($set_height/$size[1])*$size[0]);
				 $resized = imagecreatetruecolor($new_w, $new_h);

		   }


		   if (($size[0] > $set_width) || ($size[1] > $set_height)){ // resizes if max height & max width is violated
				 $new_w = "$set_width";
				 $new_h = round(($set_width/$size[0])*$size[1]);
				 if($new_h > $set_height){
					  $new_h = "$set_height";
					  $new_w = round(($set_height/$new_h)*$new_w);
				 }
				 $resized = imagecreatetruecolor($new_w, $new_h); 

		   }

		   $ow = $size[0];
		   $oh = $size[1];

		   $new_image_resized = $_GET['src'];
		   imagecopyresampled($resized, $image, 0, 0, 0, 0, $new_w, $new_h, $ow, $oh);
		  // imagejpeg($resized, $new_image_resized, 80);
		  if($ext=='png'){
			  imagepng($resized, $new_image_resized, 8);
			}else if($ext=='gif'){
			  imagegif($resized, $new_image_resized, 80);
			}else{
			  imagejpeg($resized, $new_image_resized, 80);
			}
		   imagedestroy($resized);
		   imagedestroy($image);
	}
	
	function createthumb($input_file_name, $output_filename, $new_w, $new_h='') {
		if (preg_match("/(jpg|jpeg)$/i",$input_file_name)){
			$src_img = imagecreatefromjpeg($input_file_name);
		} else if (preg_match("/png$/i",$input_file_name)){
			$src_img = imagecreatefrompng($input_file_name);
		} else if(preg_match("/bmp$/i",$input_file_name)){
			 $src_img = imagecreatefromjpeg($input_file_name);
		} else if(preg_match("/gif$/i",$input_file_name)){
			$src_img = imagecreatefromgif($input_file_name);
		} else {
			throw(new Exception("ERROR: Cant work with file $input_file_name becuase its an unsupported file type for this function."));
		}
	
		if( $src_img == false ) {
			throw(new Exception("ERROR: Unabel to open image file $input_file_name"));
		}
	
		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);
	
		if( $new_h == 0 ) {
			$thumb_w = $new_w;
					$thumb_h = $old_y * ($new_w / $old_x);
	
		} else if( $new_w == 0 ) {
			$thumb_h = $new_h;
					$thumb_w = $old_x * ($new_h / $old_y);
		} else {
			if ($old_x > $old_y) 	{
				$thumb_w = $new_w;
				$thumb_h = $old_y * ($new_h/$old_x);
			} else if ($old_x < $old_y) {
				$thumb_w = $old_x * ($new_w/$old_y);
				$thumb_h = $new_h;
			} else if ($old_x == $old_y) {
				$thumb_w = $new_w;
				$thumb_h = $new_h;
			}
		}
	
		$dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	
		if (preg_match("/png$/i",$input_file_name)){
			imagepng($dst_img,$output_filename); 
		} else {
			imagejpeg($dst_img,$output_filename); 
		}
	    return $output_filename;
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
	}
	function convert2thumb($photo,$ImagePath,$ThumbPath,$newwidth,$newheight) {
		$myreturn=false;
		$show_all=true;
		$thumbs_width=$newwidth;
		$mynewimg='';
		if (extension_loaded('gd')) {
			if ($imginfo=getimagesize($ImagePath."$photo")) {
			   $fileType = substr(strrchr($ImagePath."$photo",'.'),1);
				$width=$imginfo[0];
				$height=$imginfo[1];
				if ($width>$thumbs_width) {
					$newwidth=$thumbs_width;
					//$newheight=$newheight;
					$newheight=$height*($thumbs_width/$width);
					if ($imginfo[2]==1) {		//gif
							if (function_exists('imagecreatefromgif')) {
								//print "gif--";
								 $myimg=imagecreatefromgif($ImagePath."$photo");
							}
					}else if($imginfo[2]==3){
							if (function_exists('imagecreatefrompng')) {
								 $myimg=imagecreatefrompng($ImagePath."$photo");
						   }
					}else if($imginfo[2]==2) {		//jpg
							if (function_exists('imagecreatefromjpeg')) {
								 $myimg=imagecreatefromjpeg($ImagePath."$photo");
							}
					}
					if (isset($myimg) && !empty($myimg)) {
						$gdinfo=$this->_gdinfo();
						if (stristr($gdinfo['GD Version'], '2.')) {	// if we have GD v2 installed
							$mynewimg=@imagecreatetruecolor($newwidth,$newheight);
							if (imagecopyresampled($mynewimg,$myimg,0,0,0,0,$newwidth,$newheight,$width,$height)) {
								$show_all=false;
							}
						} else {	// GD 1.x here
							$mynewimg=@imagecreate($newwidth,$newheight);
							if (@imagecopyresized($mynewimg,$myimg,0,0,0,0,$newwidth,$newheight,$width,$height)) {
								$show_all=false;
							}
						}
					}
				}
				
			}
	}

	if (!is_writable($ThumbPath)) {
		@chmod($ThumbPath,0755);
		if (!is_writable($ThumbPath)) {
			@chmod($ThumbPath,0777);
		}
	}
	if ($show_all) {
		$myreturn=@copy($ThumbPath."$photo",$ThumbPath."$photo");
	} else {
		$myreturn=@imagejpeg($mynewimg,$ThumbPath."/$photo",100);
	}
	@chmod($ThumbPath."/$photo",0644);
	return $myreturn;
	}
	
function _gdinfo() {
	$myreturn=array();
	if (function_exists('gd_info')) {
		$myreturn=gd_info();
	} else {
		$myreturn=array('GD Version'=>'');
		ob_start();
		phpinfo(8);
		$info=ob_get_contents();
		ob_end_clean();
		foreach (explode("\n",$info) as $line) {
			if (strpos($line,'GD Version')!==false) {
				$myreturn['GD Version']=trim(str_replace('GD Version', '', strip_tags($line)));
			}
		}
	}
	return $myreturn;
	}	
	function checkFile($filename){
	  if (file_exists($filename)) {
		return 1;
	  }
	}
	function removeLastChar($rtnStr){
	 $res = substr($rtnStr, 0, strlen($rtnStr)-1);
	 return $res;
	}
	function removeFirstChar($rtnStr){
	 $res = substr($rtnStr, 1);
	 return $res;
	}	
	function delTree($dir) {
		$files = glob( $dir . '*', GLOB_MARK );
		foreach( $files as $file ){
			if( substr( $file, -1 ) == '/' )
				delTree( $file );
			else
				unlink( $file );
		}
		if (is_dir($dir)) rmdir( $dir );
	   
	} 	
	function slugName($string){
				$string = preg_replace("`\[.*\]`U","",$string);
				$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
				$string = htmlentities($string, ENT_COMPAT, 'utf-8');
				$string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
				$string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);
				return strtolower(trim($string, '-'));
   }	
    function sendMail($objArray){
	    if($objArray['mail_count']!=2){
			require_once(MAIN_CLASS_PATH."Email/class.phpmailer.php");
			require_once(MAIN_CLASS_PATH."Email/class.smtp.php");
		}
		
		$mail = new PHPMailer();
		$mail->IsSMTP(); // set mailer to use SMTP
		$mail->SMTPSecure = "ssl";
		$mail->Host = 'mail.busyfriend.com'; // specify main and backup server
		$mail->Port = '465'; // set the port to use 
		$mail->SMTPAuth = true; // turn on SMTP authentication
		
		if($objArray['fromName']=='BusyFriend-Support'){
			$mail->Username = _BUSYFRIEND_SUPPORT_USER_; // your SMTP username or your gmail username
			$mail->Password = _BUSYFRIEND_SUPPORT_PASSWORD_; // your SMTP password or your gmail password
		}else if($objArray['fromName']=='BusyFriend-MatchFinder-Support'){
			$mail->Username = _BUSYFRIEND_MATCHFINDER_SUPPORT_USER_; 
			$mail->Password = _BUSYFRIEND_MATCHFINDER_SUPPORT_PASSWORD_; 
		}else if($objArray['fromName']=='BusyFriend-SocialNetworking-Support'){
			$mail->Username = _BUSYFRIEND_SOCIALNET_SUPPORT_USER_; 
			$mail->Password = _BUSYFRIEND_SOCIALNET_SUPPORT_PASSWORD_; 
		}else if($objArray['fromName']=='BusyFriend-BucketList-Support'){
			$mail->Username = _BUSYFRIEND_BUCKETLIST_SUPPORT_USER_; 
			$mail->Password = _BUSYFRIEND_BUCKETLIST_SUPPORT_PASSWORD_; 
		}
		
		$strFromName   	 = $objArray['fromName'];
		$strToName   	 = $objArray['toName'];
		$strToEmail  	 =$objArray['toEmail'];
		$strFromMail  	 =$objArray['fromEmail'];
		$strSubject   	 =$objArray['sub'];
		$strTemplateCode =$objArray['message'];

		$from = $strFromName; // Reply to this email
		$to=$strToEmail; // Recipients email ID
		$name=$strToEmail; // Recipient's name 

		$mail->From = $strFromMail;
		$mail->FromName = $strFromName; // Name to indicate where the email came from when the recepient received

		$mail->AddAddress($strToEmail,$strToName);
		$mail->AddReplyTo($strFromMail,$strFromName);
		$mail->WordWrap = 50; // set word wrap
		$mail->IsHTML(true); // send as HTML
		$mail->Subject = $strSubject;
		$mail->Body = $strTemplateCode; //HTML Body
		$mail->AltBody = $strTemplateCode; //Text Body
		
		if(!$mail->Send())
		{
			echo "Mailer Error: " . $mail->ErrorInfo;
			
		}
		else
		{
			return true;
		}
    }
	function array_unique_deep($array) {
        $values=array();
        //ideally there would be some is_array() testing for $array here...
        foreach ($array as $part) {
            if (is_array($part)) $values=array_merge($values,array_unique_deep($part));
            else $values[]=$part;
        }
        return array_unique($values);
   		 }
	function msort($array, $id="id", $sort_decending=true) {
        $temp_array = array();
        while(count($array)>0) {
            $lowest_id = 0;
            $index=0;
            foreach ($array as $item) {
                if (isset($item[$id])) {
                                    if ($array[$lowest_id][$id]) {
                    if (strtolower($item[$id]) < strtolower($array[$lowest_id][$id])) {
                        $lowest_id = $index;
                    }
                    }
                                }
                $index++;
            }
            $temp_array[] = $array[$lowest_id];
            $array = array_merge(array_slice($array, 0,$lowest_id), array_slice($array, $lowest_id+1));
        }
             if ($sort_ascending) {
            return $temp_array;
                } else {
                    return array_reverse($temp_array);
                }
        }	
	function filter_by_value ($array, $index, $value){ 
        if(is_array($array) && count($array)>0)  
        { 
            foreach(array_keys($array) as $key){ 
                $temp[$key] = $array[$key][$index]; 
                 
                if ($temp[$key] == $value || $temp[$key]!=$_SESSION['user_id']){ 
                    $newarray[$key] = $array[$key]; 
                } 
            } 
          } 
      return $newarray; 
    } 
	 function instr($haystack, $needle) { 
	  $pos = strpos($haystack, $needle, 0); 
	  if ($pos != 0) return true; 
	  return false; 
	 } 
	 function createthumbNew($input_file_name, $output_filename, $new_w, $new_h='') {
		if (preg_match("/(jpg|jpeg)$/i",$input_file_name)){
			$src_img = imagecreatefromjpeg($input_file_name);
		} else if (preg_match("/png$/i",$input_file_name)){
			$src_img = imagecreatefrompng($input_file_name);
		} else {
			throw(new Exception("ERROR: Cant work with file $input_file_name becuase its an unsupported file type for this function."));
		}
	
		if( $src_img == false ) {
			throw(new Exception("ERROR: Unabel to open image file $input_file_name"));
		}
	
		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);
	
		if( $new_h == 0 ) {
			$thumb_w = $new_w;
					$thumb_h = $old_y * ($new_w / $old_x);
	
		} else if( $new_w == 0 ) {
			$thumb_h = $new_h;
					$thumb_w = $old_x * ($new_h / $old_y);
		} else {
			if ($old_x > $old_y) 	{
				$thumb_w = $new_w;
				$thumb_h = $old_y * ($new_h/$old_x);
			} else if ($old_x < $old_y) {
				$thumb_w = $old_x * ($new_w/$old_y);
				$thumb_h = $new_h;
			} else if ($old_x == $old_y) {
				$thumb_w = $new_w;
				$thumb_h = $new_h;
			}
		}
	
		$dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	
		if (preg_match("/png$/i",$input_file_name)){
			imagepng($dst_img,$output_filename); 
		} else {
			imagejpeg($dst_img,$output_filename); 
		}
	
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
	}
}
?>