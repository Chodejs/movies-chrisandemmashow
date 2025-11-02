<?php
// ---
// This is our simple, secure backend API.
// Its only job is to get the correct movies from the DB
// based on the current date and send them as JSON.
// ---

// 1. Set the content type to JSON
header('Content-Type: application/json');

// ---
// !!! IMPORTANT, CHRIS !!!
// You MUST fill in your database details here.
// ---
$servername = "localhost";        // Or your remote DB host
$username = "root";   // Your MySQL username
$password = "mysql";   // Your MySQL password
$dbname = "holiday_marathon";         // The name of your database
// ---

// 2. Set the timezone so the date logic is correct
// (We're in Ohio, so America/New_York)
date_default_timezone_set('America/New_York');

$currentMonth = (int)date('m');
$currentDay = (int)date('d');

$movies = [];

// 3. Only bother connecting to the DB if it's December
// We use '12' for December.
// (For testing, you can temporarily change this to your current month, e.g., '10' for October)
if ($currentMonth == 11) { // <-- THE FIX IS HERE!
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        // Send back an error in JSON format
        echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
        exit();
    }

    // 4. Prepare a secure SQL statement
    // We select all movies where the 'day' is less than or equal to the current day.
    // This is our "Advent Calendar" logic!
    $sql = "SELECT day, movie_title, tmdb_id, alt_movie_title, alt_tmdb_id, picked_by, streaming_notes 
            FROM holiday_marathon 
            WHERE day <= ?";

    $stmt = $conn->prepare($sql);
    
    // 'i' means we are binding an integer
    $stmt->bind_param("i", $currentDay);

    // 5. Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // 6. Fetch all matching movies into our array
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
    }

    // 7. Clean up
    $stmt->close();
    $conn->close();
}
// If it's not December, this script will just output an empty array,
// which is correct. No days will be "unlocked".

// 8. Send the final JSON data back to our JavaScript
echo json_encode($movies);

?>

