<?php
    class Books {
        // Static Methods
        # Create
        public static function _Create($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $name = htmlspecialchars(strip_tags($book_name));
            $grnIdPattern = htmlspecialchars(strip_tags($grn_pattern));
            $startingPage = htmlspecialchars(strip_tags($starting_page));
            $lastPage = htmlspecialchars(strip_tags($last_page));
            $active = htmlspecialchars(strip_tags($active));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM books WHERE name = :name';
            $sql1 = 'SELECT * FROM books WHERE grnIdPattern = :grnIdPattern';
            $sql2 = 'UPDATE books SET active = 0 WHERE active = 1';
            $sql3 = 'INSERT INTO books SET name = :name, grnIdPattern = :grnIdPattern, startingPage = :startingPage, lastPage = :lastPage, active = :active';

            // Main Function
            if ($P['success'] && $P['data']['books']['create']) {
                try {
                    $conn = $db->connect();

                    // Check if book name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Check if grn id pattern exists
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':grnIdPattern', $grnIdPattern);
                        $stmt->execute();
                        if ($stmt->rowCount() < 1) {
                            if ($active) {
                                $stmt = $conn->prepare($sql2);
                                $stmt->execute();
                            }

                            // Insert Book
                            $stmt = $conn->prepare($sql3);
                            $stmt->bindParam(':name', $name);
                            $stmt->bindParam(':grnIdPattern', $grnIdPattern);
                            $stmt->bindParam(':startingPage', $startingPage);
                            $stmt->bindParam(':lastPage', $lastPage);
                            $stmt->bindParam(':active', $active);
                            $stmt->execute();

                            return array('msg'=>["Book Added!"], 'success'=>true);
                        } else { 
                            return array('msg'=>["GRN ID Pattern Already Exists!"], 'success'=>false);
                        }
                    } else { 
                        return array('msg'=>["Book Already Exists!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        
        # Create
        public static function Create($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $name = htmlspecialchars(strip_tags($book_name));
            $grnIdPattern = htmlspecialchars(strip_tags($grn_pattern));
            $startingPage = htmlspecialchars(strip_tags($starting_page));
            $lastPage = htmlspecialchars(strip_tags($last_page));
            $active = htmlspecialchars(strip_tags($active));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM books WHERE name = :name AND grnIdPattern = :grnIdPattern';
            $sql1 = 'UPDATE books SET active = 0 WHERE active = 1';
            $sql2 = 'INSERT INTO books SET name = :name, grnIdPattern = :grnIdPattern, startingPage = :startingPage, lastPage = :lastPage, active = :active';

            // Main Function
            if ($P['success'] && $P['data']['books']['create']) {
                try {
                    $conn = $db->connect();

                    // Check if book name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':grnIdPattern', $grnIdPattern);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        if ($active) {
                            $stmt = $conn->prepare($sql1);
                            $stmt->execute();
                        }

                        // Insert Book
                        $stmt = $conn->prepare($sql2);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':grnIdPattern', $grnIdPattern);
                        $stmt->bindParam(':startingPage', $startingPage);
                        $stmt->bindParam(':lastPage', $lastPage);
                        $stmt->bindParam(':active', $active);
                        $stmt->execute();

                        return array('msg'=>["Book Added!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Book Already Exists!"], 'success'=>false);
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
        public static function _Edit($db, string $userId, array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $id = htmlspecialchars(strip_tags($id));
            $name = htmlspecialchars(strip_tags($book_name));
            $grnIdPattern = htmlspecialchars(strip_tags($grn_pattern));
            $startingPage = htmlspecialchars(strip_tags($starting_page));
            $lastPage = htmlspecialchars(strip_tags($last_page));
            $active = htmlspecialchars(strip_tags($active));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM books WHERE name = :name AND id <> :id';
            $sql1 = 'SELECT * FROM books WHERE grnIdPattern = :grnIdPattern AND id <> :id';
            $sql2 = 'SELECT * FROM books WHERE id = :id';
            $sql3 = 'SELECT grnNo FROM grn WHERE id = (SELECT MAX(id) FROM grn WHERE book = :book)';
            $sql4 = 'UPDATE books SET name = :name, grnIdPattern = :grnIdPattern, startingPage = :startingPage, lastPage = :lastPage, active = :active WHERE id = :id';
            $sql5 = 'SELECT id,grnNo FROM grn WHERE book = :id';
            $sql6 = 'UPDATE grn SET grnNo = :grnNo WHERE id = :id';
            $sql7 = 'UPDATE books SET active = 0 WHERE active = 1';

            // Main Function
            if ($P['success'] && $P['data']['books']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if book name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Check if grn id pattern exists
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':grnIdPattern', $grnIdPattern);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        if ($stmt->rowCount() < 1) {
                            // Get Previous Data
                            $stmt = $conn->prepare($sql2);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();
                            $previousData = $stmt->fetch(PDO::FETCH_ASSOC);

                            // Fetch Last GRN No.
                            $stmt = $conn->prepare($sql3);
                            $stmt->bindParam(':book', $id);
                            $stmt->execute();
                            $grn = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($grn) {
                                $lastUsedPage = intval(substr($grn['grnNo'],strlen('GRN-'.$previousData['grnIdPattern'])));
                                if ($lastPage < $lastUsedPage) {
                                    return array('msg'=>["Last page must be grater than or equal to ".$lastUsedPage."!"], 'success'=>false);
                                }
                            }

                            if ($active) {
                                $stmt = $conn->prepare($sql7);
                                $stmt->execute();
                            }

                            // Update Book
                            $stmt = $conn->prepare($sql4);
                            $stmt->bindParam(':name', $name);
                            $stmt->bindParam(':grnIdPattern', $grnIdPattern);
                            $stmt->bindParam(':startingPage', $startingPage);
                            $stmt->bindParam(':lastPage', $lastPage);
                            $stmt->bindParam(':active', $active);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();

                            if ($previousData['grnIdPattern'] != $grnIdPattern) {
                                $prevPattern = 'GRN-'.$previousData['grnIdPattern'];
                                $newPattern = 'GRN-'.$grnIdPattern;

                                // Update GrnNo
                                $stmt = $conn->prepare($sql5);
                                $stmt->bindParam(':id', $id);
                                $stmt->execute();
                                if ($stmt->rowCount() > 1) {
                                    $grns = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($grns as $v) {
                                        $grnId = $v['id'];
                                        $grnNo = $v['grnNo'];
                                        $grnNo = str_replace($prevPattern,$newPattern,$grnNo);
                                        $stmt = $conn->prepare($sql6);
                                        $stmt->bindParam(':id', $id);
                                        $stmt->bindParam(':grnNo', $grnNo);
                                        $stmt->execute();
                                    }
                                }
                            }

                            return array('msg'=>["Updated!"], 'success'=>true);
                        } else { 
                            return array('msg'=>["GRN ID Pattern Already Exists!"], 'success'=>false);
                        }
                    } else { 
                        return array('msg'=>["Book Already Exists!"], 'success'=>false);
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
            $name = htmlspecialchars(strip_tags($book_name));
            $grnIdPattern = htmlspecialchars(strip_tags($grn_pattern));
            $startingPage = htmlspecialchars(strip_tags($starting_page));
            $lastPage = htmlspecialchars(strip_tags($last_page));
            $active = htmlspecialchars(strip_tags($active));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM books WHERE name = :name AND grnIdPattern = :grnIdPattern AND id <> :id';
            $sql2 = 'SELECT * FROM books WHERE id = :id';
            $sql3 = 'SELECT grnNo FROM grn WHERE id = (SELECT MAX(id) FROM grn WHERE book = :book)';
            $sql4 = 'UPDATE books SET name = :name, grnIdPattern = :grnIdPattern, startingPage = :startingPage, lastPage = :lastPage, active = :active WHERE id = :id';
            $sql5 = 'SELECT id,grnNo FROM grn WHERE book = :id';
            $sql6 = 'UPDATE grn SET grnNo = :grnNo WHERE id = :id';
            $sql7 = 'UPDATE books SET active = 0 WHERE active = 1';

            // Main Function
            if ($P['success'] && $P['data']['books']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if book name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':grnIdPattern', $grnIdPattern);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Get Previous Data
                        $stmt = $conn->prepare($sql2);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        $previousData = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Fetch Last GRN No.
                        $stmt = $conn->prepare($sql3);
                        $stmt->bindParam(':book', $id);
                        $stmt->execute();
                        $grn = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($grn) {
                            $lastUsedPage = intval(substr($grn['grnNo'],strlen('GRN-'.$previousData['grnIdPattern'])));
                            if ($lastPage < $lastUsedPage) {
                                return array('msg'=>["Last page must be grater than or equal to ".$lastUsedPage."!"], 'success'=>false);
                            }
                        }

                        // if ($active) {
                        //     $stmt = $conn->prepare($sql7);
                        //     $stmt->execute();
                        // }

                        // Update Book
                        $stmt = $conn->prepare($sql4);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':grnIdPattern', $grnIdPattern);
                        $stmt->bindParam(':startingPage', $startingPage);
                        $stmt->bindParam(':lastPage', $lastPage);
                        $stmt->bindParam(':active', $active);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        if ($previousData['grnIdPattern'] != $grnIdPattern) {
                            $prevPattern = 'GRN-'.$previousData['grnIdPattern'];
                            $newPattern = 'GRN-'.$grnIdPattern;

                            // Update GrnNo
                            $stmt = $conn->prepare($sql5);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();
                            if ($stmt->rowCount() > 1) {
                                $grns = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($grns as $v) {
                                    $grnId = $v['id'];
                                    $grnNo = $v['grnNo'];
                                    $grnNo = str_replace($prevPattern,$newPattern,$grnNo);
                                    $stmt = $conn->prepare($sql6);
                                    $stmt->bindParam(':id', $id);
                                    $stmt->bindParam(':grnNo', $grnNo);
                                    $stmt->execute();
                                }
                            }
                        }

                        return array('msg'=>["Updated!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Book Already Exists!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM books WHERE id = :id';
            $sql1 = 'DELETE FROM books WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['books']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check if book name exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    if ($stmt->rowCount() == 1) {
                        // Delete Book
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        return array('msg'=>["Deleted!"], 'success'=>true);
                    } else { 
                        return array('msg'=>["Book Dose Not Exists!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["Book is being used!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch All
        public static function FetchAll($db) {
            // All SQL Commands
            $sql0 = 'SELECT * FROM books';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Books
                $stmt = $conn->prepare($sql0);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["All Books!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["No Book!"], 'success'=>false);
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
            $sql0 = 'SELECT * FROM books WHERE id = :id';

            // Main Function
            try {
                $conn = $db->connect();

                // Fetch Book Info
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    return array('msg'=>["Book Info!"],  'data'=>$data,  'success'=>true);
                } else {
                    return array('msg'=>["Book Not Found!"], 'success'=>false);
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
    }