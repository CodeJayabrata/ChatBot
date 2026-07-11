<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../includes/config/database.php';
require_once '../includes/functions.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$userMessage = sanitizeInput($data['message'] ?? '');
$sessionId = getSessionId();

if (empty($userMessage)) {
    echo json_encode(['error' => 'Message is required']);
    exit;
}

// Check for pending learning request
$pendingRequest = getPendingLearningRequest($sessionId);

if ($pendingRequest) {
    // User is responding to a learning request
    if (isCollegeRelated($userMessage)) {
        // User is teaching the bot
        saveToKnowledgeBase($pendingRequest['unknown_question'], $userMessage);
        updateLearningRequestStatus($pendingRequest['id'], 'learned');
        saveLearningRequest($sessionId, $pendingRequest['unknown_question'], $userMessage, 'learned');
        
        $response = "Thank you! I have learned this information and will use it to answer future questions.";
    } else {
        // Not college-related or user declined
        updateLearningRequestStatus($pendingRequest['id'], 'rejected');
        saveLearningRequest($sessionId, $pendingRequest['unknown_question'], $userMessage, 'rejected');
        
        $response = "I understand. Let me know if you have any other questions about the college.";
    }
    
    saveConversation($sessionId, $userMessage, $response);
    echo json_encode(['response' => $response, 'learned' => false]);
    exit;
}

// Search knowledge base
$result = searchKnowledgeBase($userMessage);

if ($result) {
    // Answer found
    $response = $result['answer'];
    saveConversation($sessionId, $userMessage, $response);
    
    echo json_encode([
        'response' => $response,
        'learned' => false,
        'category' => $result['category']
    ]);
} else {
    // Answer not found - ask if user wants to teach
    $response = "Sorry, I don't have information about that yet. 🤔\n\n";
    $response .= "If you'd like, you can tell me about it. ";
    $response .= "If the information is valid and related to the college, I can learn it and remember it for future conversations.";
    
    // Save learning request as pending
    saveLearningRequest($sessionId, $userMessage, '', 'pending');
    saveConversation($sessionId, $userMessage, $response);
    
    echo json_encode([
        'response' => $response,
        'learned' => false,
        'learning_mode' => true
    ]);
}
?>