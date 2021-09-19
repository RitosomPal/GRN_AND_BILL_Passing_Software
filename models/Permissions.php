<?php
    class Permissions {
        // Static Methods
        # User Permissions
        public static function getUserPermissions($db, string $userId) {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));

            // All SQL Commands
            $sql0 = 'SELECT groupId FROM users WHERE id = :id';
            $sql1 = 'SELECT permission FROM groups WHERE id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Check for valid Id
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                if ($stmt->rowCount() == 1) {
                    $groupId = $stmt->fetch(PDO::FETCH_ASSOC)['groupId'];
                    $stmt = $conn->prepare($sql1);
                    $stmt->bindParam(':id', $groupId);
                    $stmt->execute();
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);
                    $data = Permissions::toArray($data['permission']);
                    return array('msg'=>"Successful!", 'data'=>$data, 'success'=>true);
                } else { 
                    return array('msg'=>'User Not Found!', 'success'=>false);   
                }
            }
            catch(PDOException $e) {
                return array('msg'=>"System Error! => ".$e->getMessage(), 'success'=>false);
            }
        }
        # Post to Permissions
        public static function sanatizePost(array $post) {
            $p = array( 'create' => 0, 'view' => 0, 'modify' => 0, 'report' => 0 );
            $permission = array( 'groups'=>$p, 'users'=>$p, 'suppliers'=>$p, 'items'=>$p, 'units'=>$p, 'books'=>$p, 'grn'=>$p, 'bpm'=>$p );

            if(isset($post['s'])) {
                $c = 0;
                foreach ($p as $key => $value) {
                    if(isset($post['s'][$c])) { $permission['suppliers'][$key] = 1; }
                    $c++;
                }
            }

            if(isset($post['i'])) {
                $c = 0;
                foreach ($p as $key => $value) {
                    if(isset($post['i'][$c])) { $permission['items'][$key] = 1; }
                    $c++;
                }
            }

            if(isset($post['u'])) {
                $c = 0;
                foreach ($p as $key => $value) {
                    if(isset($post['u'][$c])) { $permission['units'][$key] = 1; }
                    $c++;
                }
            }

            if(isset($post['bk'])) {
                $c = 0;
                foreach ($p as $key => $value) {
                    if(isset($post['bk'][$c])) { $permission['books'][$key] = 1; }
                    $c++;
                }
            }

            if(isset($post['g'])) {
                $c = 0;
                foreach ($p as $key => $value) {
                    if(isset($post['g'][$c])) { $permission['grn'][$key] = 1; }
                    $c++;
                }
            }

            if(isset($post['b'])) {
                $c = 0;
                foreach ($p as $key => $value) {
                    if(isset($post['b'][$c])) { $permission['bpm'][$key] = 1; }
                    $c++;
                }
            }


            return  json_encode($permission);
        }
        # Array To String
        public static function toString(array $permission) {
            return  json_encode($permission);
        }
        # String TO Array
        public static function toArray(string $permission) {
            return  json_decode($permission, true);
        }
    }