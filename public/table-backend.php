<?php
/**
 * Pure PHP backend solution for table search, sorting, and pagination
 * Framework-free, using native PHP and PDO
 */

// Load configuration and database connection
require_once __DIR__ . '/../config/db-config.php';
require_once __DIR__ . '/../config/db-connection.php';

// Initialize the database connection
$db = new DatabaseConnection();

// Handle GET parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : '';
$sortDirection = isset($_GET['dir']) ? strtoupper($_GET['dir']) : 'ASC';
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$pageSize = isset($_GET['page_size']) ? intval($_GET['page_size']) : 10;

// Validate sort column (prevent SQL injection)
$allowedColumns = ['name', 'position', 'office', 'age', 'start_date'];
if (!in_array($sortColumn, $allowedColumns)) {
    $sortColumn = '';
}

// Validate sort direction
if (!in_array($sortDirection, ['ASC', 'DESC'])) {
    $sortDirection = 'ASC';
}

// Validate items per page
$allowedPageSizes = [5, 10, 25, 50, 100];
if (!in_array($pageSize, $allowedPageSizes)) {
    $pageSize = 10;
}

// Handle sorting logic: no sort -> ASC -> DESC -> no sort
$actualSortColumn = '';
$actualSortDirection = '';

if ($sortColumn) {
    // Use provided sorting parameters when available
    $actualSortColumn = $sortColumn;
    $actualSortDirection = $sortDirection;
} else {
    // Check for existing sort state (from URL parameters)
    if (isset($_GET['sort']) && $_GET['sort'] === '') {
        // An empty string means no sorting
        $actualSortColumn = '';
        $actualSortDirection = '';
    }
}

// Execute search and sorting query
$tableHandler = new TableHandler($db);
$result = $tableHandler->getEmployees($search, $actualSortColumn, $actualSortDirection, $currentPage, $pageSize);

// Calculate total pages
$totalPages = ceil($result['total'] / $pageSize);

// Calculate the display range
$startItem = $result['total'] > 0 ? (($currentPage - 1) * $pageSize) + 1 : 0;
$endItem = min($currentPage * $pageSize, $result['total']);

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>表格搜尋、排序、分頁 - 後端解決方案</title>
    <link rel="stylesheet" href="table-backend-styles.css">
</head>
<body>
    <div class="container">
        <h1>員工資料表（後端處理）</h1>
        
        <!-- Search section -->
        <div class="search-container">
            <form method="GET" action="table-backend.php" class="search-form">
                <div class="search-box-wrapper">
                    <input type="text" 
                           name="search" 
                           id="searchInput" 
                           class="search-input" 
                           placeholder="搜尋表格內容..."
                           value="<?php echo htmlspecialchars($search); ?>">
                    <span class="search-icon">🔍</span>
                    <?php if ($search): ?>
                        <a href="table-backend.php?page=1&page_size=<?php echo $pageSize; ?><?php echo $sortColumn ? '&sort=' . $sortColumn . '&dir=' . $sortDirection : ''; ?>" 
                           class="clear-btn">✕</a>
                    <?php endif; ?>
                </div>
                <!-- Preserve other parameters -->
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortColumn); ?>">
                <input type="hidden" name="dir" value="<?php echo htmlspecialchars($sortDirection); ?>">
                <input type="hidden" name="page_size" value="<?php echo $pageSize; ?>">
                <button type="submit" class="search-btn">搜尋</button>
            </form>
            <div class="search-info">
                <span>顯示 <strong><?php echo $startItem; ?></strong>-<strong><?php echo $endItem; ?></strong> 筆，共 <strong><?php echo $result['total']; ?></strong> 筆</span>
            </div>
        </div>

        <!-- Table section -->
        <div class="table-wrapper">
            <?php if (count($result['data']) > 0): ?>
                <table id="dataTable">
                    <thead>
                        <tr>
                            <?php
                            $columns = [
                                'name' => 'Name',
                                'position' => 'Position',
                                'office' => 'Office',
                                'age' => 'Age',
                                'start_date' => 'Start Date'
                            ];
                            
                            foreach ($columns as $colKey => $colLabel):
                                $sortUrl = 'table-backend.php?';
                                $sortParams = [];
                                
                                // Preserve the search parameter
                                if ($search) {
                                    $sortParams[] = 'search=' . urlencode($search);
                                }
                                
                                // Preserve the page size
                                $sortParams[] = 'page_size=' . $pageSize;
                                
                                // Handle sorting logic
                                if ($sortColumn === $colKey) {
                                    if ($sortDirection === 'ASC') {
                                        // Currently ascending; clicking switches to descending
                                        $sortParams[] = 'sort=' . $colKey;
                                        $sortParams[] = 'dir=DESC';
                                        $arrowClass = 'asc';
                                    } else {
                                        // Currently descending; clicking removes sorting
                                        $sortParams[] = 'sort=';
                                        $sortParams[] = 'dir=';
                                        $arrowClass = 'desc';
                                    }
                                } else {
                                    // Clicking another column sets ascending sorting
                                    $sortParams[] = 'sort=' . $colKey;
                                    $sortParams[] = 'dir=ASC';
                                    $arrowClass = '';
                                }
                                
                                $sortParams[] = 'page=1'; // Reset to the first page after sorting
                                $sortUrl .= implode('&', $sortParams);
                                
                                $headerClass = 'sortable';
                                if ($sortColumn === $colKey) {
                                    $headerClass .= ' sorted';
                                }
                            ?>
                                <th class="<?php echo $headerClass; ?>">
                                    <a href="<?php echo $sortUrl; ?>" class="sort-link">
                                        <?php echo htmlspecialchars($colLabel); ?>
                                        <span class="sort-icon <?php echo $arrowClass; ?>">
                                            <?php if ($sortColumn === $colKey): ?>
                                                <?php echo $sortDirection === 'ASC' ? '▲' : '▼'; ?>
                                            <?php endif; ?>
                                        </span>
                                    </a>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['data'] as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['position']); ?></td>
                                <td><?php echo htmlspecialchars($row['office']); ?></td>
                                <td><?php echo htmlspecialchars($row['age']); ?></td>
                                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-results">
                    <p>找不到符合條件的資料</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination controls -->
        <div class="pagination-container">
            <div class="pagination-info">
                <span>每頁顯示：</span>
                <form method="GET" action="table-backend.php" class="page-size-form" style="display: inline;">
                    <select name="page_size" id="pageSizeSelect" class="page-size-select" onchange="this.form.submit()">
                        <?php foreach ($allowedPageSizes as $size): ?>
                            <option value="<?php echo $size; ?>" <?php echo $pageSize == $size ? 'selected' : ''; ?>>
                                <?php echo $size; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Preserve other parameters -->
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortColumn); ?>">
                    <input type="hidden" name="dir" value="<?php echo htmlspecialchars($sortDirection); ?>">
                </form>
            </div>
            <div class="pagination-controls">
                <?php if ($totalPages > 0): ?>
                    <!-- Previous page -->
                    <?php if ($currentPage > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage - 1])); ?>" 
                           class="page-btn">← 上一頁</a>
                    <?php else: ?>
                        <span class="page-btn disabled">← 上一頁</span>
                    <?php endif; ?>
                    
                    <!-- Page numbers -->
                    <div class="page-numbers">
                        <?php
                        $maxVisiblePages = 7;
                        $startPage = 1;
                        $endPage = $totalPages;
                        
                        if ($totalPages > $maxVisiblePages) {
                            if ($currentPage <= 4) {
                                $endPage = $maxVisiblePages;
                            } elseif ($currentPage >= $totalPages - 3) {
                                $startPage = $totalPages - $maxVisiblePages + 1;
                            } else {
                                $startPage = $currentPage - 3;
                                $endPage = $currentPage + 3;
                            }
                        }
                        
                        // First page
                        if ($startPage > 1) {
                            $pageParams = array_merge($_GET, ['page' => 1]);
                            echo '<a href="?' . http_build_query($pageParams) . '" class="page-number">1</a>';
                            if ($startPage > 2) {
                                echo '<span class="page-number dots">...</span>';
                            }
                        }
                        
                        // Middle page numbers
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            $pageParams = array_merge($_GET, ['page' => $i]);
                            $activeClass = ($i == $currentPage) ? 'active' : '';
                            echo '<a href="?' . http_build_query($pageParams) . '" class="page-number ' . $activeClass . '">' . $i . '</a>';
                        }
                        
                        // Last page
                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<span class="page-number dots">...</span>';
                            }
                            $pageParams = array_merge($_GET, ['page' => $totalPages]);
                            echo '<a href="?' . http_build_query($pageParams) . '" class="page-number">' . $totalPages . '</a>';
                        }
                        ?>
                    </div>
                    
                    <!-- Next page -->
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage + 1])); ?>" 
                           class="page-btn">下一頁 →</a>
                    <?php else: ?>
                        <span class="page-btn disabled">下一頁 →</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>


