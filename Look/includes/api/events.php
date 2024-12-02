<?php
header('Content-Type: application/json');
include '../db.php';

function getEvents() {
    global $conn;
    
    $sql = "SELECT e.*, 
            GROUP_CONCAT(DISTINCT et.tag_name) as tags
            FROM events e
            LEFT JOIN event_tags et ON e.event_id = et.event_id
            WHERE e.event_date >= CURDATE()
            GROUP BY e.event_id
            ORDER BY e.event_date ASC";
            
    $result = $conn->query($sql);
    
    if (!$result) {
        return ['error' => $conn->error];
    }
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $row['tags'] = $row['tags'] ? explode(',', $row['tags']) : [];
        $events[] = $row;
    }
    
    return $events;
}

echo json_encode(getEvents());
?>