<?php
  // If the user is logged in, delete the session vars to log them out
  session_start();
  if (isset($_SESSION['username'])) {
    // Delete the session vars by clearing the $_SESSION array
    $_SESSION = array();

  //destroy created files
  if (file_exists('player1.html') || file_exists('player2.html') || file_exists('remaining_deck.html') || file_exists('court.html') || file_exists('deck.html') || file_exists('update_court.txt')) { //delete prev generated files
    @unlink('player1.html');
    @unlink('player2.html');
    @unlink('court.html');
    @unlink('remaining_deck.html');
    @unlink('deck.html');
    @unlink('who.txt');
    @unlink('chat/log.html');
  }

    // Delete the session cookie by setting its expiration to an hour ago (3600)
    if (isset($_COOKIE[session_name()])) {
      setcookie(session_name(), '', time() - 3600);
    }

    // Destroy the session
    session_destroy();
  }

  // Delete the user ID and username cookies by setting their expirations to an hour ago (3600)
  setcookie('profile', '', time() - 3600);
  setcookie('username', '', time() - 3600);

  // Redirect to the home page
  $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.html';
  header('Location: ' . $home_url);
?>