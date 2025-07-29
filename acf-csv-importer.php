<?php
/*
Plugin Name: ACF CSV Importer - Updating Content
Description: Import ACF field values from a CSV file based on Post ID and Title.
Version: 1.0
Author: Pradeepraj RS
Description: Custom client requirement. Import one ACF field using csv file. We have three fields ID,Title and ACF Field. If ID and Title matches then only content will import otherwise it fails
*/

if (!defined('ABSPATH')) exit;

// Add a custom admin page under Tools menu
add_action('admin_menu', function() {
    add_management_page(
        'ACF CSV Import',
        'ACF CSV Import',
        'manage_options',
        'acf-csv-import',
        'acf_csv_import_page'
    );
});

function acf_csv_import_page() {
    echo '<div class="wrap"><h1>ACF CSV Import</h1>';

    // Check if file exists
    $csv_file = ABSPATH . 'wp-content/uploads/acf-import.csv';

    if (!file_exists($csv_file)) {
        echo '<p style="color:red;">❌ CSV file not found at: <code>/wp-content/uploads/acf-import.csv</code></p>';
        echo '</div>';
        return;
    }

    // Process CSV
    $csv = array_map('str_getcsv', file($csv_file));
    $headers = array_shift($csv);

    $import_count = 0;
    $error_count = 0;

    foreach ($csv as $row) {
        $post_id      = intval($row[0]);
        $title        = sanitize_text_field($row[1]);
        $field_value  = sanitize_text_field($row[2]);

        $post = get_post($post_id);

        if ($post) {
            if ($post->post_title === $title) {
                update_field('q1_2025_ind', $field_value, $post_id);
                echo "<p style='color:green;'>✅ Updated Post ID {$post_id} ('{$title}') with value '{$field_value}'</p>";
                $import_count++;
            } else {
                echo "<p style='color:orange;'>❌ Title mismatch for Post ID {$post_id}. Expected '{$title}', found '{$post->post_title}'</p>";
                $error_count++;
            }
        } else {
            echo "<p style='color:red;'>❌ Post ID {$post_id} not found.</p>";
            $error_count++;
        }
    }

    echo "<h3>✔️ Import Summary:</h3>";
    echo "<ul>
        <li>✅ Imported: {$import_count}</li>
        <li>❌ Issues: {$error_count}</li>
    </ul>";
    echo "<p>Done.</p>";
    echo '</div>';
}
