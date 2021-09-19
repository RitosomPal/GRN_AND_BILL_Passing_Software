<?php
    require_once('DotEnv.php');
    (new DotEnv(__DIR__ . '/.env'))->load();

    class Database {
        // DB Params
        private $host;
        private $db_name;
        private $username;
        private $password;
        private $conn = null;

        public function __construct() {
            $this->host = $_ENV['DB_HOST'];
            $this->db_name = $_ENV['DB_DATABASE'];
            $this->username = $_ENV['DB_USERNAME'];
            $this->password = $_ENV['DB_PASSWORD'];
            date_default_timezone_set($_ENV['APP_TIMEZONE']);
        }

        // Get DB Name
        public function getDBName() {
            return $this->db_name;
        }

        // DB Connect
        public function connect($db_name = '') {
            $this->conn = null;
            $this->db_name = $_ENV['DB_DATABASE'].htmlspecialchars(strip_tags($db_name));;

            try {
                $this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e) {
                if($e->getCode() == 1049) {
                    header('location: ./restore');
                }
                echo 'Connection Error: ' . $e->getMessage();
            }
            return $this->conn;
        }

        // DB Connect Specific
        public function connectSpecific($db_name) {
            $this->conn = null;
            $this->db_name = htmlspecialchars(strip_tags($db_name));

            try {
                $this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e) {
                echo 'Connection Error: ' . $e->getMessage();
            }
            return $this->conn;
        }

        // Backup
        private function templateSql(bool $start=TRUE) {
            if($start) {
                return '
                    SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
                    START TRANSACTION;
                    SET time_zone = "+05:30";

                    CREATE TABLE `books` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` text NOT NULL,
                        `grnIdPattern` text NOT NULL,
                        `startingPage` int(11) NOT NULL DEFAULT 1,
                        `lastPage` int(11) NOT NULL,
                        `active` tinyint(1) NOT NULL DEFAULT 0,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `bpm` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `regNo` text NOT NULL,
                        `billNo` text NOT NULL,
                        `supplier` int(11) NOT NULL,
                        `date` date NOT NULL,
                        `user` int(11) NOT NULL,
                        `modified_at` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `bpmlist` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `billId` int(11) NOT NULL,
                        `grnlistId` int(11) NOT NULL,
                        `billedQty` float NOT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `grn` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `book` int(11) NOT NULL,
                        `grnNo` text NOT NULL,
                        `date` date NOT NULL,
                        `supplier` int(11) DEFAULT NULL,
                        `site` text NOT NULL,
                        `vehicle` text NOT NULL,
                        `cancelled` tinyint(1) NOT NULL,
                        `user` int(11) NOT NULL,
                        `modified_at` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `grnlist` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `grnId` int(11) NOT NULL,
                        `item` int(11) NOT NULL,
                        `qtyRec` float NOT NULL,
                        `unit` int(11) NOT NULL,
                        `billed` tinyint(1) NOT NULL DEFAULT 0,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `groups` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` text NOT NULL,
                        `permission` mediumtext NOT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `items` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` text NOT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `suppliers` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` text NOT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `sites` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` text NOT NULL,
                        `active` tinyint(1) NOT NULL DEFAULT 0,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `units` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` text NOT NULL,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE `users` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `image` text DEFAULT NULL,
                        `name` text NOT NULL,
                        `username` text NOT NULL,
                        `password` mediumtext NOT NULL,
                        `groupId` int(11) NOT NULL,
                        `lastAccess` text DEFAULT NULL,
                        `active` tinyint(1) NOT NULL DEFAULT 1,
                        PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ';
            } else {
                return '
                    ALTER TABLE `bpm`
                        ADD KEY `supplier` (`supplier`),
                        ADD KEY `user` (`user`);

                    ALTER TABLE `bpmlist`
                        ADD KEY `billId` (`billId`),
                        ADD KEY `grnlistId` (`grnlistId`);

                    ALTER TABLE `grn`
                        ADD UNIQUE KEY `grnNo` (`grnNo`) USING HASH,
                        ADD KEY `book` (`book`),
                        ADD KEY `supplier` (`supplier`),
                        ADD KEY `user` (`user`);

                    ALTER TABLE `grnlist`
                        ADD KEY `item` (`item`),
                        ADD KEY `grnId` (`grnId`),
                        ADD KEY `unit` (`unit`);

                    ALTER TABLE `users`
                        ADD KEY `groupId` (`groupId`);

                    ALTER TABLE `bpm`
                        ADD CONSTRAINT `bpm_ibfk_2` FOREIGN KEY (`supplier`) REFERENCES `suppliers` (`id`),
                        ADD CONSTRAINT `bpm_ibfk_3` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

                    ALTER TABLE `bpmlist`
                        ADD CONSTRAINT `bpmlist_ibfk_1` FOREIGN KEY (`billId`) REFERENCES `bpm` (`id`),
                        ADD CONSTRAINT `bpmlist_ibfk_2` FOREIGN KEY (`grnlistId`) REFERENCES `grnlist` (`id`);

                    ALTER TABLE `grn`
                        ADD CONSTRAINT `grn_ibfk_1` FOREIGN KEY (`book`) REFERENCES `books` (`id`),
                        ADD CONSTRAINT `grn_ibfk_2` FOREIGN KEY (`supplier`) REFERENCES `suppliers` (`id`),
                        ADD CONSTRAINT `grn_ibfk_3` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

                    ALTER TABLE `grnlist`
                        ADD CONSTRAINT `grnlist_ibfk_1` FOREIGN KEY (`item`) REFERENCES `items` (`id`),
                        ADD CONSTRAINT `grnlist_ibfk_2` FOREIGN KEY (`grnId`) REFERENCES `grn` (`id`),
                        ADD CONSTRAINT `grnlist_ibfk_3` FOREIGN KEY (`unit`) REFERENCES `units` (`id`);

                    ALTER TABLE `users`
                        ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`groupId`) REFERENCES `groups` (`id`);
                    
                    COMMIT;
                ';
            }
        }

        public function Backup() {
            $dbName = $_ENV['DB_DATABASE'];
            $fName = $dbName.'-backup_' . date('Y-m-d');
            $SQL = $this->templateSql();

            try {
                $conn = $this->connect();

                // Get All Tables
                $stmt = $conn->query('SHOW TABLES');
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                foreach($tables as $table) {
                    $stmt = $conn->query("SELECT * FROM $table");
                    $num_fields = $stmt->columnCount();
                    $num_rows = $stmt->rowCount();

                    //insert values
                    $qry = "";
                    if ($num_rows) {
                        $qry = "INSERT INTO `".$table."` (";
                        $stmt = $conn->query("SHOW COLUMNS FROM $table");
                        $cols = array();
                        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $col) { array_push($cols, "`".$col[0]."`"); }
                        $qry .= implode(', ', $cols).") VALUES";
                        $SQL .= "\n\t\t\t\t\t".$qry;

                        $qry = array();
                        $stmt = $conn->query("SELECT * FROM $table");
                        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $data) {
                            $values = array();
                            foreach($data as $d) {
                                if(is_numeric($d)) { array_push($values, $d); }
                                elseif(empty($d)) { array_push($values, 'NULL'); }
                                else { array_push($values, "'".$d."'"); }
                            }
                            array_push($qry, "(".implode(', ', $values).")");
                        }
                        $qry = implode(",\n\t\t\t\t\t",$qry).";\n";
                        $SQL .= "\n\t\t\t\t\t".$qry;
                    }
                }

                $SQL .= $this->templateSql(FALSE);

                $path = 'assets/backup';
                $zip = new ZipArchive;
                $res = $zip->open($path.'/'.$fName.'.zip', ZipArchive::CREATE);
                if ($res === TRUE) {
                    $zip->addFromString($_ENV['BACKUP_ZIP'].'.sql', $SQL);
                    $zip->setEncryptionName($_ENV['BACKUP_ZIP'].'.sql', ZipArchive::EM_AES_256, $_ENV['BACKUP_PASSWORD']);
                    $zip->close();
                } else {
                    return array('msg'=>["Unable to create backup file!"], 'success'=>false);
                }

                return array('msg'=> ['Backup Created!'], 'filePath'=>$path, 'fileName'=>$fName, 'success'=>True);
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }

        public function Recover($file) {
            if ($file['type'] != 'application/octet-stream') 
                return array('msg'=>['Invalid File.'], 'success'=>false);

            $SQL = NULL;
            $path = 'assets/backup/';
            $fName = explode('.',$file["name"]);
            array_pop($fName);
            $fName = implode('.',$fName);
            move_uploaded_file($file['tmp_name'], $path.$fName.'.zip');

            $zip = new ZipArchive;
            $res = $zip->open($path.$fName.'.zip');
            if ($res === TRUE) {
                if ($zip->setPassword($_ENV['BACKUP_PASSWORD'])) {
                    if (!$SQL = $zip->getFromName($_ENV['BACKUP_ZIP'].'.sql')) {
                        return array('msg'=>['Invalid File.'], 'success'=>false);
                    }
                } else {
                    return array('msg'=>['Invalid File.'], 'success'=>false);
                }
                $zip->close();
            } else {
                unlink($path.$fName.'.zip');
                return array('msg'=>['Invalid File.'], 'success'=>false);
            }
            unlink($path.$fName.'.zip');

            try {
                $conn = new PDO('mysql:host='.$this->host, $this->username, $this->password);
                $conn->exec("DROP DATABASE IF EXISTS `".$_ENV['DB_DATABASE']."`;");
                $conn->exec("CREATE DATABASE IF NOT EXISTS `".$_ENV['DB_DATABASE']."`;");
                $conn = $this->connect();
                $conn->exec($SQL);
                return array('msg'=>['Restored.'], 'success'=>True);
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }

        public function Reset(array $post) {
            // Extract Post Data
            extract($post);

            // Clean Data
            $userId = 1;
            $password = md5(htmlspecialchars(strip_tags($password)));
            $s1 = htmlspecialchars(strip_tags($s1));
            $s2 = htmlspecialchars(strip_tags($s2));
            $s3 = htmlspecialchars(strip_tags($s3));

            // All SQL Commands
            $sql0 = 'SELECT *  FROM users WHERE id = :userId AND password = :password';
            $sql1_1 = 'DELETE FROM bpmlist; ALTER TABLE bpmlist AUTO_INCREMENT = 1;';
            $sql1_2 = 'DELETE FROM bpm; ALTER TABLE bpm AUTO_INCREMENT = 1;';
            $sql1_3 = 'DELETE FROM grnlist; ALTER TABLE grnlist AUTO_INCREMENT = 1;';
            $sql1_4 = 'DELETE FROM grn; ALTER TABLE grn AUTO_INCREMENT = 1;';
            $sql2_1 = 'DELETE FROM units; ALTER TABLE units AUTO_INCREMENT = 1;';
            $sql2_2 = 'DELETE FROM items; ALTER TABLE items AUTO_INCREMENT = 1;';
            $sql2_3 = 'DELETE FROM suppliers; ALTER TABLE suppliers AUTO_INCREMENT = 1;';
            $sql2_4 = 'DELETE FROM books; ALTER TABLE books AUTO_INCREMENT = 1;';
            $sql3_1 = 'DELETE FROM users WHERE id <> 1';
            $sql3_2 = 'DELETE FROM groups WHERE id <> 1';

            try {
                $conn = $this->connect();

                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    $conn->exec($sql1_1);
                    $conn->exec($sql1_2);
                    $conn->exec($sql1_3);
                    $conn->exec($sql1_4);

                    if ($s2 || $s3) {
                        $conn->exec($sql2_1);
                        $conn->exec($sql2_2);
                        $conn->exec($sql2_3);
                        $conn->exec($sql2_4);
                    }

                    if ($s3) {
                        $conn->exec($sql3_1);
                        $conn->exec($sql3_2);
                    }

                    return array('msg'=>["Reset Completed!"], 'success'=>true);
                } else { 
                    return array('msg'=>["Invalid Password!"], 'success'=>false);   
                }
            }
            catch(PDOException $e) {
                return array('msg'=>["System Error!",$e->getMessage()], 'success'=>false);
            }
        }
 }
