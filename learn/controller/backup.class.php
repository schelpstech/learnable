<?php

class DBBackup
{
    private $db;
    private $suffix;

    public function __construct(PDO $db_conn)
    {
        $this->db = $db_conn;
        $this->suffix = date('Ymd_His');
    }

    public function runBackup($tables = '*')
    {
        $output = $this->generateBackupHeader();
        $tables = $this->getTableNames($tables);

        foreach ($tables as $table) {
            $output .= $this->generateTableDump($table);
        }

        $output .= $this->generateBackupFooter();
        $this->saveBackupToFile($output);
    }

    private function generateBackupHeader()
    {
        return "-- Database backup - " . date('Y-m-d H:i:s') . PHP_EOL
             . "SET NAMES utf8;" . PHP_EOL
             . "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';" . PHP_EOL
             . "SET foreign_key_checks = 0;" . PHP_EOL
             . "SET AUTOCOMMIT = 0;" . PHP_EOL
             . "START TRANSACTION;" . PHP_EOL;
    }

    private function getTableNames($tables)
    {
        if ($tables === '*') {
            $query = $this->db->query('SHOW TABLES');
            $tables = $query->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }
        return $tables;
    }

    private function generateTableDump($table)
    {
        $output = "";

        // Drop table if exists
        $output .= "DROP TABLE IF EXISTS `$table`;" . PHP_EOL;

        // Create table statement
        $createTableQuery = $this->db->query("SHOW CREATE TABLE `$table`");
        $createTableStmt = $createTableQuery->fetch(PDO::FETCH_COLUMN, 1);
        $output .= PHP_EOL . $createTableStmt . ";" . PHP_EOL;

        // Insert data
        $dataQuery = $this->db->query("SELECT * FROM `$table`");
        while ($row = $dataQuery->fetch(PDO::FETCH_ASSOC)) {
            $values = array_map([$this, 'escapeString'], array_values($row));
            $output .= "INSERT INTO `$table` VALUES (" . implode(',', $values) . ");" . PHP_EOL;
        }

        return $output;
    }

    private function escapeString($value)
    {
        if ($value === null) {
            return "NULL";
        }
        return "'" . addslashes($value) . "'";
    }

    private function generateBackupFooter()
    {
        return PHP_EOL . "COMMIT;" . PHP_EOL;
    }

    private function saveBackupToFile($content)
    {
        $filename = 'db_backup_' . $this->suffix . '.sql';
        file_put_contents($filename, $content);
        echo '<a href="./' . $filename . '">Click to download backup file</a>';
    }
}

?>
