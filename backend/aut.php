<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password']; // Este ya está encriptado desde el frontend

  
  if ($password === $hashed_password_from_db) {
    echo json_encode(['success' => true, 'message' => 'Login successful']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
  }
}
