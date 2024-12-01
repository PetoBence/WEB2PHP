<?php
function getMenuItems() {
    global $pdo;
    // parent_id alapján minden menu tétel meghivása
    $stmt = $pdo->query("SELECT * FROM menu ORDER BY position");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// funkció a menü rendelerésére 
function renderMenu() {
    $menuItems = getMenuItems();

    // parent_id szerinti rendelés
    $menuTree = [];

    foreach ($menuItems as $item) {
        // ha a parent_id = 0 akkor ez egy "fő" menü tétel 
        if ($item['parent_id'] === NULL) {
            $menuTree[$item['id']] = [
                'name' => $item['name'],
                'link' => $item['link'],
                'children' => [] // üres tömb az almenü tételekhez
            ];
        } else {
            // ha van szülő elem akkor a gyerek elem hozzáadása
            $menuTree[$item['parent_id']]['children'][] = [
                'name' => $item['name'],
                'link' => $item['link']
            ];
        }
    }

    // HTML render
    echo '<ul class="menu">';
    foreach ($menuTree as $parent) {
        echo '<li>';
        echo '<a href="' . htmlspecialchars($parent['link']) . '">' . htmlspecialchars($parent['name']) . '</a>';

        // ellenőrzése hogy vannek-e további gyerek elemek
        if (!empty($parent['children'])) {
            echo '<ul>';
            foreach ($parent['children'] as $child) {
                echo '<li><a href="' . htmlspecialchars($child['link']) . '">' . htmlspecialchars($child['name']) . '</a></li>';
            }
            echo '</ul>';
        }
        echo '</li>';
    }
    echo '</ul>';
}
?>
