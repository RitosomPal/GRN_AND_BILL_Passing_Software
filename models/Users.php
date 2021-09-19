<?php
    require_once('Permissions.php');

    class Users {
        //Static Methods
        # Create
        public static function Create($db, string $userId, array $post, array $file) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $groupId = htmlspecialchars(strip_tags($group_id));
            $name = htmlspecialchars(strip_tags($name));
            $username = htmlspecialchars(strip_tags($username));
            $active = htmlspecialchars(strip_tags($active));
            $password = md5(htmlspecialchars(strip_tags($password)));
            if ($file['size'] > 0) {
                $image = uniqid().'.'.array_reverse(explode('.',$file['name']))[0];
            } else {
                $image = NULL;
            }
            
            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM users WHERE username = :username';
            $sql1 = 'INSERT INTO users SET image = :image, name = :name, username = :username, password = :password, groupId = :groupId, active = :active';

            // Main Function
            if ($P['success'] && $P['data']['users']['create']) {
                try {
                    $conn = $db->connect();

                    // Check if username exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Insert User
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':image', $image);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':password', $password);
                        $stmt->bindParam(':groupId', $groupId);
                        $stmt->bindParam(':active', $active);
                        $stmt->execute();

                        // Upload File
                        if ($file['size'] > 0) {
                            $dir = './assets/images/';
                            move_uploaded_file($file["tmp_name"], $dir.$image);
                        }

                        return array('msg'=>["User Added!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Username Name Already Exists!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Update Profle Picture
        public static function Upload($db, string $userId, array $file) {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));

            // All SQL Commands
            $sql0 = 'SELECT image FROM users WHERE id = :id';
            $sql1 = 'UPDATE users SET image = :image WHERE id = :id';

            // Main Function
            if (explode('/',$file['type'])[0] == 'image') {
                try {
                    $conn = $db->connect();

                    // Fetch previous image
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $userId);
                    $stmt->execute();
                    $prevName = $stmt->fetch(PDO::FETCH_ASSOC)['image'];

                    // Upload File
                    $dir = './assets/images/';
                    $name = uniqid().'.'.array_reverse(explode('.',$file['name']))[0];
                    if (move_uploaded_file($file["tmp_name"], $dir.$name)) {
                        if (!empty($prevName)) { unlink($dir.$prevName); }
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':id', $userId);
                        $stmt->bindParam(':image', $name);
                        $stmt->execute();
                        return array('msg'=>["Uploaded!"], 'success'=>true);
                    } else {
                        return array('msg'=>["System Error!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Not a valid image file!"], 'success'=>false);
            }
        }
        # Update Password
        public static function Update($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId= htmlspecialchars(strip_tags($userId));
            $password = md5(htmlspecialchars(strip_tags($password)));

            // All SQL Commands
            $sql0 = 'UPDATE users SET password = :password WHERE id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Check if userId exists
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();

                return array('msg'=>["Successfully Updated!"], 'success'=>true);
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
        # Update Profle Details
        public static function Edit($db, string $userId, array $post, array $file) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId= htmlspecialchars(strip_tags($userId));
            $id = htmlspecialchars(strip_tags($id));
            $groupId = htmlspecialchars(strip_tags($group_id));
            $name = htmlspecialchars(strip_tags($name));
            $username = htmlspecialchars(strip_tags($username));
            $active = htmlspecialchars(strip_tags($active));
            if(isset($password)) { $password = md5(htmlspecialchars(strip_tags($password))); }

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM users WHERE username = :username AND id <> :id';
            $sql1 = 'UPDATE users SET name = :name, username = :username, groupId = :groupId, active = :active WHERE id = :id';
            $sql2 = 'UPDATE users SET password = :password WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['users']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if userId exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();

                    if ($stmt->rowCount() < 1) {
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':active', $active);
                        $stmt->bindParam(':groupId', $groupId);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        if(isset($post['password'])) {
                            $stmt = $conn->prepare($sql2);
                            $stmt->bindParam(':password', $password);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();
                        }

                        if ($file['size'] > 0) {
                            Users::Upload($db, $id, $file);
                        }

                        return array('msg'=>["Updated!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Username Already Exists!"], 'success'=>false);   
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
            $userId= htmlspecialchars(strip_tags($userId));
            $id = htmlspecialchars(strip_tags($id));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT image FROM users WHERE id = :id';
            $sql1 = 'DELETE FROM users WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['users']['modify']) {
                try {
                    $conn = $db->connect();

                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    $image = $stmt->fetch(PDO::FETCH_ASSOC)['image'];
                    $dir = './assets/images/';
                    if (!empty($image)) { unlink($dir.$image); }
                    $stmt = $conn->prepare($sql1);
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
        # Login
        public static function Login($db, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $username = htmlspecialchars(strip_tags($username));
            $password = md5(htmlspecialchars(strip_tags($password)));
            
            // All SQL Commands
            $sql0 = 'SELECT id, name, image  FROM users WHERE username = :username AND password = :password AND active = 1';
            // $sql1 = 'UPDATE users SET lastAccess = :lastAccess WHERE id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Check for valid username & password
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);

                    return array('msg'=>["Successfully Logged In!"], 'data'=>$data, 'success'=>true);
                } else { 
                    return array('msg'=>["Invalid Username Or Password!"], 'success'=>false);   
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
        # Logout
        public static function Logout($db, string $userId) {
            // Clean Data
            $id = htmlspecialchars(strip_tags($userId));
            
            // All SQL Commands
            $sql0 = 'SELECT * FROM users WHERE id = :id';
            $sql1 = 'UPDATE users SET lastAccess = :lastAccess WHERE id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Check for valid username & password
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    $lastAccess = date("d/m/Y, h:i a");
                    $stmt = $conn->prepare($sql1);
                    $stmt->bindParam(':lastAccess', $lastAccess);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    return array('msg'=>["Updated!"], 'success'=>true);
                } else { 
                    return array('msg'=>["Invalid User!"], 'success'=>false);   
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
        # Fetch All
        public static function FetchAll($db) {
            // All SQL Commands
            $sql0 = 'SELECT users.*, groups.name AS groupName FROM users JOIN groups ON users.groupId = groups.id WHERE users.id <> 1';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Users
                $stmt = $conn->prepare($sql0);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($data as $key => $value) {
                        unset($data[$key]['password']);
                    }

                    return array('msg'=>["All Users!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["No Users"], 'success'=>false);
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
        # Fetch Info
        public static function FetchInfo($db, string $userId) {
            // Clean Data
            $id = htmlspecialchars(strip_tags($userId));
            
            // All SQL Commands
            $sql0 = 'SELECT users.*, groups.name AS groupName FROM users JOIN groups ON users.groupId = groups.id WHERE users.id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Users
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                if ($stmt->rowCount() == 1) {
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);
                    unset($data['password']);

                    return array('msg'=>["Info Found!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["Invalid User!"], 'success'=>false);
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
    }
    