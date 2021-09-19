<?php
    class Sites {
        // Static Methods
        # Create
        public static function Create($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $name = htmlspecialchars(strip_tags($site_name));
            $active = htmlspecialchars(strip_tags($active));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM sites WHERE name = :name';
            $sql1 = 'INSERT INTO sites SET name = :name, active = :active';

            // Main Function
            if ($P['success'] && $P['data']['suppliers']['create']) {
                try {
                    $conn = $db->connect();

                    // Check if site name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Insert Site
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':active', $active);
                        $stmt->execute();

                        return array('msg'=>["Site Added!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Site Name Already Exists!"], 'success'=>false);
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
            $name = htmlspecialchars(strip_tags($site_name));
            $active = htmlspecialchars(strip_tags($active));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM sites WHERE name = :name AND id <> :id';
            $sql1 = 'UPDATE sites SET name = :name, active = :active WHERE id = :id';

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
                        $stmt->bindParam(':active', $active);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        return array('msg'=>["Updated!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Site Name Already Exists!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM sites WHERE id = :id';
            $sql1 = 'SELECT * FROM grn WHERE site = :site';
            $sql2 = 'DELETE FROM sites WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['suppliers']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if supplier name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() == 1) {
                        $site = $stmt->fetch(PDO::FETCH_ASSOC)['name'];
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':site', $site);
                        $stmt->execute();
                        if ($stmt->rowCount() < 1) {
                            // Delete Site
                            $stmt = $conn->prepare($sql2);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();

                            return array('msg'=>["Deleted!"], 'success'=>true);
                        } else {
                            return array('msg'=>["Site has been used!"], 'success'=>false);
                        }
                    } else { 
                        return array('msg'=>["Site Dose Not Exists!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM sites';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Sites
                $stmt = $conn->prepare($sql0);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["All Sites!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["No Site!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM sites WHERE id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Site Info
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["Site Info!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["Site Not Found!"], 'success'=>false);
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
    }
    