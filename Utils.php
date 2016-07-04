<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Utils extends Controller
{
    public static function saveImage($file,$path){
        $image_name = self::generateRandomString().time() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $image_name);
        return $image_name;
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // To remove the unwanted attributes of an obj/obj array
    public static function unsetAttributes($mixed, $attr_mixed){
        if(is_array($mixed)){
            $is_array = is_array($attr_mixed);
            foreach($mixed as $key => $item){
                if($is_array){
                    foreach($attr_mixed as $attr){
                        if(is_array($item)){
                            unset($item[$key]);
                        } else{
                            unset($item->{$attr});
                        }
                    }
                } else{
                    if(is_array($item)){
                        unset($item[$key]);
                    } else{
                        unset($item->{$attr_mixed});
                    }
                }
            }
        } else{
            if(is_array($attr_mixed)){
                foreach($attr_mixed as $attr){
                    if(is_array($mixed)){
                        $mixed[$attr];
                    } else{
                        unset($mixed->{$attr});
                    }
                }
            } else{
                if(is_array($mixed)){
                    $mixed[$attr_mixed];
                } else{
                    unset($mixed->{$attr_mixed});
                }
            }
        }
        return $mixed;
    }

    public static function deleteFile($file){
        if(is_file($file) && file_exists($file)){
            unlink($file);
        }
    }

    // To get & set Absolute URLs of images for the given obj/obj array
    public static function getAbsoluteUrls($obj_mixed, $attr, $path){
        /*$route_uri = Route::getCurrentRoute()->getPath();
        if(strpos($route_uri,'webservice')!==false){
            $pos = strpos($path,'/',strrpos($path,'uploads'));
            if($pos!==false){
                $path = substr_replace($path, 'thumbs/', $pos+1, 0);
            } else{
                $path = $path.'/thumbs';
            }
        }*/
        if(is_array($obj_mixed)){
            foreach($obj_mixed as $key => $obj){
                if(is_array($obj)){
                    if($obj_mixed[$key][$attr]!='' || $obj_mixed[$key][$attr]!=null){
                        $obj_mixed[$key][$attr] = url($path.$obj[$attr]);
                    }
                } else{
                    if($obj->{$attr}!='' || $obj->{$attr}!=null) {
                        $obj->{$attr} = url($path . $obj->{$attr});
                    }
                }
            }
        } else{
            if($obj_mixed->{$attr}!='' || $obj_mixed->{$attr}!=null) {
                $obj_mixed->{$attr} = url($path . $obj_mixed->{$attr});
            }
        }
        return $obj_mixed;
    }

    public static function uniqueMultidimArray($mix_array, $key) {
        $temp_array = array();
        $key_array = array();
        foreach($mix_array as $val) {
            if(is_array($val)){
                if (!in_array($val[$key], $key_array)) {
                    array_push($key_array,$val[$key]);
                    array_push($temp_array,$val);
                }
            } else{
                if (!in_array($val->{$key}, $key_array)) {
                    array_push($key_array,$val->{$key});
                    array_push($temp_array,$val);
                }
            }
        }
        return $temp_array;
    }

    public static function isUrlExist($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){
            $status = true;
        }else{
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    public static function getDistanceBtwLatLng($s_lat, $s_lng, $d_lat, $d_lng,$formatted=true){
        $s_lat = doubleval($s_lat);
        $s_lng = doubleval($s_lng);
        $d_lat = doubleval($d_lat);
        $d_lng = doubleval($d_lng);
        $distance = ( 6371 * acos( cos( deg2rad($s_lat) ) * cos( deg2rad( $d_lat ) ) * cos( deg2rad( $d_lng ) - deg2rad($s_lng) )
                + sin( deg2rad($s_lat) ) * sin( deg2rad( $d_lat ) ) ) );
        if($distance<1){
            return ($formatted) ? round($distance*1000,2).' m' : round($distance*1000,2);
        }
        return ($formatted) ? round($distance,3).' KM' : round($distance,3);
    }

    public static function removeDuplicates($array, $obj_attr_identifier){
        $array1 = $array;
        foreach($array as $key => $value){
            foreach($array1 as $key1 => $value1){
                if($value->{$obj_attr_identifier}==$value1->{$obj_attr_identifier} && $key1!=$key){
                    unset($array1[$key]);
                }
            }
        }
        return array_values($array1);
    }

    public static function getCombinations($str) {
        $words = explode(' ',$str);
        $elements = pow(2, count($words))-1;

        $result = array();

        for ($i = 1; $i<=$elements; $i++){
            $bin = decbin($i);
            $padded_bin = str_pad($bin, count($words), "0", STR_PAD_LEFT);

            $res = array();
            for ($k=0; $k<count($words); $k++){
                //append element, if binary position says "1";
                if ($padded_bin[$k]==1){
                    $res[] = $words[$k];
                }
            }

            sort($res);
            $result[] = implode(" ", $res);
        }
        sort($result);
        return $result;
    }

    public static function resizeAndSaveImage($file_path, $destination_path, $width, $height){
        $dir = substr($destination_path,0,strrpos($destination_path,'/'));
        if(!(is_dir($dir))){
            return false;
        }
        $img = Image::make(imagecreatefromjpeg($file_path));
        $height = ($img->height()/$img->width())*$width;
        /*if($img->width() > $img->height()){
            $height = ($img->height()/$img->width())*$width;
        } else{
            $width = ($img->width()/$img->height())*$height;
        }*/
        $img->resize($width, $height);
        if($img->save($destination_path)){
            return true;
        }
        return false;
    }

    // To send success JSON response for web services
    public static function sendSuccessJsonResponse($msg, $data=null, $extra_key_val_pairs=null){
        $response = ['status'=>'1','message'=>$msg];
        if($data!=null){
            $response['data'] = $data;
        }
        if($extra_key_val_pairs!=null){
            foreach ($extra_key_val_pairs as $key => $value) {
                $response[$key] = $value;
            }
        }
        return response()->json($response)->header('Content-Type', 'application/json');
    }

    // To send failure JSON response for web services
    public static function sendFailureJsonResponse($msg, $data=null, $extra_key_val_pairs=null){
        $response = ['status'=>'0','message'=>$msg];
        if($data!=null){
            $response['data'] = $data;
        }
        if($extra_key_val_pairs!=null){
            foreach ($extra_key_val_pairs as $key => $value) {
                $response[$key] = $value;
            }
        }
        return response()->json($response)->header('Content-Type', 'application/json');
    }
}
