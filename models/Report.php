<?php    
    require_once('Permissions.php');

    class Report {
        // Static Methods
        # Fetch By Date And Supplier
        public static function Report1($db, string $userId, string $supplier, string $from, string $to, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $supplier = htmlspecialchars(strip_tags($supplier));
            $from = htmlspecialchars(strip_tags($from));
            $to = htmlspecialchars(strip_tags($to));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        bpm.id, 
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
                        bpm.supplier = :supplier AND date BETWEEN  CAST(:from AS DATE) AND CAST(:to AS DATE) 
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        books.id AS bookId,
                        books.name AS bookName,
                        list.grnlistId, 
                        grn.site AS siteName, 
                        grn.vehicle AS vehicleNo, 
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
            if ($P['success'] && ($P['data']['bpm']['report'] || $P['data']['grn']['report'])) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':supplier', $supplier);
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
        # Fetch By Date And Site
        public static function Report2($db, string $userId, string $site, string $from, string $to, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $site = htmlspecialchars(strip_tags($site));
            $from = htmlspecialchars(strip_tags($from));
            $to = htmlspecialchars(strip_tags($to));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        grn.site AS siteName,
                        suppliers.name AS supplierName,
                        items.name AS itemName,
                        grn.date AS qtyRecDate,
                        grn.grnNo AS grnNo,
                        grnlist.qtyRec AS qtyRec,
                        units.name AS unit,
                        bpm.date AS billedDate,
                        bpm.billNo AS billNo,
                        bpmlist.billedQty AS billedQty,
                        (grnlist.qtyRec - bpmlist.billedQty) AS balQty
                    FROM grn
                    JOIN suppliers ON grn.supplier = suppliers.id
                    JOIN grnlist ON grn.id = grnlist.grnId
                    JOIN items ON grnlist.item = items.id
                    JOIN units ON grnlist.unit = units.id
                    LEFT JOIN bpmlist ON grnlist.id = bpmlist.grnlistId
                    LEFT JOIN bpm ON bpmlist.billId = bpm.id
                    WHERE 
                        grn.date BETWEEN  CAST(:from AS DATE) AND CAST(:to AS DATE) AND grn.site= :site 
                    ORDER BY grn.date '.$ORDER.' ,grn.grnNo '.$ORDER;

            // Main Function
            if ($P['success'] && ($P['data']['bpm']['report'] || $P['data']['grn']['report'])) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':site', $site);
                    $stmt->bindParam(':from', $from);
                    $stmt->bindParam(':to', $to);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        # Fetch By Date - GRN List
        public static function Report3($db, string $userId, string $from, string $to, $ORDER='ASC') {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));
            $from = htmlspecialchars(strip_tags($from));
            $to = htmlspecialchars(strip_tags($to));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);
            
            // All SQL Commands
            $sql0 = 'SELECT 
                        bpm.id, 
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
                        grn.site AS siteName, 
                        grn.vehicle AS vehicleNo, 
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
            if ($P['success'] && ($P['data']['bpm']['report'] || $P['data']['grn']['report'])) {
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
        # Fetch By Date - GRN Status
        public static function Report4($db, string $userId, string $from, string $to, $ORDER='ASC') {
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
                    JOIN suppliers ON grn.supplier = suppliers.id 
                    JOIN users ON grn.user = users.id 
                    WHERE 
                        date BETWEEN  CAST(:from AS DATE) AND CAST(:to AS DATE) 
                    ORDER BY id '.$ORDER;

            // Main Function
            if ($P['success'] && ($P['data']['bpm']['report'] || $P['data']['grn']['report'])) {
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
        # Fetch By Date - GRN Filter By Yeat to billed
        public static function Report5($db, string $userId, string $from, string $to, $ORDER='ASC') {
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
                        suppliers.name AS supplierName,
                        grn.site AS siteName, 
                        grn.vehicle AS vehicleNo
                    FROM grn 
                    JOIN suppliers ON grn.supplier = suppliers.id 
                    WHERE 
                        date BETWEEN  CAST(:from AS DATE) AND CAST(:to AS DATE) AND cancelled <> 1
                    ORDER BY id '.$ORDER;
            $sql1 = 'SELECT
                        list.id, 
                        list.item AS itemId, 
                        items.name AS item, 
                        list.qtyRec, 
                        list.unit AS unitId, 
                        units.name AS unit 
                    FROM grnlist AS list 
                    JOIN items ON list.item = items.id 
                    JOIN units ON list.unit = units.id 
                    WHERE
                        list.grnId = :grnId AND list.billed <> 1';

            // Main Function
            if ($P['success'] && ($P['data']['bpm']['report'] || $P['data']['grn']['report'])) {
                try {
                    $conn = $db->connect();

                    // Insert GRN Details
                    $stmt = $conn->prepare($sql0);
                    $stmt->bindParam(':from', $from);
                    $stmt->bindParam(':to', $to);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    $f = 0;
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($data as $k => $v) {
                            $grnId = $v['id'];
                            $stmt = $conn->prepare($sql1);
                            $stmt->bindParam(':grnId', $grnId);
                            $stmt->execute();
                            $data[$k]['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($data[$k]['list']) > 0) { $f = 1; }
                        }
                        if ($f) {
                            return array('msg'=>[$cnt." Results found."],  'data'=>$data,  'success'=>true);
                        } else {
                            return array('msg'=>["No Result Found!"], 'success'=>false);
                        }
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
        # General Report
        public static function Report6($db, string $userId, array $post) {
            // Clean Data
            $userId = htmlspecialchars(strip_tags($userId));

            // Permission
            $P = Permissions::getUserPermissions($db,$userId);

            // Gen SQL Query
            $sql = 'SELECT ';
            $view = array();
            $table = array();
            $by = array();
            if(isset($post['grn']) && isset($post['bpm'])) {
                array_push($table, ' bpm');
                foreach ($post['bpm'] as $key => $value) {
                    switch ($key) {
                        case 'regNo':
                            array_push($view,' bpm.regNo AS "Reg.No"');
                            break;
                        case 'billNo':
                            array_push($view,' bpm.billNo AS "Bill.No"');
                            break;
                        case 'date':
                            array_push($view,' bpm.date AS "QtyBilledDate"');
                            break;
                        case 'supplier':
                            // array_push($view,' bpm.supplier AS supplierId');
                            array_push($view,' suppliers.name AS "Supplier"');
                            array_push($table,' JOIN suppliers ON  bpm.supplier = suppliers.id');
                            break;
                        case 'item':
                            // array_push($view,' bpmlist.item AS itemId');
                            array_push($view,' items.name AS Item');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN items ON  grnlist.item = items.id');
                            break;
                        case 'qtyBilled':
                            array_push($view,' bpmlist.billedQty AS "Qty Billed"');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            break;
                        case 'unit':
                            // array_push($view,' bpmlist.unit AS unitId');
                            array_push($view,' units.name AS Unit');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN units ON  grnlist.unit = units.id');
                            break;
                        case 'balance':
                            array_push($view,' (grnlist.qtyRec - bpmlist.billedQty) AS Balance');
                            // array_push($view,' units.name AS balanceUnit');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN units ON  grnlist.unit = units.id');
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                unset($post['bpm']);

                foreach ($post['grn'] as $key => $value) {
                    switch ($key) {
                        case 'grnNo':
                            array_push($view,' grn.grnNo');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN grn ON  grnlist.grnId = grn.id');
                            break;
                        case 'date':
                            array_push($view,' grn.date AS qtyRecDate');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN grn ON  grnlist.grnId = grn.id');
                            break;
                        case 'site':
                            array_push($view,' grn.site');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN grn ON  grnlist.grnId = grn.id');
                            break;
                        case 'vehicle':
                            array_push($view,' grn.vehicle');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN grn ON  grnlist.grnId = grn.id');
                            break;
                        case 'supplier':
                            // array_push($view,' grn.supplier AS supplierId');
                            array_push($view,' suppliers.name AS supplier');
                            array_push($table,' JOIN suppliers ON  bpm.supplier = suppliers.id');
                            break;
                        case 'book':
                            // array_push($view,' grn.book AS bookId');
                            array_push($view,' books.name AS book');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN grn ON  grnlist.grnId = grn.id');
                            array_push($table,' JOIN books ON  grn.book = books.id');
                            break;
                        case 'item':
                            // array_push($view,' grnlist.item AS itemId');
                            array_push($view,' items.name AS item');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN items ON  grnlist.item = items.id');
                            break;
                        case 'qtyRec':
                            array_push($view,' grnlist.qtyRec');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            break;
                        case 'unit':
                            // array_push($view,' grnlist.unit AS unitId');
                            array_push($view,' units.name AS unit');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN units ON  grnlist.unit = units.id');
                            break;
                        case 'cancelled':
                            array_push($view,' grn.cancelled');
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                unset($post['grn']);
                foreach ($post as $key => $value) {
                    if(!empty($value)) {
                        switch ($key) {
                            case 'sfDate':
                                $date = explode('-',$value);
                                $from = implode('-', array_reverse(explode('/',trim($date[0]))));
                                $to = implode('-', array_reverse(explode('/',trim($date[1]))));
                                array_push($by,' bpm.date BETWEEN  CAST("'.$from.'" AS DATE) AND CAST("'.$to.'" AS DATE)');
                                break;
                            case 'sfBook':
                                array_push($by,' bpm.book = '.$value);
                                break;
                            case 'sfSite':
                                array_push($by,' grn.site = "'.$value.'"');
                                array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                                array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                                array_push($table,' JOIN grn ON  grnlist.grnId = grn.id');
                                break;
                            case 'sfSupplier':
                                array_push($by,' bpm.supplier = '.$value);
                                break;
                            case 'sfItem':
                                array_push($by,' grnlist.item = '.$value);
                                array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                                array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                                break;
                            case 'sfYetToBill':
                                return array('msg'=>["BPM View dosen\'t support the search criteria!"], 'success'=>false);
                                break;
                            default:
                                # code...
                                break;
                        }
                    }
                }
            } elseif (isset($post['grn'])) {
                array_push($table, ' grn');
                foreach ($post['grn'] as $key => $value) {
                    switch ($key) {
                        case 'grnNo':
                            array_push($view,' grn.grnNo');
                            break;
                        case 'date':
                            array_push($view,' grn.date AS qtyRecDate');
                            break;
                        case 'site':
                            array_push($view,' grn.site');
                            break;
                        case 'vehicle':
                            array_push($view,' grn.vehicle');
                            break;
                        case 'supplier':
                            // array_push($view,' grn.supplier AS supplierId');
                            array_push($view,' suppliers.name AS supplier');
                            array_push($table,' JOIN suppliers ON  grn.supplier = suppliers.id');
                            break;
                        case 'book':
                            // array_push($view,' grn.book AS bookId');
                            array_push($view,' books.name AS book');
                            array_push($table,' JOIN books ON  grn.book = books.id');
                            break;
                        case 'item':
                            // array_push($view,' grnlist.item AS itemId');
                            array_push($view,' items.name AS item');
                            array_push($table,' JOIN grnlist ON  grnlist.grnId = grn.id');
                            array_push($table,' JOIN items ON  grnlist.item = items.id');
                            break;
                        case 'qtyRec':
                            array_push($view,' grnlist.qtyRec');
                            array_push($table,' JOIN grnlist ON  grnlist.grnId = grn.id');
                            break;
                        case 'unit':
                            // array_push($view,' grnlist.unit AS unitId');
                            array_push($view,' units.name AS unit');
                            array_push($table,' JOIN grnlist ON  grnlist.grnId = grn.id');
                            array_push($table,' JOIN units ON  grnlist.unit = units.id');
                            break;
                        case 'cancelled':
                            array_push($view,' grn.cancelled');
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                unset($post['grn']);

                foreach ($post as $key => $value) {
                    if(!empty($value)) {
                        switch ($key) {
                            case 'sfDate':
                                $date = explode('-',$value);
                                $from = implode('-', array_reverse(explode('/',trim($date[0]))));
                                $to = implode('-', array_reverse(explode('/',trim($date[1]))));
                                array_push($by,' grn.date BETWEEN  CAST("'.$from.'" AS DATE) AND CAST("'.$to.'" AS DATE)');
                                break;
                            case 'sfBook':
                                    array_push($by,' grn.book = '.$value);
                                    break;
                            case 'sfSupplier':
                                    array_push($by,' grn.supplier = '.$value);
                                    break;
                            case 'sfSite':
                                    array_push($by,' grn.site = "'.$value.'"');
                                    break;
                            case 'sfItem':
                                    array_push($by,' grnlist.item = '.$value);
                                    array_push($table,' JOIN grnlist ON  grnlist.grnId = grn.id');
                                    break;
                            case 'sfYetToBill':
                                array_push($by,' grnlist.billed = 0');
                                array_push($table,' JOIN grnlist ON  grnlist.grnId = grn.id');
                                break;
                            default:
                                # code...
                                break;
                        }
                    }
                }
            } elseif (isset($post['bpm'])) {
                array_push($table, ' bpm');
                foreach ($post['bpm'] as $key => $value) {
                    switch ($key) {
                        case 'regNo':
                            array_push($view,' bpm.regNo AS "Reg.No"');
                            break;
                        case 'billNo':
                            array_push($view,' bpm.billNo AS "Bill.No"');
                            break;
                        case 'date':
                            array_push($view,' bpm.date AS "QtyBilledDate"');
                            break;
                        case 'supplier':
                            // array_push($view,' bpm.supplier AS supplierId');
                            array_push($view,' suppliers.name AS Supplier');
                            array_push($table,' JOIN suppliers ON  bpm.supplier = suppliers.id');
                            break;
                        case 'item':
                            // array_push($view,' bpmlist.item AS itemId');
                            array_push($view,' items.name AS Item');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN items ON  grnlist.item = items.id');
                            break;
                        case 'qtyBilled':
                            array_push($view,' bpmlist.billedQty AS "Qty Billed"');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            break;
                        case 'unit':
                            // array_push($view,' bpmlist.unit AS unitId');
                            array_push($view,' units.name AS Unit');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN units ON  grnlist.unit = units.id');
                            break;
                        case 'balance':
                            array_push($view,' (grnlist.qtyRec - bpmlist.billedQty) AS Balance');
                            // array_push($view,' units.name AS balanceUnit');
                            array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                            array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                            array_push($table,' JOIN units ON  grnlist.unit = units.id');
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                unset($post['bpm']);

                foreach ($post as $key => $value) {
                    if(!empty($value)) {
                        switch ($key) {
                            case 'sfDate':
                                $date = explode('-',$value);
                                $from = implode('-', array_reverse(explode('/',trim($date[0]))));
                                $to = implode('-', array_reverse(explode('/',trim($date[1]))));
                                array_push($by,' bpm.date BETWEEN  CAST("'.$from.'" AS DATE) AND CAST("'.$to.'" AS DATE)');
                                break;
                            case 'sfBook':
                                    return array('msg'=>["BPM View dosen\'t support the search criteria!"], 'success'=>false);
                                    break;
                            case 'sfSupplier':
                                    array_push($by,' bpm.supplier = '.$value);
                                    break;
                            case 'sfSite':
                                    return array('msg'=>["BPM View dosen\'t support the search criteria!"], 'success'=>false);
                                    break;
                            case 'sfItem':
                                    array_push($by,' grnlist.item = '.$value);
                                    array_push($table,' JOIN bpmlist ON  bpmlist.billId = bpm.id');
                                    array_push($table,' JOIN grnlist ON  bpmlist.grnlistId = grnlist.id');
                                    break;
                            case 'sfYetToBill':
                                return array('msg'=>["BPM View dosen\'t support the search criteria!"], 'success'=>false);
                                break;
                            default:
                                # code...
                                break;
                        }
                    }
                }
            } else {
                return array('msg'=>["No View Selected!"], 'success'=>false);
            }

            $sql = $sql.implode(',', array_unique($view)).' FROM '.implode(' LEFT OUTER ', array_unique($table)).' WHERE '.implode(' AND', array_unique($by));
            // return array('msg'=>["No Result Found!",$post,$sql], 'success'=>false);

            // Main Function
            if ($P['success'] && ($P['data']['bpm']['report'] || $P['data']['grn']['report'])) {
                try {
                    $conn = $db->connect();
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $cnt = $stmt->rowCount();
                    if ($stmt->rowCount() > 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        public static function DashboardFeed($db) {
            // All SQL
            $sql0 = 'SELECT 
                        (   SELECT COUNT(*) FROM suppliers  )   AS suppliers,
                        (   SELECT COUNT(*) FROM items      )   AS items,
                        (   SELECT COUNT(*) FROM   grn      )   AS grn,
                        (   SELECT COUNT(*) FROM   bpm      )   AS bpm
                    FROM    dual';
            try {
                $conn = $db->connect();

                $stmt = $conn->prepare($sql0);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                return array('msg'=>[" Results found."],  'data'=>$data,  'success'=>true);
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
    }