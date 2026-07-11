<?php
/**
 * Clear Chat History API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();

try {
    // Database connection
    $conn = new PDO(
        "mysql:host=localhost;dbname=college_chatbot;charset=utf8mb4",
        "root",
        "",
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        )
    );
    
    // Get session ID
    $sessionId = isset($_SESSION['chat_session_id']) ? $_SESSION['chat_session_id'] : '';
    
    if ($sessionId) {
        // Clear conversation history
        $stmt = $conn->prepare("DELETE FROM conversation_history WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        
        // Clear pending learning requests
        $stmt = $conn->prepare("UPDATE learning_requests SET status = 'ignored' WHERE session_id = ? AND status = 'pending'");
        $stmt->execute([$sessionId]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Conversation cleared'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>