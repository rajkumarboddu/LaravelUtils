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

}
