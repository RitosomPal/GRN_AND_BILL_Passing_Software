<?php
    require_once('Permissions.php');

    class GRN {
        // Static Methods
        # Create
        public static function Create($db, string $userId, array $GRN, array $GRN_LIST) {
            // Extract Post Data
            extract($GRN);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $book = htmlspecialchars(strip_tags($book));
            $supplier = htmlspecialchars(strip_tags($supplier));
            $site = htmlspecialchars(strip_tags($site));
            $vehicle = htmlspecialchars(strip_tags($vehicle));
            $date = htmlspecialchars(strip_tags($date));
            $grnNo = htmlspecialchars(strip_tags($grnNo));
            $cancelled = htmlspecialchars(strip_tags($cancelled));
            if (empty($supplier)) {
                $supplier = NULL;
            }

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'INSERT INTO grn SET book = :book, grnNo = :grnNo, date = :date, supplier = :supplier, site = :site, vehicle = :vehicle, cancelled = :cancelled, user = :user';
            $sql1 = 'SELECT id FROM grn WHERE grnNo = :grnNo';
            $sql2 = 'INSERT INTO grnlist SET grnId = :grnId, item = :item, qtyRec = :qtyRec, unit = :unit';

            // Main Function
            if ($P['success'] && $P['data']['grn']['create']) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    // $grnNo = $grnNo['data'];
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':book', $book);
                    $stmt->bindParam(':grnNo', $grnNo);
                    $stmt->bindParam(':date', $date);
                    $stmt->bindParam(':supplier', $supplier);
                    $stmt->bindParam(':site', $site);
                    $stmt->bindParam(':vehicle', $vehicle);
                    $stmt->bindParam(':cancelled', $cancelled);
                    $stmt->bindParam(':user', $userId);
                    $stmt->execute();
                    $stmt = $conn->prepare($sql1);
                    $stmt->bindParam(':grnNo', $grnNo);
                    $stmt->execute();
                    $grnId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
                    foreach ($GRN_LIST as $v) {
                        if (!empty($v['i'])) {
                            $stmt = $conn->prepare($sql2);
                            $stmt->bindParam(':grnId', $grnId);
                            $stmt->bindParam(':item', $v['i']);
                            $stmt->bindParam(':qtyRec', $v['q']);
                            $stmt->bindParam(':unit', $v['u']);
                            $stmt->execute();
                        }
                    }

                    return array('msg'=>["Created!"], 'success'=>true);
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Edit
        public static function Edit($db, string $userId, array $GRN, array $GRN_LIST) {
            // Extract Post Data
            extract($GRN);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $id = htmlspecialchars(strip_tags($id));
            $book = htmlspecialchars(strip_tags($book));
            $grnNo = htmlspecialchars(strip_tags($grnNo));
            $date = htmlspecialchars(strip_tags($date));
            $supplier = htmlspecialchars(strip_tags($supplier));
            $site = htmlspecialchars(strip_tags($site));
            $vehicle = htmlspecialchars(strip_tags($vehicle));
            $cancelled = htmlspecialchars(strip_tags($cancelled));
            $modified_at = date('Y-m-d');
            if (empty($supplier)) {
                $supplier = NULL;
            }

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT * FROM grn WHERE id = :id';
            $sql1 = 'UPDATE grn SET book = :book, grnNo = :grnNo, date = :date, supplier = :supplier, site = :site, vehicle = :vehicle, cancelled = :cancelled, modified_at = :modified_at WHERE id = :id';
            $sql2 = 'SELECT item,qtyRec,unit FROM grnlist WHERE grnId = :id';
            $sql3 = 'DELETE FROM grnlist WHERE grnId = :id';
            $sql4 = 'INSERT INTO grnlist SET grnId = :grnId, item = :item, qtyRec = :qtyRec, unit = :unit';

            // Main Function
            if ($P['success'] && $P['data']['grn']['modify']) {
                try {
                    $conn = $db->connect();

                    // Get Previous GRN Data
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    $prevData = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($userId == 1 || $userId == $prevData['user']) {

                        // Checking For GRN No
                        if ($grnNo == $prevData['grnNo']) {
                            $book = $prevData['book'];
                        }

                        // UPDATE GRN
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':book', $book);
                        $stmt->bindParam(':grnNo', $grnNo);
                        $stmt->bindParam(':date', $date);
                        $stmt->bindParam(':supplier', $supplier);
                        $stmt->bindParam(':site', $site);
                        $stmt->bindParam(':vehicle', $vehicle);
                        $stmt->bindParam(':cancelled', $cancelled);
                        $stmt->bindParam(':modified_at', $modified_at);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();

                        // Get Previous GRN List Data
                        $stmt = $conn->prepare($sql2);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        $prevList = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($prevList as $K => $L) {
                            $prevList[$K]['i'] = $L['item'];
                            $prevList[$K]['q'] = $L['qtyRec'];
                            $prevList[$K]['u'] = $L['unit'];

                            unset($prevList[$K]['item']);
                            unset($prevList[$K]['qtyRec']);
                            unset($prevList[$K]['item']);
                        }

                        // Check For Changes in List
                        if (!GRN::compareJson($prevList, $GRN_LIST)) {
                            $stmt = $conn->prepare($sql3);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();

                            foreach ($GRN_LIST as $v) {
                                if (!empty($v['i'])) {
                                    $stmt = $conn->prepare($sql4);
                                    $stmt->bindParam(':grnId', $id);
                                    $stmt->bindParam(':item', $v['i']);
                                    $stmt->bindParam(':qtyRec', $v['q']);
                                    $stmt->bindParam(':unit', $v['u']);
                                    $stmt->execute();
                                }
                            }
                        }

                        return array('msg'=>["Updated!"], 'success'=>true);
                    } else {
                        return array('msg'=>["You are not allowed to edit!"], 'success'=>false);
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
            $id = htmlspecialchars(strip_tags($grnId));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT user FROM grn WHERE id = :id';
            $sql1 = 'DELETE FROM grnlist WHERE grnId = :grnId';
            $sql2 = 'DELETE FROM grn WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['grn']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check for user
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    $user_id = $stmt->fetch(PDO::FETCH_ASSOC)['user'];
                    if ($userId == 1 || $user_id == $userId) {
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':grnId', $id);
                        $stmt->execute();
                        $stmt = $conn->prepare($sql2);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        return array('msg'=>["Deleted!"], 'success'=>true);
                    } else {
                        return array('msg'=>["You are not allowed to deleted!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch By Date
        public static function FetchByDate($db, string $userId, string $from, string $to, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $from = htmlspecialchars(strip_tags($from));
            $to = htmlspecialchars(strip_tags($to));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        grn.id, 
                        grn.grnNo, 
                        grn.date, 
                        grn.book AS bookId, 
                        books.name AS bookName, 
                        grn.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        grn.site AS siteName, 
                        grn.vehicle AS vehicleNo, 
                        grn.user AS userId, 
                        users.name AS userName, 
                        grn.cancelled
                    FROM grn 
                    JOIN books ON grn.book = books.id 
                    LEFT OUTER JOIN suppliers ON grn.supplier = suppliers.id 
                    JOIN users ON grn.user = users.id 
                    WHERE 
                        date BETWEEN  CAST(:from AS DATE) AND CAST(:to AS DATE) 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        list.item AS itemId, 
                        items.name AS item, 
                        list.qtyRec, 
                        list.unit AS unitId, 
                        units.name AS unit, 
                        list.billed
                    FROM grnlist AS list 
                    JOIN items ON list.item = items.id 
                    JOIN units ON list.unit = units.id 
                    WHERE
                        list.grnId = :grnId';

            // Main Function
            if ($P['success'] && $P['data']['grn']['view']) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':from', $from);
                    $stmt->bindParam(':to', $to);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($data as $k => $v) {
                            $grnId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':grnId', $grnId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $total = $stmt->rowCount();
                            $billed = 0;
                            foreach ($data[$k]['list'] as $L) {
                                $billed += $L['billed'];
                            }
                            if($billed == $total && $billed != 0) { $data[$k]['billed'] = 1; } 
                            else if ($billed > 0) { $data[$k]['billed'] = -1; } 
                            else { $data[$k]['billed'] = 0; }
                        }
    
                        return array('msg'=>[$cnt." Results found."],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["No Result Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch By Modified_At
        public static function FetchByMDate($db, string $userId, string $from, string $to, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $from = htmlspecialchars(strip_tags($from));
            $to = htmlspecialchars(strip_tags($to));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        grn.id, 
                        grn.grnNo, 
                        grn.date, 
                        grn.book AS bookId, 
                        books.name AS bookName, 
                        grn.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        grn.site AS siteName, 
                        grn.vehicle AS vehicleNo, 
                        grn.user AS userId, 
                        users.name AS userName, 
                        grn.cancelled
                    FROM grn 
                    JOIN books ON grn.book = books.id 
                    LEFT OUTER JOIN suppliers ON grn.supplier = suppliers.id 
                    JOIN users ON grn.user = users.id 
                    WHERE 
                        modified_at BETWEEN  CAST(:from AS DATE) AND CAST(:to AS DATE) 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        list.item AS itemId, 
                        items.name AS item, 
                        list.qtyRec, 
                        list.unit AS unitId, 
                        units.name AS unit, 
                        list.billed
                    FROM grnlist AS list 
                    JOIN items ON list.item = items.id 
                    JOIN units ON list.unit = units.id 
                    WHERE
                        list.grnId = :grnId';

            // Main Function
            if ($P['success'] && $P['data']['grn']['view']) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':from', $from);
                    $stmt->bindParam(':to', $to);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($data as $k => $v) {
                            $grnId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':grnId', $grnId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $total = $stmt->rowCount();
                            $billed = 0;
                            foreach ($data[$k]['list'] as $L) {
                                $billed += $L['billed'];
                            }
                            if($billed == $total && $billed != 0) { $data[$k]['billed'] = 1; } 
                            else if ($billed > 0) { $data[$k]['billed'] = -1; } 
                            else { $data[$k]['billed'] = 0; }
                        }
    
                        return array('msg'=>[$cnt." Results found."],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["No Result Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch By Supplier
        public static function FetchBySupplier($db, string $userId, string $supplier, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $supplier = htmlspecialchars(strip_tags($supplier));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        grn.id, 
                        grn.grnNo, 
                        grn.date, 
                        grn.book AS bookId, 
                        books.name AS bookName, 
                        grn.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        grn.site AS siteName, 
                        grn.vehicle AS vehicleNo, 
                        grn.user AS userId, 
                        users.name AS userName, 
                        grn.cancelled
                    FROM grn 
                    JOIN books ON grn.book = books.id 
                    LEFT OUTER JOIN suppliers ON grn.supplier = suppliers.id 
                    JOIN users ON grn.user = users.id 
                    WHERE 
                        grn.supplier = :supplier 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        list.item AS itemId, 
                        items.name AS item, 
                        list.qtyRec, 
                        list.unit AS unitId, 
                        units.name AS unit, 
                        list.billed
                    FROM grnlist AS list 
                    JOIN items ON list.item = items.id 
                    JOIN units ON list.unit = units.id 
                    WHERE
                        list.grnId = :grnId';

            // Main Function
            if ($P['success'] && $P['data']['grn']['view']) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':supplier', $supplier);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($data as $k => $v) {
                            $grnId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':grnId', $grnId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $total = $stmt->rowCount();
                            $billed = 0;
                            foreach ($data[$k]['list'] as $L) {
                                $billed += $L['billed'];
                            }
                            if($billed == $total && $billed != 0) { $data[$k]['billed'] = 1; } 
                            else if ($billed > 0) { $data[$k]['billed'] = -1; } 
                            else { $data[$k]['billed'] = 0; }
                        }
    
                        return array('msg'=>[$cnt." Results found."],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["No Result Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch By Site
        public static function FetchBySite($db, string $userId, string $site, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $site = htmlspecialchars(strip_tags($site));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        grn.id, 
                        grn.grnNo, 
                        grn.date, 
                        grn.book AS bookId, 
                        books.name AS bookName, 
                        grn.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        grn.site AS siteName, 
                        grn.vehicle AS vehicleNo, 
                        grn.user AS userId, 
                        users.name AS userName, 
                        grn.cancelled
                    FROM grn 
                    JOIN books ON grn.book = books.id 
                    LEFT OUTER JOIN suppliers ON grn.supplier = suppliers.id 
                    JOIN users ON grn.user = users.id 
                    WHERE 
                        grn.site = :site 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        list.item AS itemId, 
                        items.name AS item, 
                        list.qtyRec, 
                        list.unit AS unitId, 
                        units.name AS unit, 
                        list.billed
                    FROM grnlist AS list 
                    JOIN items ON list.item = items.id 
                    JOIN units ON list.unit = units.id 
                    WHERE
                        list.grnId = :grnId';

            // Main Function
            if ($P['success'] && $P['data']['grn']['view']) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':site', $site);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($data as $k => $v) {
                            $grnId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':grnId', $grnId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $total = $stmt->rowCount();
                            $billed = 0;
                            foreach ($data[$k]['list'] as $L) {
                                $billed += $L['billed'];
                            }
                            if($billed == $total && $billed != 0) { $data[$k]['billed'] = 1; } 
                            else if ($billed > 0) { $data[$k]['billed'] = -1; } 
                            else { $data[$k]['billed'] = 0; }
                        }
    
                        return array('msg'=>[$cnt." Results found."],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["No Result Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch By Book
        public static function FetchByBook($db, string $userId, string $book, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $book = htmlspecialchars(strip_tags($book));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        grn.id, 
                        grn.grnNo, 
                        grn.date, 
                        grn.book AS bookId, 
                        books.name AS bookName, 
                        grn.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        grn.site AS siteName, 
                        grn.vehicle AS vehicleNo, 
                        grn.user AS userId, 
                        users.name AS userName, 
                        grn.cancelled
                    FROM grn 
                    JOIN books ON grn.book = books.id 
                    LEFT OUTER JOIN suppliers ON grn.supplier = suppliers.id 
                    JOIN users ON grn.user = users.id 
                    WHERE 
                        grn.book = :book 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        list.item AS itemId, 
                        items.name AS item, 
                        list.qtyRec, 
                        list.unit AS unitId, 
                        units.name AS unit, 
                        list.billed
                    FROM grnlist AS list 
                    JOIN items ON list.item = items.id 
                    JOIN units ON list.unit = units.id 
                    WHERE
                        list.grnId = :grnId';

            // Main Function
            if ($P['success'] && $P['data']['grn']['view']) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':book', $book);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($data as $k => $v) {
                            $grnId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':grnId', $grnId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $total = $stmt->rowCount();
                            $billed = 0;
                            foreach ($data[$k]['list'] as $L) {
                                $billed += $L['billed'];
                            }
                            if($billed == $total && $billed != 0) { $data[$k]['billed'] = 1; } 
                            else if ($billed > 0) { $data[$k]['billed'] = -1; } 
                            else { $data[$k]['billed'] = 0; }
                        }
    
                        return array('msg'=>[$cnt." Results found."],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["No Result Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch By GRN No
        public static function FetchByGrnNo($db, string $userId, string $grnNo, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $grnNo = htmlspecialchars(strip_tags($grnNo));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        grn.id, 
                        grn.grnNo, 
                        grn.date, 
                        grn.book AS bookId, 
                        books.name AS bookName, 
                        grn.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        grn.site AS siteName, 
                        grn.vehicle AS vehicleNo, 
                        grn.user AS userId, 
                        users.name AS userName, 
                        grn.cancelled
                    FROM grn 
                    JOIN books ON grn.book = books.id 
                    LEFT OUTER JOIN suppliers ON grn.supplier = suppliers.id 
                    JOIN users ON grn.user = users.id 
                    WHERE 
                        grn.grnNo = :grnNo 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        list.item AS itemId, 
                        items.name AS item, 
                        list.qtyRec, 
                        list.unit AS unitId, 
                        units.name AS unit, 
                        list.billed
                    FROM grnlist AS list 
                    JOIN items ON list.item = items.id 
                    JOIN units ON list.unit = units.id 
                    WHERE
                        list.grnId = :grnId';

            // Main Function
            if ($P['success'] && $P['data']['grn']['view']) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':grnNo', $grnNo);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($data as $k => $v) {
                            $grnId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':grnId', $grnId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $total = $stmt->rowCount();
                            $billed = 0;
                            foreach ($data[$k]['list'] as $L) {
                                $billed += $L['billed'];
                            }
                            if($billed == $total && $billed != 0) { $data[$k]['billed'] = 1; } 
                            else if ($billed > 0) { $data[$k]['billed'] = -1; } 
                            else { $data[$k]['billed'] = 0; }
                        }
    
                        return array('msg'=>["Result found!"],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["No Result Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch GRN List By GRN ID
        public static function GrnListByGrnId($db, string $userId, string $grnId, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $grnId = htmlspecialchars(strip_tags($grnId));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT
                        list.id, 
                        list.item AS itemId, 
                        items.name AS item, 
                        list.qtyRec, 
                        list.unit AS unitId, 
                        units.name AS unit, 
                        list.billed
                    FROM grnlist AS list 
                    JOIN items ON list.item = items.id 
                    JOIN units ON list.unit = units.id 
                    WHERE
                        list.grnId = :grnId';

            // Main Function
            if ($P['success'] && $P['data']['grn']['view']) {
                try {
                    $conn = $db->connect();
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':grnId', $grnId);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                        return array('msg'=>["GRN List Found."],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["GRN List Not Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch All Un-Billed GRN List With Book Id and Supplier Id
        public static function GrnListByBookAndSupplierId($db, string $userId, string $book, string $supplier, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $book = htmlspecialchars(strip_tags($book));
            $supplier = htmlspecialchars(strip_tags($supplier));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT
                        grn.date,
                        books.id AS bookId,
                        books.name AS bookName,
                        suppliers.id AS supplierId,
                        suppliers.name As supplierName,
                        grn.id AS grnId,
                        grn.grnNo,
                        grn.cancelled,
                        items.id AS itemId,
                        items.name AS itemName,
                        units.id AS unitId,
                        units.name AS unitName,
                        grnlist.qtyRec,
                        grnlist.billed,
                        grnlist.id AS grnListId
                    FROM grn
                    JOIN books ON books.id = grn.book
                    JOIN suppliers ON suppliers.id = grn.supplier
                    JOIN grnlist ON grnlist.grnId = grn.id
                    JOIN items ON items.id = grnlist.item
                    JOIN units ON units.id = grnlist.unit
                    WHERE
                        grn.book = :book
                    AND
                        grn.supplier = :supplier
                    ORDER BY grnListId '.$ORDER;

            // Main Function
            if ($P['success'] && ($P['data']['grn']['view'] || $P['data']['bpm']['view'])) {
                try {
                    $conn = $db->connect();
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':book', $book);
                    $stmt->bindParam(':supplier', $supplier);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                        return array('msg'=>["GRN List Found."],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["No Data Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Fetch All Un-Billed GRN List With Supplier Id
        public static function GrnListBySupplierId($db, string $userId, string $supplier, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $supplier = htmlspecialchars(strip_tags($supplier));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT
                        grn.date,
                        books.id AS bookId,
                        books.name AS bookName,
                        suppliers.id AS supplierId,
                        suppliers.name As supplierName,
                        grn.id AS grnId,
                        grn.grnNo,
                        grn.cancelled,
                        items.id AS itemId,
                        items.name AS itemName,
                        units.id AS unitId,
                        units.name AS unitName,
                        grnlist.qtyRec,
                        grnlist.billed,
                        grnlist.id AS grnListId
                    FROM grn
                    JOIN books ON books.id = grn.book
                    JOIN suppliers ON suppliers.id = grn.supplier
                    JOIN grnlist ON grnlist.grnId = grn.id
                    JOIN items ON items.id = grnlist.item
                    JOIN units ON units.id = grnlist.unit
                    WHERE
                        grn.supplier = :supplier
                    ORDER BY grnListId '.$ORDER;

            // Main Function
            if ($P['success'] && ($P['data']['grn']['view'] || $P['data']['bpm']['view'])) {
                try {
                    $conn = $db->connect();
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':supplier', $supplier);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                        return array('msg'=>["GRN List Found."],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["No Data Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Get Next GRN No
        public static function getNextGrnNo($db, string $userId, string $book) {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $book = htmlspecialchars(strip_tags($book));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT grnIdPattern, startingPage, lastPage FROM books WHERE id = :id';
            $sql1 = 'SELECT grnNo FROM grn WHERE id = (SELECT MAX(id) FROM grn WHERE book = :book)';

            // Main Function
            if ($P['success'] && $P['data']['grn']['create']) {
                try {
                    $conn = $db->connect();

                    // Fetch GRN No Pattern
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $book);
                    $stmt->execute();
                    $bookD = $stmt->fetch(PDO::FETCH_ASSOC);
                    $pattern = $bookD['grnIdPattern'];

                    // Fetch Last GRN No.
                    $stmt = $conn->prepare($sql1);
                    $stmt->bindParam(':book', $book);
                    $stmt->execute();
                    $grnNo = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($grnNo) {
                        $id = intval(substr($grnNo['grnNo'],strlen('GRN-'.$pattern)))+1;
                        if ($id > $bookD['lastPage']) {
                            return  array('msg'=>["No more pages in this book!"], 'success'=>false);
                        }
                        $pl = (strlen($bookD['lastPage']) > 4) ? strlen($bookD['lastPage']) : 4;
                        $grnNo = 'GRN-'.$pattern.str_pad($id, $Pl, "0", STR_PAD_LEFT);
                    } else { 
                        $pl = (strlen($bookD['lastPage']) > 4) ? strlen($bookD['lastPage']) : 4;
                        $grnNo = 'GRN-'.$pattern.str_pad($bookD['startingPage'], $pl, "0", STR_PAD_LEFT);
                    }
                    return array('msg'=>["Next GRN No.!"], 'data'=> $grnNo, 'success'=>true);
                }
                catch(PDOException $e) {
                    return  array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }
        # Get ALL GRN No
        public static function getALLGrnNo($db, string $userId, string $book) {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $book = htmlspecialchars(strip_tags($book));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT grnIdPattern, startingPage, lastPage FROM books WHERE id = :id';
            $sql1 = 'SELECT grnNo FROM grn WHERE book = :book';

            // Main Function
            if ($P['success'] && $P['data']['grn']['create']) {
                try {
                    $conn = $db->connect();

                    // Fetch GRN No Pattern
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $book);
                    $stmt->execute();
                    $bookD = $stmt->fetch(PDO::FETCH_ASSOC);
                    $pattern = $bookD['grnIdPattern'];

                    // Fetch Last GRN No.
                    $stmt = $conn->prepare($sql1);
                    $stmt->bindParam(':book', $book);
                    $stmt->execute();
                    $grnNo = array();
                    $data = array();
                    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $key => $value) {
                        array_push($grnNo, $value['grnNo']);
                    }
                    for($i = $bookD['startingPage']; $i <= $bookD['lastPage'];$i++) {
                        $pl = (strlen($bookD['lastPage']) > 4) ? strlen($bookD['lastPage']) : 4;
                        array_push($data,'GRN-'.$pattern.str_pad($i, $pl, "0", STR_PAD_LEFT));
                    }
                    $data = array_diff($data,$grnNo);
                    $grnNo = array();
                    foreach ($data as $key => $value) { array_push($grnNo, $value); }

                    return array('msg'=>["Next GRN No.!"], 'data'=> $grnNo, 'success'=>true);
                }
                catch(PDOException $e) {
                    return  array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
                }
            } else {
                return array('msg'=>["Premission Denied!"], 'success'=>false);
            }
        }

        // [Helper Function]
        public static function compareJson(array $l1, array $l2) {
            if(count($l1) != count($l2)) return false;
            foreach ($l1 as $v) {
                if (!in_array($v,$l2)) return false;
            }
            return true;
        }
    }