<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Willywes\AgoraSDK\RtcTokenBuilder;

class AgoraHelper
{
    public static function GetToken($user_id){
    
        $appID = "0dee13f11d3e47339ad6560de67c960d";
        $appCertificate = "400f4dcf56af41d3b05872db66b3ebb2";
        $channelName = md5($user_id);
        $uid = $user_id;
        $uidStr = ($user_id) . '';
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
    
        return RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
    }
}