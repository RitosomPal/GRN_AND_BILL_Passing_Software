<?php
    class Suppliers {
        // Static Methods
        # Create
        public static function Create($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $name = htmlspecialchars(strip_tags($supplier_name));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM suppliers WHERE name = :name';
            $sql1 = 'INSERT INTO suppliers SET name = :name';

            // Main Function
            if ($P['success'] && $P['data']['suppliers']['create']) {
                try {
                    $conn = $db->connect();

                    // Check if supplier name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Insert Supplier
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->execute();

                        return array('msg'=>["Supplier Added!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Supplier\'s Name Already Exists!"], 'success'=>false);
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
            $name = htmlspecialchars(strip_tags($supplier_name));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM suppliers WHERE name = :name AND id <> :id';
            $sql1 = 'UPDATE suppliers SET name = :name WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['suppliers']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if supplier name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Update Supplier
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        return array('msg'=>["Updated!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Supplier\'s Name Already Exists!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM suppliers WHERE id = :id';
            $sql1 = 'DELETE FROM suppliers WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['suppliers']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if supplier name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() == 1) {
                        // Delete Supplier
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        return array('msg'=>["Deleted!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Supplier Dose Not Exists!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM suppliers';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Suppliers
                $stmt = $conn->prepare($sql0);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["All Suppliers!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["No Supplier!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM suppliers WHERE id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Supplier Info
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["Supplier Info!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["Supplier Not Found!"], 'success'=>false);
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
    }
    