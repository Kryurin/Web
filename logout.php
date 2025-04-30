<?php
session_start();
session_unset();  // Removes all session variables
session_destroy();  // Destroys the session

// Redirect to index page after logging out
header('Location: index.php');
exit;
?>
