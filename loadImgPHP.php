<?php 
$imgWorker = new imageWorker();
if($imgWorker -> saveImg("")){
    echo 'load:done';
};

    
class imageWorker {
         
    function saveImg ($id) {
        if (isset($_FILES['userImg'])){
            $time = time();
            $cutCoordinate = explode(',', $_POST['cuts']);
        
            if ($_FILES["userImg"]["error"] == "UPLOAD_ERR_OK") {
                $tmp_name = $_FILES["userImg"]["tmp_name"];
                $tempType = explode('/', $_FILES["userImg"]["type"]);
                $type = $tempType[1];
       
                //Crop param
                
                $size = getimagesize($tmp_name);
                $realWidth = $size[0];
                $realHeigth = $size[1];
                $cutRight = ($realWidth/100)*$cutCoordinate[1];
                $cutBottom = ($realHeigth/100)*$cutCoordinate[2];
                $cutX = ($realWidth/100)*$cutCoordinate[3];
                $cutY = ($realHeigth/100)*$cutCoordinate[0];
                $newWidth = $realWidth - $cutRight - $cutX;
                $newHeigth = $realHeigth - $cutBottom - $cutY;
                
                //Crop img and make thumbnail 200*150
                $thumbWidth = 200;
                $thumbHight = 150;
                $constrFunc = 'imagecreatefrom'.$type;
                $saveFunc = 'image'.$type;
                
                if ($constrFunc && $saveFunc){
                    $r_img = $constrFunc($tmp_name);
                    $out_img = imagecreatetruecolor($newWidth,$newHeigth);
                    imagecopy($out_img,$r_img, 0,0,$cutX,$cutY,$newWidth,$newHeigth);
                    if($saveFunc($out_img,"userImg/".$id._.$time.".".$type)){ // save crop img
                        $thumb = imagecreatetruecolor($thumbWidth,$thumbHight);
                        imagecopyresampled($thumb,$out_img,0,0,0,0,$thumbWidth,$thumbHight,$newWidth,$newHeigth);
                        if ($saveFunc($thumb,"userImg/".$id._.$time."_thumb.".$type)){ // save thumbnail and clear memoty
                            imagedestroy($r_img);
                            imagedestroy($thumb);
                            imagedestroy($out_img);
                            unlink($tmp_name);
                            return $id._.$time;    
                        }else{
                            imagedestroy($out_img);
                            imagedestroy($thumb);
                            imagedestroy($r_img);
                            unlink($tmp_name);
                            return false;
                        } 
                    }else {
                        imagedestroy($out_img);
                        imagedestroy($r_img);
                        unlink($tmp_name);
                        return false;
                    } 
                }else {
                    unlink($tmp_name);
                    return false;
                }
            }else return false;
        }else return false;
    }        
}