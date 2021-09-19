<?php
    require_once('Permissions.php');

    class Groups {
        //Static Methods
        # Create
        public static function Create($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $id = htmlspecialchars(strip_tags($userId));
            $name = htmlspecialchars(strip_tags($name));

            // Permission
            $P = Permissions::getUserPermissions($db,$id);
            
            // All SQL Commands
            $sql0 = 'SELECT * FROM groups WHERE name = :name';
            $sql1 = 'INSERT INTO groups SET name = :name, permission = :permission';

            // Main Function
            if ($P['success'] && $P['data']['groups']['create']) {
                try {
                    $conn = $db->connect();

                    // Check if group name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Insert group
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':permission', $permission);
                        $stmt->execute();

                        return array('msg'=>["Group Added!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Group Name Already Exists!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }

        # Edit
        public static function Edit($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $id = htmlspecialchars(strip_tags($id));
            $name = htmlspecialchars(strip_tags($name));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT * FROM groups WHERE name = :name AND id <> :id';
            $sql1 = 'UPDATE groups SET name = :name, permission = :permission WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['groups']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if group name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Insert group
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':permission', $permission);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        return array('msg'=>["Updated!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Group Name Already Exists!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }

        # Delete
        public static function Delete($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $id = htmlspecialchars(strip_tags($group_id));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'DELETE FROM groups WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['groups']['modify']) {
                try {
                    $conn = $db->connect();

                    // Delete Group
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    return array('msg'=>["Deleted!"], 'success'=>true);
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }

        # Fetch All
        public static function FetchAll($db) {
            // All SQL Commands
            $sql0 = 'SELECT * FROM groups WHERE id <> 1';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch All Groups
                $stmt = $conn->prepare($sql0);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>[$stmt->rowCount()." Results found."],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["Add New User!"], 'success'=>false);
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
    }
    