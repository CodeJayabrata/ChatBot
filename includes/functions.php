<?php
session_start();

// Generate unique session ID for chatbot
function getSessionId() {
    if (!isset($_SESSION['chat_session_id'])) {
        $_SESSION['chat_session_id'] = uniqid('chat_', true);
    }
    return $_SESSION['chat_session_id'];
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if text is college-related
function isCollegeRelated($text) {
    // College-related keywords
    $collegeKeywords = [
        'college', 'university', 'campus', 'student', 'faculty', 'teacher',
        'professor', 'course', 'degree', 'department', 'admission', 'fee',
        'hostel', 'library', 'lab', 'classroom', 'exam', 'result', 'placement',
        'scholarship', 'syllabus', 'semester', 'academic', 'study', 'education',
        'canteen', 'sports', 'event', 'fest', 'club', 'society', 'office',
        'principal', 'director', 'registrar', 'council', 'committee',
        'assignment', 'project', 'internship', 'training', 'workshop',
        'seminar', 'conference', 'symposium', 'convocation', 'alumni'
    ];
    
    // Offensive/irrelevant keywords to reject
    $rejectKeywords = [
        'joke', 'funny', 'laugh', 'abuse', 'hack', 'password', 'bank',
        'account', 'card', 'otp', 'pin', 'personal', 'private', 'secret',
        'politics', 'political', 'religion', 'religious', 'god', 'worship',
        'adult', 'xxx', 'sex', 'drug', 'alcohol', 'gamble', 'betting',
        'hate', 'kill', 'fight', 'racist', 'caste'
    ];
    
    $textLower = strtolower($text);
    
    // Check for reject keywords first
    foreach ($rejectKeywords as $keyword) {
        if (strpos($textLower, $keyword) !== false) {
            return false;
        }
    }
    
    // Check for college keywords
    foreach ($collegeKeywords as $keyword) {
        if (strpos($textLower, $keyword) !== false) {
            return true;
        }
    }
    
    return false;
}

// Search knowledge base
function searchKnowledgeBase($question) {
    $conn = getDBConnection();
    
    // First try exact match
    $stmt = $conn->prepare("SELECT * FROM knowledge_base WHERE question LIKE ? AND status = 'active' LIMIT 1");
    $stmt->execute(["%" . $question . "%"]);
    $result = $stmt->fetch();
    
    if ($result) {
        return $result;
    }
    
    // Then try keyword matching
    $words = explode(' ', strtolower($question));
    $keywords = array_filter($words, function($word) {
        return strlen($word) > 3;
    });
    
    if (!empty($keywords)) {
        $keywordConditions = [];
        $params = [];
        
        foreach ($keywords as $keyword) {
            $keywordConditions[] = "keywords LIKE ?";
            $params[] = "%" . $keyword . "%";
        }
        
        $sql = "SELECT * FROM knowledge_base WHERE status = 'active' AND (" . 
               implode(" OR ", $keywordConditions) . ") LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        if ($result) {
            return $result;
        }
    }
    
    return null;
}

// Save to knowledge base
function saveToKnowledgeBase($question, $answer) {
    $conn = getDBConnection();
    
    // Generate keywords
    $keywords = implode(',', array_filter(explode(' ', strtolower($question)), function($word) {
        return strlen($word) > 3;
    }));
    
    $stmt = $conn->prepare("INSERT INTO knowledge_base (question, answer, category, keywords, created_by) VALUES (?, ?, 'Learned', ?, 'user')");
    $stmt->execute([$question, $answer, $keywords]);
    
    return $conn->lastInsertId();
}

// Save conversation
function saveConversation($sessionId, $userMessage, $botResponse) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO conversation_history (session_id, user_message, bot_response) VALUES (?, ?, ?)");
    $stmt->execute([$sessionId, $userMessage, $botResponse]);
}

// Save learning request
function saveLearningRequest($sessionId, $unknownQuestion, $userResponse, $status) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO learning_requests (session_id, unknown_question, user_response, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$sessionId, $unknownQuestion, $userResponse, $status]);
}

// Check pending learning request
function getPendingLearningRequest($sessionId) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT * FROM learning_requests WHERE session_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1");
    $stmt->execute([$sessionId]);
    
    return $stmt->fetch();
}

// Update learning request status
function updateLearningRequestStatus($requestId, $status) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("UPDATE learning_requests SET status = ? WHERE id = ?");
    $stmt->execute([$status, $requestId]);
}

// Clear conversation history
function clearConversation($sessionId) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("DELETE FROM conversation_history WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    
    $stmt = $conn->prepare("DELETE FROM learning_requests WHERE session_id = ? AND status = 'pending'");
    $stmt->execute([$sessionId]);
}

// Get recent conversations
function getRecentConversations($sessionId, $limit = 50) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT * FROM conversation_history WHERE session_id = ? ORDER BY created_at ASC LIMIT ?");
    $stmt->execute([$sessionId, $limit]);
    
    return $stmt->fetchAll();
}
?>