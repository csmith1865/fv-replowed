<?php 

class AvatarService{
    public static function saveAvatar($playerObj, $request){
        $avatar = array();
    
        $avatar["gender"] = $request->params[1];
        $avatar["version"] = "fv_1";
        $avatar["items"] = $request->params[0];

        $playerObj->setAvatar($avatar);

        return [];
    }
}