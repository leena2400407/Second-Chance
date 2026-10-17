<?php
/**
 * Fair Price Suggestion Module (AI / Statistical Heuristic)
 * Second Life / Second Chance Marketplace
 * 
 * Uses completed university transactions (median / IQR price range)
 * to recommend a fair price when a student lists an item.
 */

require_once __DIR__ . '/db.php';

function getFairPriceSuggestion($conn, $itemName, $category, $condition = null) {
    $cleanName     = mysqli_real_escape_string($conn, trim($itemName));
    $cleanCategory = mysqli_real_escape_string($conn, trim($category));
    $cleanCondition = $condition ? mysqli_real_escape_string($conn, trim($condition)) : null;

    // Search for comparable sales by keywords or exact name
    $keywords = preg_split('/\s+/', $cleanName);
    $keywordConditions = [];
    foreach ($keywords as $kw) {
        if (strlen($kw) >= 3) {
            $keywordConditions[] = "item_name LIKE '%" . mysqli_real_escape_string($conn, $kw) . "%'";
        }
    }

    $whereClause = "";
    if (!empty($keywordConditions)) {
        $whereClause = "(" . implode(" OR ", $keywordConditions) . ")";
    } else {
        $whereClause = "item_name LIKE '%$cleanName%'";
    }

    if (!empty($cleanCategory)) {
        $whereClause .= " AND (category LIKE '%$cleanCategory%' OR category = '$cleanCategory')";
    }

    if ($cleanCondition) {
        $whereClause .= " AND condition_state = '$cleanCondition'";
    }

    $sql = "SELECT * FROM completed_sales WHERE $whereClause ORDER BY sale_price_egp ASC";
    $result = mysqli_query($conn, $sql);

    $prices = [];
    $records = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $prices[] = (float)$row['sale_price_egp'];
            $records[] = $row;
        }
    }

    // If condition filter yielded no results, broaden search to just name & category
    if (count($prices) < 2 && $cleanCondition) {
        $broadSql = "SELECT * FROM completed_sales WHERE (" . implode(" OR ", $keywordConditions) . ") ORDER BY sale_price_egp ASC";
        $broadRes = mysqli_query($conn, $broadSql);
        if ($broadRes) {
            $prices = [];
            $records = [];
            while ($row = mysqli_fetch_assoc($broadRes)) {
                $prices[] = (float)$row['sale_price_egp'];
                $records[] = $row;
            }
        }
    }

    $count = count($prices);
    if ($count < 2) {
        return [
            'status'        => 'insufficient_data',
            'count'         => $count,
            'explanation'   => "Not enough recent sales history for '$itemName' to give a confident estimate. You may set your desired asking price.",
            'suggested_min' => null,
            'suggested_max' => null,
            'median'        => null,
            'sales'         => $records
        ];
    }

    // Compute Median
    sort($prices);
    $mid = floor(($count - 1) / 2);
    if ($count % 2) {
        $median = $prices[$mid];
    } else {
        $median = ($prices[$mid] + $prices[$mid + 1]) / 2.0;
    }

    $minPrice = $prices[0];
    $maxPrice = $prices[$count - 1];

    return [
        'status'        => 'success',
        'count'         => $count,
        'suggested_min' => round($minPrice),
        'suggested_max' => round($maxPrice),
        'median'        => round($median),
        'currency'      => 'EGP',
        'explanation'   => "Based on $count similar " . htmlspecialchars($itemName) . " listings completed recently on campus.",
        'sales'         => $records
    ];
}
?>

