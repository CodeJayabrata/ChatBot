<?php
header('Content-Type: application/json');

require_once '../includes/config/database.php';
require_once '../includes/functions.php';

$sessionId = getSessionId();
clearConversation($sessionId);

echo json_encode(['success' => true, 'message' => 'Conversation cleared']);
?>