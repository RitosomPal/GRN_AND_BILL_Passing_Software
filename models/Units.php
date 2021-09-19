<?php
    class Units {
        // Static Methods
        # Create
        public static function Create($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $name = htmlspecialchars(strip_tags($unit_name));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM units WHERE name = :name';
            $sql1 = 'INSERT INTO units SET name = :name';

            // Main Function
            if ($P['success'] && $P['data']['units']['create']) {
                try {
                    $conn = $db->connect();

                    // Check if unit name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Insert Unit
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->execute();

                        return array('msg'=>["Unit Added!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Unit Already Exists!"], 'success'=>false);
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
            $name = htmlspecialchars(strip_tags($unit_name));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM units WHERE name = :name AND id <> :id';
            $sql1 = 'UPDATE units SET name = :name WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['units']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if unit name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Update Unit
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        return array('msg'=>["Updated!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Unit Already Exists!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM units WHERE id = :id';
            $sql1 = 'DELETE FROM units WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['units']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if unit name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() == 1) {
                        // Delete Unit
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        return array('msg'=>["Deleted!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Unit Dose Not Exists!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM units';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Units
                $stmt = $conn->prepare($sql0);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["All Units!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["No Unit!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM units WHERE id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Unit Info
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["Unit Info!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["Unit Not Found!"], 'success'=>false);
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
    }