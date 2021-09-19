<?php
    require_once('Permissions.php');

    class BPM {
        // Static Methods
        # Create
        public static function Create($db, string $userId, array $BPM, array $BPM_LIST) {
            // Extract Post Data
            extract($BPM);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $supplier = htmlspecialchars(strip_tags($supplier));
            $date = htmlspecialchars(strip_tags($date));
            $regNo = htmlspecialchars(strip_tags($regNo));
            $billNo = htmlspecialchars(strip_tags($billNo));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM bpm WHERE regNo = :regNo';
            $sql1 = 'SELECT * FROM bpm WHERE billNo = :billNo AND supplier = :supplier';
            $sql2 = 'INSERT INTO bpm SET regNo = :regNo, billNo = :billNo, supplier = :supplier, date = :date, user = :user';
            $sql3 = 'INSERT INTO bpmlist SET billId = :billId, grnlistId = :grnlistId, billedQty = :billedQty';
            $sql4 = 'UPDATE grnlist SET billed = 1 WHERE id = :id';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['create']) {
                try {
                    $conn = $db->connect();

                    // Check if Reg No Exists
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':regNo', $regNo);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0) {
                        return array('msg'=>["Register No. already used!"], 'success'=>false);
                    }

                    // Check if Bill no Exists
                    $stmt = $conn->prepare($sql1);
                    $stmt->bindParam(':billNo', $billNo);
                    $stmt->bindParam(':supplier', $supplier);
                    $stmt->execute();
                    if ($stmt->rowCount() < 1) {
                        // Insert Bill Details
                        $stmt = $conn->prepare($sql2);
                        $stmt->bindParam(':regNo', $regNo);
                        $stmt->bindParam(':billNo', $billNo);
                        $stmt->bindParam(':supplier', $supplier);
                        $stmt->bindParam(':date', $date);
                        $stmt->bindParam(':user', $userId);
                        $stmt->execute();

                        // Get Last Bill ID
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':billNo', $billNo);
                        $stmt->bindParam(':supplier', $supplier);
                        $stmt->execute();
                        $billId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

                        // Insert Bill List
                        foreach ($BPM_LIST as $v) {
                            $stmt = $conn->prepare($sql3);
                            $stmt->bindParam(':billId', $billId);
                            $stmt->bindParam(':grnlistId', $v['grnlistId']);
                            $stmt->bindParam(':billedQty', $v['billedQty']);
                            $stmt->execute();

                            $stmt = $conn->prepare($sql4);
                            $stmt->bindParam(':id', $v['grnlistId']);
                            $stmt->execute();
                        }
    
                        return array('msg'=>["Created!"], 'success'=>true);
                    } else {
                        return array('msg'=>["Bill No. Already Exists!"], 'success'=>false);
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
        public static function Edit($db, string $userId, array $BPM, array $BPM_LIST) {
            // Extract Post Data
            extract($BPM);

            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $id = htmlspecialchars(strip_tags($id));
            $billNo = htmlspecialchars(strip_tags($billNo));
            $regNo = htmlspecialchars(strip_tags($regNo));
            $date = htmlspecialchars(strip_tags($date));
            $modified_at = date('Y-m-d');

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // All SQL Commands
            $sql0 = 'SELECT * FROM bpm WHERE id = :id';
            $sql01 = 'SELECT * FROM grn WHERE regNo = :regNo';
            $sql1 = 'SELECT * FROM bpm WHERE billNo = :billNo AND supplier = :supplier';
            $sql02 = 'UPDATE bpm SET regNo = :regNo, date = :date, modified_at = :modified_at WHERE id = :id';
            $sql2 = 'UPDATE bpm SET billNo = :billNo, date = :date, modified_at = :modified_at WHERE id = :id';
            $sql3 = 'SELECT grnlistId,billedQty FROM bpmlist WHERE billId = :id';
            $sql4 = 'UPDATE grnlist SET billed = :s WHERE id = :id';
            $sql5 = 'DELETE FROM bpmlist WHERE billId = :id';
            $sql6 = 'INSERT INTO bpmlist SET billId = :billId, grnlistId = :grnlistId, billedQty = :billedQty';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['modify']) {
                try {
                    $conn = $db->connect();

                    // Get Previous GRN Data
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    $prevData = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($userId == 1 || $userId == $prevData['user']) {
                        // Checking For Reg No
                        if ($regNo != $prevData['regNo']) {
                            // Check if Reg no Exists
                            $stmt = $conn->prepare($sql01);
                            $stmt->bindParam(':regNo', $regNo);
                            $stmt->execute();
                            if ($stmt->rowCount() < 1) {
                                $stmt = $conn->prepare($sql02);
                                $stmt->bindParam(':regNo', $regNo);
                                $stmt->bindParam(':date', $date);
                                $stmt->bindParam(':modified_at', $modified_at);
                                $stmt->bindParam(':id', $id);
                                $stmt->execute();
                            } else {
                                return array('msg'=>["Register No. Already Exists!"], 'success'=>false);
                            }
                        }

                        // Checking For Bill No
                        if ($billNo != $prevData['billNo']) {
                            // Check if Bill no Exists
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':billNo', $billNo);
                            $stmt->bindParam(':supplier', $prevData['supplier']);
                            $stmt->execute();
                            if ($stmt->rowCount() < 1) {
                                $stmt = $conn->prepare($sql2);
                                $stmt->bindParam(':billNo', $billNo);
                                $stmt->bindParam(':date', $date);
                                $stmt->bindParam(':modified_at', $modified_at);
                                $stmt->bindParam(':id', $id);
                                $stmt->execute();
                            } else {
                                return array('msg'=>["Bill No. Already Exists!"], 'success'=>false);
                            }
                        }

                        if ($date !=  $prevData['date']) {
                            $stmt = $conn->prepare($sql02);
                            $stmt->bindParam(':regNo', $prevData['regNo']);
                            $stmt->bindParam(':date', $date);
                            $stmt->bindParam(':modified_at', $modified_at);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();
                        }

                        // Get Previous BPM List Data
                        $stmt = $conn->prepare($sql3);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        $prevList = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Check For Changes in List
                        if (!BPM::compareJson($prevList, $BPM_LIST)) {
                            // Un Bill Previous GRNS
                            foreach ($prevList as $v) {
                                $s=0;
                                $stmt = $conn->prepare($sql4);
                                $stmt->bindParam(':id', $v['grnlistId']);
                                $stmt->bindParam(':s', $s);
                                $stmt->execute();
                            }

                            // Delete Previous BPM List
                            $stmt = $conn->prepare($sql5);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();

                            // New BRM List
                            foreach ($BPM_LIST as $v) {
                                $stmt = $conn->prepare($sql6);
                                $stmt->bindParam(':billId', $id);
                                $stmt->bindParam(':grnlistId', $v['grnlistId']);
                                $stmt->bindParam(':billedQty', $v['billedQty']);
                                $stmt->execute();

                                // Set Bill Flag
                                $s=1;
                                $stmt = $conn->prepare($sql4);
                                $stmt->bindParam(':id', $v['grnlistId']);
                                $stmt->bindParam(':s', $s);
                                $stmt->execute();
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
            $billId = htmlspecialchars(strip_tags($billId));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT user FROM bpm WHERE id = :billId';
            $sql1 = 'SELECT grnlistId FROM bpmlist WHERE billId = :billId';
            $sql2 = 'UPDATE grnlist SET billed = 0 WHERE id = :grnlistId';
            $sql3 = 'DELETE FROM bpmlist WHERE billId = :billId';
            $sql4 = 'DELETE FROM bpm WHERE id = :billId';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['modify']) {
                try {
                    $conn = $db->connect();

                    // Check for user
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':billId', $billId);
                    $stmt->execute();
                    $user_id = $stmt->fetch(PDO::FETCH_ASSOC)['user'];
                    if ($userId == 1 || $user_id == $userId) {
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':billId', $billId);
                        $stmt->execute();
                        $grnlistIds = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($grnlistIds as $v) {
                            $stmt = $conn->prepare($sql2);
                            $stmt->bindParam(':grnlistId', $v['grnlistId']);
                            $stmt->execute();
                        }
                        $stmt = $conn->prepare($sql3);
                        $stmt->bindParam(':billId', $billId);
                        $stmt->execute();
                        $stmt = $conn->prepare($sql4);
                        $stmt->bindParam(':billId', $billId);
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
                        bpm.id, 
                        bpm.regNo, 
                        bpm.billNo, 
                        bpm.date,
                        bpm.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        bpm.user AS userId, 
                        users.name AS userName 
                    FROM bpm 
                    JOIN suppliers ON bpm.supplier = suppliers.id 
                    JOIN users ON bpm.user = users.id 
                    WHERE 
                        date BETWEEN  CAST(:from AS DATE) AND CAST(:to AS DATE) 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        books.id AS bookId,
                        books.name AS bookName,
                        list.grnlistId, 
                        grn.grnNo, 
                        grn.date AS qtyRecDate, 
                        grnlist.item AS itemId, 
                        items.name AS item, 
                        grnlist.qtyRec, 
                        list.billedQty, 
                        grnlist.unit AS unitId, 
                        units.name AS unit 
                    FROM bpmlist AS list  
                    JOIN grnlist ON list.grnlistId = grnlist.id 
                    JOIN grn ON grnlist.grnId = grn.id
                    JOIN books ON grn.book = books.id
                    JOIN units ON grnlist.unit = units.id 
                    JOIN items ON grnlist.item = items.id 
                    WHERE
                        list.billId = :billId';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['view']) {
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
                            $billId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':billId', $billId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        bpm.id, 
                        bpm.regNo, 
                        bpm.billNo, 
                        bpm.date, 
                        bpm.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        bpm.user AS userId, 
                        users.name AS userName 
                    FROM bpm 
                    JOIN suppliers ON bpm.supplier = suppliers.id 
                    JOIN users ON bpm.user = users.id 
                    WHERE 
                        modified_at BETWEEN  CAST(:from AS DATE) AND CAST(:to AS DATE) 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        books.id AS bookId,
                        books.name AS bookName,
                        list.grnlistId, 
                        grn.grnNo, 
                        grn.date AS qtyRecDate, 
                        grnlist.item AS itemId, 
                        items.name AS item, 
                        grnlist.qtyRec, 
                        list.billedQty, 
                        grnlist.unit AS unitId, 
                        units.name AS unit 
                    FROM bpmlist AS list  
                    JOIN grnlist ON list.grnlistId = grnlist.id 
                    JOIN grn ON grnlist.grnId = grn.id
                    JOIN books ON grn.book = books.id
                    JOIN units ON grnlist.unit = units.id 
                    JOIN items ON grnlist.item = items.id 
                    WHERE
                        list.billId = :billId';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['view']) {
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
                            $billId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':billId', $billId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        bpm.id, 
                        bpm.regNo, 
                        bpm.billNo, 
                        bpm.date,
                        bpm.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        bpm.user AS userId, 
                        users.name AS userName 
                    FROM bpm 
                    JOIN suppliers ON bpm.supplier = suppliers.id 
                    JOIN users ON bpm.user = users.id 
                    WHERE 
                        bpm.supplier = :supplier 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        books.id AS bookId,
                        books.name AS bookName,
                        list.grnlistId, 
                        grn.grnNo, 
                        grn.date AS qtyRecDate, 
                        grnlist.item AS itemId, 
                        items.name AS item, 
                        grnlist.qtyRec, 
                        list.billedQty, 
                        grnlist.unit AS unitId, 
                        units.name AS unit 
                    FROM bpmlist AS list  
                    JOIN grnlist ON list.grnlistId = grnlist.id 
                    JOIN grn ON grnlist.grnId = grn.id
                    JOIN books ON grn.book = books.id
                    JOIN units ON grnlist.unit = units.id 
                    JOIN items ON grnlist.item = items.id 
                    WHERE
                        list.billId = :billId';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['view']) {
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
                            $billId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':billId', $billId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        bpm.id, 
                        bpm.regNo, 
                        bpm.billNo, 
                        bpm.date, 
                        bpm.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        bpm.user AS userId, 
                        users.name AS userName 
                    FROM bpm 
                    JOIN suppliers ON bpm.supplier = suppliers.id 
                    JOIN users ON bpm.user = users.id 
                    WHERE 
                        bpm.book = :book 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        books.id AS bookId,
                        books.name AS bookName,
                        list.grnlistId, 
                        grn.grnNo, 
                        grn.date AS qtyRecDate, 
                        grnlist.item AS itemId, 
                        items.name AS item, 
                        grnlist.qtyRec, 
                        list.billedQty, 
                        grnlist.unit AS unitId, 
                        units.name AS unit 
                    FROM bpmlist AS list  
                    JOIN grnlist ON list.grnlistId = grnlist.id 
                    JOIN grn ON grnlist.grnId = grn.id
                    JOIN books ON grn.book = books.id
                    JOIN units ON grnlist.unit = units.id 
                    JOIN items ON grnlist.item = items.id 
                    WHERE
                        list.billId = :billId';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['view']) {
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
                            $billId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':billId', $billId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        # Fetch By Bill No
        public static function FetchByBillNo($db, string $userId, string $billNo, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $billNo = htmlspecialchars(strip_tags($billNo));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        bpm.id, 
                        bpm.regNo, 
                        bpm.billNo, 
                        bpm.date, 
                        bpm.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        bpm.user AS userId, 
                        users.name AS userName 
                    FROM bpm 
                    JOIN books ON bpm.book = books.id 
                    JOIN suppliers ON bpm.supplier = suppliers.id 
                    JOIN users ON bpm.user = users.id 
                    WHERE 
                        bpm.billNo = :billNo 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        books.id AS bookId,
                        books.name AS bookName,
                        list.grnlistId, 
                        grn.grnNo, 
                        grn.date AS qtyRecDate, 
                        grnlist.item AS itemId, 
                        items.name AS item, 
                        grnlist.qtyRec, 
                        list.billedQty, 
                        grnlist.unit AS unitId, 
                        units.name AS unit 
                    FROM bpmlist AS list  
                    JOIN grnlist ON list.grnlistId = grnlist.id 
                    JOIN grn ON grnlist.grnId = grn.id
                    JOIN books ON grn.book = books.id
                    JOIN units ON grnlist.unit = units.id 
                    JOIN items ON grnlist.item = items.id 
                    WHERE
                        list.billId = :billId';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['view']) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':billNo', $billNo);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($data as $k => $v) {
                            $billId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':billId', $billId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        # Fetch By Reg No
        public static function FetchByRegNo($db, string $userId, string $regNo, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $regNo = htmlspecialchars(strip_tags($regNo));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        bpm.id, 
                        bpm.regNo, 
                        bpm.billNo, 
                        bpm.date, 
                        bpm.supplier AS supplierId, 
                        suppliers.name AS supplierName, 
                        bpm.user AS userId, 
                        users.name AS userName 
                    FROM bpm 
                    JOIN suppliers ON bpm.supplier = suppliers.id 
                    JOIN users ON bpm.user = users.id 
                    WHERE 
                        bpm.regNo = :regNo 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        books.id AS bookId,
                        books.name AS bookName,
                        list.grnlistId, 
                        grn.grnNo, 
                        grn.date AS qtyRecDate, 
                        grnlist.item AS itemId, 
                        items.name AS item, 
                        grnlist.qtyRec, 
                        list.billedQty, 
                        grnlist.unit AS unitId, 
                        units.name AS unit 
                    FROM bpmlist AS list  
                    JOIN grnlist ON list.grnlistId = grnlist.id 
                    JOIN grn ON grnlist.grnId = grn.id
                    JOIN books ON grn.book = books.id
                    JOIN units ON grnlist.unit = units.id 
                    JOIN items ON grnlist.item = items.id 
                    WHERE
                        list.billId = :billId';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['view']) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':regNo', $regNo);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($data as $k => $v) {
                            $billId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':billId', $billId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        # Fetch GRN List By GRN ID
        public static function BpmListByBillId($db, string $userId, string $billId, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $billId = htmlspecialchars(strip_tags($billId));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT
                        list.id, 
                        list.grnlistId, 
                        books.name AS bookName,
                        grn.grnNo, 
                        grn.date AS qtyRecDate, 
                        grnlist.item AS itemId, 
                        items.name AS item, 
                        grnlist.qtyRec, 
                        list.billedQty, 
                        grnlist.unit AS unitId, 
                        units.name AS unit 
                    FROM bpmlist AS list  
                    JOIN grnlist ON list.grnlistId = grnlist.id 
                    JOIN grn ON grnlist.grnId = grn.id
                    JOIN books ON grn.book = books.id
                    JOIN units ON grnlist.unit = units.id 
                    JOIN items ON grnlist.item = items.id 
                    WHERE
                        list.billId = :billId';

            // Main Function
            if ($P['success'] && $P['data']['bpm']['view']) {
                try {
                    $conn = $db->connect();
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':billId', $billId);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                        return array('msg'=>["BPM List Found."],  'data'=>$data,  'success'=>true);
                    } else {
                        return array('msg'=>["BPM List Not Found!"], 'success'=>false);
                    }
                }
                catch(PDOException $e) {
                    return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
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