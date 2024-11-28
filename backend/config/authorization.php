<?php

    
    function hasPermission($userID, $permission) {
        global $conn;
        $query = "
            SELECT COUNT(*) as count
            FROM Utilisateur_Roles ur
            JOIN Role_Permissions rp ON ur.RoleID = rp.RoleID
            JOIN Permissions p ON rp.PermissionID = p.PermissionID
            WHERE ur.UtilisateurID = ? AND p.PermissionName = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('is', $userID, $permission);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    function verifyGoogleToken($token) {
        $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=" . $token;
        $response = file_get_contents($url);
        $responseData = json_decode($response);
        if (isset($responseData->aud) && $responseData->aud == "410184636462-gek0ba84247np4m8ubege1e89vbeuib2.apps.googleusercontent.com") {
            return true;
        } else {
            return false;
        }
    }
    
    function verifyFacebookToken($token) {
        $appId = "Ton-App-Id-Facebook";
        $appSecret = "Ton-App-Secret-Facebook";
        $url = "https://graph.facebook.com/debug_token?input_token={$token}&access_token={$appId}|{$appSecret}";
        $response = file_get_contents($url);
        $responseData = json_decode($response);
        if (isset($responseData->data->is_valid) && $responseData->data->is_valid) {
            return true;
        } else {
            return false;
        }
    }
    

?>