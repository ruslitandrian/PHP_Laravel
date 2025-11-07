<?php
/**
 * Database connection class
 * Pure PHP PDO implementation
 */

class DatabaseConnection {
    private $pdo;
    
    public function __construct() {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("資料庫連接失敗: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
}

/**
 * Table handler class
 * Handles search, sorting, and pagination logic
 */
class TableHandler {
    private $pdo;
    
    public function __construct($dbConnection) {
        $this->pdo = $dbConnection->getConnection();
    }
    
    /**
     * Retrieve employee data (including search, sorting, and pagination)
     */
    public function getEmployees($search = '', $sortColumn = '', $sortDirection = 'ASC', $page = 1, $pageSize = 10) {
        // Build the WHERE conditions
        $whereConditions = [];
        $params = [];
        
        if (!empty($search)) {
            $whereConditions[] = "(name LIKE :search OR position LIKE :search OR office LIKE :search OR age LIKE :search OR start_date LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Build the ORDER BY clause
        $orderByClause = '';
        if (!empty($sortColumn)) {
            // Validate column names (prevent SQL injection)
            $allowedColumns = ['name', 'position', 'office', 'age', 'start_date'];
            if (in_array($sortColumn, $allowedColumns)) {
                $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';
                $orderByClause = "ORDER BY `{$sortColumn}` {$sortDirection}";
            }
        }
        
        // Calculate pagination
        $offset = ($page - 1) * $pageSize;
        
        // Query the total count
        $countSql = "SELECT COUNT(*) as total FROM employees {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        // Query the data
        $sql = "SELECT * FROM employees {$whereClause} {$orderByClause} LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize
        ];
    }
    
    /**
     * Initialize the table if it does not exist
     */
    public function initializeTable() {
        $sql = "CREATE TABLE IF NOT EXISTS employees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            position VARCHAR(100) NOT NULL,
            office VARCHAR(100) NOT NULL,
            age INT NOT NULL,
            start_date DATE NOT NULL,
            INDEX idx_name (name),
            INDEX idx_position (position),
            INDEX idx_office (office),
            INDEX idx_age (age),
            INDEX idx_start_date (start_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->pdo->exec($sql);
    }
    
    /**
     * Insert sample data
     */
    public function insertSampleData() {
        // Check whether data already exists
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM employees");
        $count = $stmt->fetch()['count'];
        
        if ($count > 0) {
            return; // Data already exists; skip duplicate insertion
        }
        
        $sampleData = [
            ['Prescott Barlett', 'Technical Author', 'London', 27, '2011-05-07'],
            ['Gavin Cortez', 'Team Leader', 'San Francisco', 22, '2008-10-26'],
            ['Gloria Little', 'System Administrator', 'New York', 59, '2009-04-01'],
            ['Lael Greet', 'Support Lead', 'London', 30, '2011-04-25'],
            ['Bradley Greer', 'Software Engineer', 'London', 41, '2012-10-13'],
            ['Dai Rios', 'Personnel Lead', 'Edinburgh', 35, '2012-09-26'],
            ['Jenette Caldwell', 'Development Lead', 'New York', 30, '2011-09-03'],
            ['Yuri Berry', 'Chief Marketing Officer', 'New York', 40, '2009-06-25'],
            ['Caesar Vance', 'Pre-Sales Support', 'New York', 21, '2011-12-12'],
            ['Doris Wilder', 'Sales Assistant', 'Sydney', 23, '2010-09-20'],
            ['Angelica Ramos', 'Chief Executive Officer', 'London', 47, '2009-10-09'],
            ['Gavin Joyce', 'Developer', 'Edinburgh', 42, '2010-12-22'],
            ['Jennifer Chang', 'Regional Director', 'Singapore', 28, '2010-11-14'],
            ['Brenden Wagner', 'Software Engineer', 'San Francisco', 28, '2011-06-07'],
            ['Fiona Green', 'Chief Operating Officer', 'San Francisco', 48, '2010-03-11'],
            ['Shou Itou', 'Regional Marketing', 'Tokyo', 20, '2011-08-14'],
            ['Michelle House', 'Integration Specialist', 'Sydney', 37, '2011-06-02'],
            ['Suki Burks', 'Developer', 'London', 53, '2009-10-22'],
            ['Prescott Bartlett', 'Technical Author', 'London', 27, '2011-05-07'],
            ['Gavin Cortez', 'Team Leader', 'San Francisco', 22, '2008-10-26']
        ];
        
        $sql = "INSERT INTO employees (name, position, office, age, start_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($sampleData as $row) {
            $stmt->execute($row);
        }
    }
}




