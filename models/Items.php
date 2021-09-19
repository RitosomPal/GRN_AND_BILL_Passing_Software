<?php
    class Items {
        // Static Methods
        # Create
        public static function Create($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $name = htmlspecialchars(strip_tags($item_name));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM items WHERE name = :name';
            $sql1 = 'INSERT INTO items SET name = :name';

            // Main Function
            if ($P['success'] && $P['data']['items']['create']) {
                try {
                    $conn = $db->connect();

                    // Check if item name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Insert Item
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->execute();

                        return array('msg'=>["Item Added!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Item Already Exists!"], 'success'=>false);
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
            $name = htmlspecialchars(strip_tags($item_name));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM items WHERE name = :name AND id <> :id';
            $sql1 = 'UPDATE items SET name = :name WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['items']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if item name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Update Item
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        return array('msg'=>["Updated!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Item Already Exists!"], 'success'=>false);
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
            $id = htmlspecialchars(strip_tags($id));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM items WHERE id = :id';
            $sql1 = 'DELETE FROM items WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['items']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if item name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() == 1) {
                        // Delete Item
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        return array('msg'=>["Deleted!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Item Dose Not Exists!"], 'success'=>false);
                    }
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
            $sql0 = 'SELECT * FROM items';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Items
                $stmt = $conn->prepare($sql0);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["All Items!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["No Item!"], 'success'=>false);
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
        # Fetch Info
        public static function FetchInfo($db, string $id) {
            // Clean Data
            $id = htmlspecialchars(strip_tags($userId));
            
            // All SQL Commands
            $sql0 = 'SELECT * FROM items WHERE id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Item Info
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["Item Info!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["Item Not Found!"], 'success'=>false);
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
    }