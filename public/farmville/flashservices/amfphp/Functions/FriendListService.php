<?php

class FriendListService{
    public static function getFriendsForR2FlashNeighborFlow($playerObj){
        // We are here to populate the friends for adding a neighbor
        $friendData = $playerObj->getPlayerDataForNeighbor();
        // var_dump($friendData);
        $fvFriends = [];
        foreach ($friendData as $friend){
            $fvFriends[] =  (object) [
                "uid" => $friend['uid'],
                "name" => $friend['name'],
                "first_name" => $friend['firstname'],
                "last_name" => $friend['lastname'],
                "is_app_user" => true,
                "valid" => true,
                "allowed_restrictions" => false,
                "pic_square" => "",
                "pic_big" => ""
            ];
        }

        $currentNeighbors = $playerObj->getCurrentNeighbors();
        $data["data"] = array(
                // We return 1 friend
                "requestedFriends" => (object)[
                    // "GhostNeighbor" => [],
                    "FarmVille" => $fvFriends,
                    "CurrentAllNeighbor" => $currentNeighbors
                ]
            
        );

        return $data;
    }
}