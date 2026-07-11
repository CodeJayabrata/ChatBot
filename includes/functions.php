<?php
session_start();

/**
 * Generate unique session ID for chatbot
 */
function getSessionId() {
    if (!isset($_SESSION['chat_session_id'])) {
        $_SESSION['chat_session_id'] = uniqid('chat_', true);
    }
    return $_SESSION['chat_session_id'];
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Check if text is college-related (for learning)
 */
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
        'seminar', 'conference', 'symposium', 'convocation', 'alumni',
        'robotics', 'cultural', 'technical', 'laboratory', 'infrastructure',
        'mess', 'cafeteria', 'auditorium', 'gym', 'sports', 'medical',
        'transport', 'bus', 'wifi', 'internet', 'computer', 'center',
        'block', 'building', 'wing', 'section', 'hod', 'coordinator',
        'curriculum', 'elective', 'credit', 'grade', 'transcript'
    ];
    
    // Offensive/irrelevant keywords to reject
    $rejectKeywords = [
        'joke', 'funny', 'laugh', 'haha', 'hehe', 'lol',
        'abuse', 'hack', 'password', 'bank', 'account', 'card', 
        'otp', 'pin', 'personal', 'private', 'secret',
        'politics', 'political', 'religion', 'religious', 'god', 'worship',
        'adult', 'xxx', 'sex', 'drug', 'alcohol', 'gamble', 'betting',
        'hate', 'kill', 'fight', 'racist', 'caste', 'abuse',
        'spam', 'advertisement', 'promotion', 'discount', 'offer'
    ];
    
    $textLower = strtolower($text);
    
    // Check minimum length (must be at least 15 characters of actual information)
    if (strlen($text) < 15) {
        return false;
    }
    
    // Check for reject keywords first
    foreach ($rejectKeywords as $keyword) {
        if (strpos($textLower, $keyword) !== false) {
            return false;
        }
    }
    
    // Check for college keywords - at least one must match
    $matchCount = 0;
    foreach ($collegeKeywords as $keyword) {
        if (strpos($textLower, $keyword) !== false) {
            $matchCount++;
        }
    }
    
    // Need at least 2 college-related keywords for valid learning
    return $matchCount >= 2;
}

/**
 * Enhanced search knowledge base with better matching
 */
function searchKnowledgeBase($question) {
    $conn = getDBConnection();
    
    // Clean the question
    $question = trim(strtolower($question));
    
    // STEP 1: Try exact phrase matching
    $stmt = $conn->prepare("SELECT * FROM knowledge_base WHERE LOWER(question) LIKE ? AND status = 'active' LIMIT 1");
    $searchTerm = "%" . $question . "%";
    $stmt->execute([$searchTerm]);
    $result = $stmt->fetch();
    
    if ($result) {
        return $result;
    }
    
    // STEP 2: Extract important words (remove stop words)
    $stopWords = ['what', 'is', 'the', 'a', 'an', 'in', 'on', 'at', 'to', 'for', 
                  'of', 'and', 'or', 'but', 'how', 'when', 'where', 'who', 'why',
                  'can', 'could', 'would', 'should', 'will', 'shall', 'may', 'might',
                  'do', 'does', 'did', 'are', 'am', 'be', 'been', 'being',
                  'tell', 'me', 'about', 'give', 'provide', 'show', 'please', 'thanks'];
    
    $words = explode(' ', $question);
    $importantWords = array_filter($words, function($word) use ($stopWords) {
        return strlen($word) > 2 && !in_array($word, $stopWords);
    });
    
    // If no important words found, use original words
    if (empty($importantWords)) {
        $importantWords = array_filter($words, function($word) {
            return strlen($word) > 2;
        });
    }
    
    // STEP 3: Score-based keyword matching
    $scores = [];
    $stmt = $conn->prepare("SELECT * FROM knowledge_base WHERE status = 'active'");
    $stmt->execute();
    $allResults = $stmt->fetchAll();
    
    foreach ($allResults as $row) {
        $score = 0;
        $keywords = strtolower($row['keywords']);
        $rowQuestion = strtolower($row['question']);
        $category = strtolower($row['category']);
        
        foreach ($importantWords as $word) {
            // Check in keywords
            if (strpos($keywords, $word) !== false) {
                $score += 3;
            }
            // Check in question text
            if (strpos($rowQuestion, $word) !== false) {
                $score += 2;
            }
            // Check in category
            if (strpos($category, $word) !== false) {
                $score += 1;
            }
        }
        
        if ($score > 0) {
            $scores[] = ['row' => $row, 'score' => $score];
        }
    }
    
    // Sort by score (highest first)
    usort($scores, function($a, $b) {
        return $b['score'] - $a['score'];
    });
    
    // Return best match if score is high enough
    if (!empty($scores) && $scores[0]['score'] >= 2) {
        return $scores[0]['row'];
    }
    
    // STEP 4: Try individual word matching as last resort
    foreach ($importantWords as $word) {
        $stmt = $conn->prepare("SELECT * FROM knowledge_base WHERE keywords LIKE ? AND status = 'active' LIMIT 1");
        $stmt->execute(["%" . $word . "%"]);
        $result = $stmt->fetch();
        
        if ($result) {
            return $result;
        }
    }
    
    return null;
}

/**
 * Save to knowledge base
 */
function saveToKnowledgeBase($question, $answer) {
    $conn = getDBConnection();
    
    // Extract category from answer
    $category = detectCategory($question . ' ' . $answer);
    
    // Generate keywords
    $allText = strtolower($question . ' ' . $answer);
    $words = explode(' ', $allText);
    $keywords = [];
    
    foreach ($words as $word) {
        $word = trim(preg_replace('/[^a-zA-Z]/', '', $word));
        if (strlen($word) > 3) {
            $keywords[] = $word;
        }
    }
    
    $keywordsString = implode(',', array_unique($keywords));
    
    // Capitalize first letter of question
    $question = ucfirst(strtolower(trim($question)));
    
    $stmt = $conn->prepare("INSERT INTO knowledge_base (question, answer, category, keywords, created_by, status) VALUES (?, ?, ?, ?, 'user', 'active')");
    $stmt->execute([$question, $answer, $category, $keywordsString]);
    
    return $conn->lastInsertId();
}

/**
 * Detect category from text
 */
function detectCategory($text) {
    $text = strtolower($text);
    
    $categories = [
        'Admission' => ['admission', 'enroll', 'apply', 'entrance', 'merit'],
        'Courses' => ['course', 'program', 'degree', 'btech', 'bba', 'mba'],
        'Fees' => ['fee', 'fees', 'payment', 'cost', 'expense', 'rupees'],
        'Hostel' => ['hostel', 'room', 'accommodation', 'mess'],
        'Library' => ['library', 'book', 'journal'],
        'Examination' => ['exam', 'test', 'assessment', 'semester'],
        'Placement' => ['placement', 'job', 'recruit', 'company', 'package'],
        'Events' => ['event', 'fest', 'cultural', 'technical', 'workshop'],
        'Faculty' => ['teacher', 'professor', 'faculty', 'hod'],
        'Scholarship' => ['scholarship', 'financial', 'merit'],
        'Facilities' => ['facility', 'campus', 'lab', 'infrastructure', 'wifi'],
        'Office' => ['office', 'timing', 'working', 'administrative'],
        'Contact' => ['contact', 'phone', 'email', 'address'],
        'Results' => ['result', 'marks', 'grade', 'score'],
        'Notice' => ['notice', 'announcement', 'circular'],
        'Departments' => ['department', 'branch', 'stream']
    ];
    
    foreach ($categories as $category => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return $category;
            }
        }
    }
    
    return 'General';
}

/**
 * Save conversation to history
 */
function saveConversation($sessionId, $userMessage, $botResponse) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO conversation_history (session_id, user_message, bot_response) VALUES (?, ?, ?)");
    $stmt->execute([$sessionId, $userMessage, $botResponse]);
}

/**
 * Get latest pending learning request
 */
function getPendingLearningRequest($sessionId) {
    $conn = getDBConnection();
    
    // Only get the MOST RECENT pending request
    $stmt = $conn->prepare("SELECT * FROM learning_requests WHERE session_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1");
    $stmt->execute([$sessionId]);
    
    return $stmt->fetch();
}

/**
 * Update learning request status
 */
function updateLearningRequestStatus($requestId, $status, $userResponse = null) {
    $conn = getDBConnection();
    
    if ($userResponse !== null) {
        $stmt = $conn->prepare("UPDATE learning_requests SET status = ?, user_response = ? WHERE id = ?");
        $stmt->execute([$status, $userResponse, $requestId]);
    } else {
        $stmt = $conn->prepare("UPDATE learning_requests SET status = ? WHERE id = ?");
        $stmt->execute([$status, $requestId]);
    }
}

/**
 * Create new learning request
 */
function createLearningRequest($sessionId, $unknownQuestion) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO learning_requests (session_id, unknown_question, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$sessionId, $unknownQuestion]);
    
    return $conn->lastInsertId();
}

/**
 * Clear all pending requests for session
 */
function clearPendingRequests($sessionId) {
    $conn = getDBConnection();
    
    // Mark all pending as ignored
    $stmt = $conn->prepare("UPDATE learning_requests SET status = 'ignored' WHERE session_id = ? AND status = 'pending'");
    $stmt->execute([$sessionId]);
}

/**
 * Clear conversation history
 */
function clearConversation($sessionId) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("DELETE FROM conversation_history WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    
    // Also clear pending learning requests
    clearPendingRequests($sessionId);
}

/**
 * Get recent conversations
 */
function getRecentConversations($sessionId, $limit = 50) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT * FROM conversation_history WHERE session_id = ? ORDER BY created_at ASC LIMIT ?");
    $stmt->execute([$sessionId, $limit]);
    
    return $stmt->fetchAll();
}

/**
 * Check if user message indicates they want to teach
 */
function isTeachingIntent($message) {
    $message = strtolower($message);
    
    // Indicators that user IS teaching
    $teachingIndicators = [
        'it is', 'this is', 'that is', 'they are', 'these are',
        'located', 'situated', 'available', 'provides', 'offers',
        'conducts', 'organizes', 'established', 'founded'
    ];
    
    foreach ($teachingIndicators as $indicator) {
        if (strpos($message, $indicator) !== false && strlen($message) > 20) {
            return true;
        }
    }
    
    return false;
}

/**
 * Check if user message indicates they are NOT teaching
 */
function isNotTeachingIntent($message) {
    $message = strtolower($message);
    
    // Indicators that user is NOT teaching (casual conversation)
    $notTeachingIndicators = [
        'ok', 'okay', 'fine', 'leave', 'forget', 'never mind',
        'no', 'nope', 'nothing', 'whatever', 'cool',
        'hmm', 'umm', 'bye', 'thanks', 'thank you',
        'what about', 'tell me about', 'where is', 'how to',
        'who is', 'when is', 'give me', 'show me', 'i want',
        'can you', 'please', 'help'
    ];
    
    // If message is short (less than 20 chars), likely not teaching
    if (strlen($message) < 20) {
        return true;
    }
    
    // Check if message starts with question words
    $questionStarters = ['what', 'where', 'when', 'who', 'why', 'how', 'can', 'do', 'does', 'is', 'are'];
    $firstWord = explode(' ', trim($message))[0];
    if (in_array($firstWord, $questionStarters)) {
        return true;
    }
    
    foreach ($notTeachingIndicators as $indicator) {
        if (strpos($message, $indicator) !== false) {
            return true;
        }
    }
    
    return false;
}


?>