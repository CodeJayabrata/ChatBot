<?php
/**
 * Gangarampur College Chatbot API
 * All edge cases handled - Bot corrections, casual responses, proper learning
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================
// DATABASE CONNECTION
// ============================================
function getDBConnection() {
    static $conn = null;
    if ($conn === null) {
        try {
            $conn = new PDO(
                "mysql:host=localhost;dbname=college_chatbot;charset=utf8mb4",
                "root",
                "",
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    return $conn;
}

// ============================================
// SESSION MANAGEMENT
// ============================================
function getSessionId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['chat_session_id'])) {
        $_SESSION['chat_session_id'] = 'chat_' . uniqid() . '_' . bin2hex(random_bytes(4));
    }
    return $_SESSION['chat_session_id'];
}

// ============================================
// SANITIZE INPUT
// ============================================
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// ============================================
// DETECT MESSAGE TYPE - COMPREHENSIVE
// ============================================
function detectMessageType($message) {
    $message = strtolower(trim($message));
    
        // ============================================
    // IDENTITY QUESTIONS
    // ============================================
    $identityPatterns = [
        // Basic name questions
        'your name', 'what is your name', 'whats your name',
        "what's your name", 'what are you called',
        'what do they call you', 'what is ur name',
        'whats ur name', 'may i know your name',
        'can i know your name', 'tell me your name',
        'ur name', 'your good name', 'name please',
        'what should i call you', 'what do i call you',
        'what can i call you', 'how should i address you',
        
        // Who are you
        'who are you', 'who r u', 'who u', 'who is you',
        'who exactly are you', 'who might you be',
        'who am i speaking to', 'who am i talking to',
        'who is this', 'who is it', 'whos this',
        'who are u', 'who r ya',
        
        // What are you
        'what are you', 'what r u', 'what u r',
        'what exactly are you', 'what kind of bot are you',
        'what type of bot', 'what type of ai',
        
        // Tell me about yourself
        'tell me about yourself', 'tell me about u',
        'tell me about you', 'introduce yourself',
        'introduce urself', 'introduction please',
        'give your introduction', 'describe yourself',
        'explain yourself', 'tell about yourself',
        'who exactly are you', 'what exactly are you',
        
        // Bot/Human questions
        'are you a bot', 'are u a bot', 'you are a bot',
        'are you human', 'are u human', 'you human',
        'are you real', 'are u real', 'is this a bot',
        'am i talking to a bot', 'am i talking to a human',
        'are you ai', 'are you artificial intelligence',
        'are you a robot', 'are you real person',
        
        // Identity/self-awareness
        'do you know yourself', 'do u know yourself',
        'dont you know yourself', 'don\'t you know yourself',
        'do you have a name', 'do u have a name',
        'your identity', 'what is your identity',
        'whats your identity', 'identify yourself',
        
        // Self-reference
        'what is you', 'what is ur', 'about you',
        'about yourself', 'about u', 'about urself',
        'ur details', 'your details', 'your info',
        'your information', 'bot details',
        
        // Bengali
        'tumi ke', 'apni ke', 'tomar naam ki',
        'apnar naam ki', 'tumi ki', 'apni ki',
        'tomar porichoy', 'apnar porichoy',
        'tumi ki rokom', 'apni ki rokom',
        
        // Hindi
        'tum kaun ho', 'aap kaun ho', 'tumhara naam kya hai',
        'aapka naam kya hai', 'tum kya ho', 'aap kya ho',
        'tumhari pehchan', 'aapki pehchan',
        
        // Other variations
        'what do you call yourself', 'whats ur identity',
        'tell me who you are', 'tell me what you are',
        'i want to know about you', 'i wanna know about you',
        'let me know about you', 'who developed you',
        'who made you', 'who created you',
        'who is your creator', 'who programmed you'
    ];
    
    foreach ($identityPatterns as $pattern) {
        if (strpos($message, $pattern) !== false) {
            return 'identity';
        }
    }
    
        // ============================================
    // BOT CORRECTION / COMPLAINTS
    // ============================================
    $botCorrectionPatterns = [
        // Why don't you know
        'why you dont know', 'why dont you know', "why you don't know",
        'why u dont know', "why u don't know", 'why you not know',
        'how come you dont know', 'how come u dont know',
        'why didnt you know', "why didn't you know",
        
        // You should/must know
        'you should know', 'you must know', 'u should know',
        'you ought to know', 'you need to know',
        
        // Wrong/incorrect
        'you are wrong', 'youre wrong', "you're wrong",
        'u are wrong', 'ur wrong', 'that is wrong',
        'this is wrong', 'thats wrong', "that's wrong",
        'wrong information', 'wrong answer', 'wrong info',
        'incorrect', 'incorrect information', 'incorrect answer',
        'not correct', 'not right', 'thats not correct',
        "that's not correct", 'this is not correct',
        'that is not correct', 'thats not right',
        
        // Mistake
        'mistake', 'you made a mistake', 'that is a mistake',
        'you are mistaken', 'youre mistaken', "you're mistaken",
        'you learned wrong', 'u learned wrong',
        'you learnt wrong', 'u learnt wrong',
        
        // Sorry + wrong
        'sorry it is the wrong', 'sorry thats wrong',
        "sorry that's wrong", 'sorry this is wrong',
        'sorry wrong', 'sorry incorrect',
        
        // Don't understand
        'you dont understand', "you don't understand",
        'u dont understand', 'you are not understanding',
        'youre not understanding', "you're not understanding",
        'you not understanding', 'you didnt understand',
        "you didn't understand", 'u didnt understand',
        
        // What understand
        'what understand', 'what do you understand',
        'what u understand', 'what you understand',
        'what did you understand', 'what have you understood',
        
        // You don't know
        'you dont know', "you don't know", 'u dont know',
        "u don't know", 'you do not know', 'u do not know',
        'how you dont know', 'how u dont know',
        'how come u dont know', 'how is it you dont know',
        
        // Bad/wrong responses
        'bad answer', 'bad response', 'not helpful',
        'that was not helpful', 'this is not helpful',
        'not what i asked', 'not what i was asking',
        'thats not what i asked', "that's not what i asked",
        'you missed the point', 'you dont get it',
        "you don't get it", 'u dont get it',
        
        // Frustration expressions
        'useless', 'no use', 'not useful', 'waste',
        'waste of time', 'stupid bot', 'dumb bot',
        'bad bot', 'not good', 'disappointing'
    ];
    
    foreach ($botCorrectionPatterns as $pattern) {
        if (strpos($message, $pattern) !== false) {
            return 'bot_correction';
        }
    }
    
        // ============================================
    // CASUAL ACKNOWLEDGMENTS - EXPANDED
    // ============================================
    $casualPatterns = [
        'tell', 'tell me', 'i want', 'i need',
        'give', 'show me', 'list', 'details',
        'info', 'information', 'help me',
        
        // Basic acknowledgments
        'ok', 'okay', 'okk', 'okey', 'ok bye', 'okay bye',
        'fine', 'alright', 'hmm', 'umm', 'oh', 'ah',
        'i see', 'got it', 'understood', 'nice', 'cool',
        'good', 'great', 'awesome', 'wow', 'acha', 'accha',
        
        // Bengali/Hindi casual
        'thik ache', 'thik hai', 'haan', 'ha', 'hmm',
        
        // Negative casual
        'no', 'nope', 'na', 'nah', 'not really', 'nothing',
        'never mind', 'nevermind', 'leave it', 'forget it',
        'doesnt matter', 'doesn\'t matter', 'no thanks',
        
        // Positive casual
        'yes', 'yeah', 'yep', 'ya', 'yah', 'yup', 'yea',
        'sure', 'of course', 'definitely', 'absolutely',
        'right', 'correct', 'exactly', 'indeed',
        
        // Neutral
        'maybe', 'perhaps', 'possibly', 'idk', 'i dont know',
        "i don't know", 'not sure', 'dunno', 'whatever',
        'anyway', 'anyways', 'so', 'well', 'then',
        
        // Expressions
        'lol', 'lmao', 'haha', 'hehe', 'nice one',
        'interesting', 'really', 'seriously', 'oh ok',
        'oh okay', 'oh wow', 'ohh', 'ahh', 'huh',
        
        // Common short replies
        'k', 'kk', 'ook', 'okie', 'okies', 'alrighty',
        'noted', 'done', 'will do', 'sounds good',
        'makes sense', 'fair enough', 'i understand',
        
        // Polite casual
        'please', 'kindly', 'no problem', 'np',
        'its okay', 'it\'s okay', 'thats fine', 'that\'s fine',
        'all good', 'no worries', 'dont worry', 'don\'t worry'
    ];
    
    foreach ($casualPatterns as $pattern) {
        if ($message === $pattern) {
            return 'casual';
        }
    }
    
    // Check for standalone words that need more context
    if ($message === 'why' || $message === 'why?' || 
        $message === 'what' || $message === 'what?' ||
        $message === 'how' || $message === 'how?' ||
        $message === 'tell' || $message === 'tell me' ||
        $message === 'i want' || $message === 'i need' ||
        $message === 'give' || $message === 'show' ||
        $message === 'list' || $message === 'details') {
        return 'casual';
    }
    
        // ============================================
    // GREETINGS
    // ============================================
    $greetings = [
        // Basic English
        'hi', 'hello', 'hey', 'heya', 'hii', 'hiii', 'helo', 'hlo',
        'hi there', 'hello there', 'hey there', 'heya there',
        'hiya', 'hallo', 'hullo', 'helloo', 'hiee',
        
        // Time-based
        'good morning', 'good afternoon', 'good evening',
        'good day', 'good morn', 'good eve', 'morning',
        'evening', 'afternoon', 'gud morning', 'gud evening',
        'gm', 'ge', 'ga', 'gd morning', 'gd evening',
        
        // Casual/slang
        'greetings', 'sup', 'yo', 'howdy', 'hola',
        'whats up', "what's up", 'wassup', 'whassup',
        'what up', 'waddup', 'how r u', 'how are you',
        'how u doing', 'how you doing', 'howdy do',
        'whats good', "what's good", 'whats new',
        "what's new", 'whats happening', "what's happening",
        'how goes it', 'how is it going', 'hows it going',
        "how's it going", 'how are things', 'hows things',
        "how's things", 'how are ya', 'hows you', "how's you",
        
        // Indian greetings
        'namaste', 'namaskar', 'namaskaar', 'namashkar',
        'pranam', 'pranaam', 'adaab', 'aadaab',
        'assalamualaikum', 'assalamu alaikum', 'assalam alaikum',
        'salaam', 'salaam alaikum', 'walaikum assalam',
        'ram ram', 'jai shree ram', 'jai shri ram',
        'radhe radhe', 'jai jinendra', 'sat sri akaal',
        'sasrikal', 'vanakkam', 'namaskaram',
        
        // Bengali
        'nomoshkar', 'nomoskar', 'nomoshkaar',
        'assalamualaikum', 'adaab',
        'shuvo sokal', 'shuvo sandhya', 'shuvo ratri',
        'subho sokal', 'subho sandhya',
        'kemon acho', 'kemon achho', 'kemon achhen',
        'ki obostha', 'ki khobor',
        
        // Hindi
        'kaise ho', 'kaise hain', 'kya haal hai',
        'kaise hai aap', 'aur batao', 'aur bataiye',
        'kya chal raha hai', 'kya ho raha hai',
        
        // Other languages
        'bonjour', 'ciao', 'salut', 'ola',
        'konnichiwa', 'annyeong', 'nihao',
        
        // Returning user
        'i am back', 'im back', "i'm back",
        'back again', 'here again', 'me again',
        'its me again', "it's me again", 'im here',
        "i'm here", 'i have a question', 'i need help',
        'can you help', 'help me', 'i need information',
        
        // Polite openings
        'excuse me', 'pardon me', 'may i ask',
        'can i ask', 'i want to ask', 'i would like to ask',
        'i wanna ask', 'i have a query', 'i have a doubt',
        'i need to know', 'i want to know',
        
        // Very short
        'hiiii', 'heyyy', 'hiii', 'heyy', 'hlooo',
        'hlw', 'hlo sir', 'hlo mam', 'hi sir', 'hi mam',
        'hello sir', 'hello mam', 'hey sir', 'hey mam'
    ];
    
    foreach ($greetings as $greeting) {
        if ($message === $greeting || strpos($message, $greeting) === 0) {
            return 'greeting';
        }
    }
    
        // ============================================
    // GRATITUDE
    // ============================================
    $gratitude = [
        // Basic thanks
        'thanks', 'thank you', 'thx', 'thanku', 'thank', 'ty',
        'thank you so much', 'thanks a lot', 'much appreciated',
        'thankyou', 'thank u', 'thnx', 'thanx', 'thnks',
        'thankss', 'thankyouu', 'thank youu',
        
        // Appreciation phrases
        'much thanks', 'many thanks', 'lots of thanks',
        'a lot of thanks', 'tons of thanks', 'big thanks',
        'huge thanks', 'sincere thanks', 'heartfelt thanks',
        'grateful', 'very grateful', 'so grateful',
        'appreciate it', 'appreciated', 'i appreciate it',
        'i appreciate that', 'really appreciate it',
        'much appreciated', 'greatly appreciated',
        'very much appreciated', 'highly appreciated',
        
        // Informal/casual
        'cheers', 'ta', 'cheers mate', 'cheers bro',
        'nice one', 'good one', 'brilliant', 'awesome thanks',
        'cool thanks', 'ok thanks', 'okay thanks',
        'alright thanks', 'thanks mate', 'thanks bro',
        'thanks dude', 'thanks buddy', 'thanks dear',
        'thanks friend', 'thanks boss', 'thanks sir',
        'thanks mam', 'thanks maam', 'thanks madam',
        
        // Extended gratitude
        'thank you very much', 'thank u very much',
        'thank you soooo much', 'thank u so much',
        'thank you kindly', 'thank u kindly',
        'i thank you', 'i owe you', 'i owe you one',
        'cant thank you enough', "can't thank you enough",
        'cannot thank you enough', 'no words to thank',
        
        // Bengali
        'dhanyabad', 'dhonnobad', 'dhanyobaad',
        'dhonnobaad', 'dhonnobad', 'anek dhanyabad',
        'onek dhanyabad', 'anek dhonnobad', 'onek dhonnobad',
        'tomake dhanyabad', 'apnake dhanyabad',
        'khub dhanyabad', 'khub dhonnobad',
        
        // Hindi
        'dhanyavad', 'dhanyawaad', 'dhanyavaad',
        'shukriya', 'shukriyaa', 'shukria',
        'bahut shukriya', 'bahut dhanyavad',
        'aapka shukriya', 'tumhara shukriya',
        'aapka dhanyavad', 'dhanyavad aapka',
        
        // Other Indian languages
        'nandri', 'nanni', 'krutagnata',
        'abhari', 'abhaari', 'aabhari',
        
        // Religious/cultural
        'god bless you', 'god bless', 'bless you',
        'jai ho', 'sukriya', 'meherbani',
        'aap ki meherbani', 'aapki meherbani',
        
        // Polite closures with thanks
        'thanks and regards', 'thanks n regards',
        'thanks with regards', 'thank you and regards',
        'thanks bye', 'thank you bye', 'thanks and bye',
        'thank you and bye', 'ok thanks bye',
        'okay thanks bye', 'alright thanks bye',
        
        // Very short
        'tq', 'tq so much', 'tysm', 'tyvm',
        'thks', 'thnq', 'thnku', 'thnks',
        '10q', '10x', 'thk u', 'tk u'
    ];
    
    foreach ($gratitude as $thank) {
        if (strpos($message, $thank) !== false) {
            // Check if it's "no thanks" (declining)
            if (strpos($message, 'no thanks') !== false || 
                strpos($message, 'no thank') !== false) {
                return 'decline';
            }
            return 'gratitude';
        }
    }
    
        // ============================================
    // GOODBYES
    // ============================================
    $goodbyes = [
        // Basic English
        'bye', 'goodbye', 'good bye', 'byee', 'byeee',
        'by', 'bbye', 'byebye', 'bye bye', 'bai',
        
        // See you variations
        'see you', 'see ya', 'see yaa', 'see u', 'see youu',
        'see you later', 'see ya later', 'see u later',
        'see you soon', 'see ya soon', 'see u soon',
        'see you tomorrow', 'see ya tomorrow',
        'see you next time', 'catch you later',
        'catch ya later', 'catch u later',
        'talk to you later', 'ttyl', 'talk later',
        
        // Good night / time-based
        'good night', 'goodnight', 'good nite',
        'gn', 'gdnite', 'gud night', 'gud nite',
        'night', 'nite', 'nighty night',
        'good day', 'have a good day', 'have a great day',
        'have a nice day', 'good day ahead',
        'good morning', 'good afternoon', 'good evening',
        
        // Casual/slang
        'cya', 'cyaa', 'cya later', 'cyaaa',
        'tata', 'ta ta', 'tata bye bye', 'tatabyebye',
        'farewell', 'fare well', 'adios', 'adieu',
        'cheerio', 'toodleoo', 'toodles',
        'peace', 'peace out', 'im out', "i'm out",
        'im off', "i'm off", 'gotta go', 'got to go',
        'gtg', 'g2g', 'i have to go', 'i need to go',
        'i must go', 'i should go', 'i better go',
        
        // Take care variations
        'take care', 'take cares', 'takecare', 'tc',
        'take care of yourself', 'take care bye',
        'stay safe', 'be safe', 'stay well',
        
        // Ok/Alright + bye
        'ok bye', 'okay bye', 'okk bye', 'ok byee',
        'alright bye', 'fine bye', 'then bye',
        'bye then', 'bye for now', 'bye bye take care',
        'ok then bye', 'okay then bye',
        'ok take care', 'okay take care',
        'alright then', 'ok then', 'okay then',
        
        // Bengali
        'biday', 'bidaay', 'bidaye', 'bhalo thako',
        'bhalo theko', 'bhalo thakben', 'bhalo thakun',
        'achha biday', 'accha biday', 'ta ta',
        'aschi', 'aaschi', 'jai', 'jaai',
        'pore dekha hobe', 'abar dekha hobe',
        'abar hobe', 'porobortite dekha hobe',
        'allah hafez', 'allah hafiz', 'khoda hafez',
        'khuda hafez', 'allaha hafez',
        
        // Hindi
        'alvida', 'alwida', 'namaste', 'phir milenge',
        'fir milenge', 'phir milte hain', 'fir milte hain',
        'achha chalta hun', 'accha chalta hun',
        'achha chalti hun', 'accha chalti hun',
        'phir baat karte hain', 'fir baat karte hain',
        'jai hind', 'vande mataram',
        
        // Other Indian
        'poitu varen', 'poitu varen', 'meet you later',
        
        // Professional/polite
        'have a nice day', 'have a good one',
        'have a wonderful day', 'have a pleasant day',
        'have a great evening', 'enjoy your day',
        'until next time', 'till next time',
        'until we meet again', 'till we meet again',
        'it was nice talking', 'nice talking to you',
        'pleasure talking', 'good talking to you',
        'thanks for your help', 'thank you for helping',
        
        // Very short
        'b', 'byy', 'byeee', 'bbye', 'bby',
        'cyaaa', 'ttyl', 'gtg', 'g2g',
        'ok b', 'ok by', 'ok byy', 'by by',
        'chalo', 'chalo bye', 'chal bye',
        'done', 'done bye', 'done for now',
        
        // Sleep related
        'going to sleep', 'gonna sleep', 'time to sleep',
        'bed time', 'off to bed', 'hitting the bed',
        'calling it a night', 'signing off', 'logging off'
    ];
    
    foreach ($goodbyes as $goodbye) {
        if ($message === $goodbye || strpos($message, $goodbye) === 0) {
            return 'goodbye';
        }
    }
    
    // Check if message contains goodbye words and is short
    foreach (['bye', 'goodbye', 'byee'] as $word) {
        if (strpos($message, $word) !== false && strlen($message) < 10) {
            return 'goodbye';
        }
    }
    
        // ============================================
    // CAPABILITY QUESTIONS
    // ============================================
    $capabilityPatterns = [
        // What can you do
        'what can you do', 'what do you do', 'what all can you do',
        'what can u do', 'what do u do', 'what you can do',
        'what you do', 'what is it you do', 'what exactly can you do',
        'what kind of things can you do', 'what sort of things can you do',
        'what are you capable of', 'whats your capability',
        "what's your capability", 'what is your capability',
        
        // How can you help
        'how can you help', 'how do you help', 'how u help',
        'how can u help', 'how you help', 'how are you helpful',
        'how can you assist', 'how do you assist',
        'how can you help me', 'how do you help me',
        'what help can you give', 'what kind of help',
        'what type of help', 'what sort of help',
        'how are you useful', 'how useful are you',
        
        // Features/Capabilities
        'your capabilities', 'your features', 'ur capabilities',
        'ur features', 'what are your features',
        'what are your capabilities', 'what features do you have',
        'what capabilities do you have', 'list your features',
        'list your capabilities', 'tell me your features',
        'tell me your capabilities', 'show me what you can do',
        'show your features', 'show your capabilities',
        
        // What questions can I ask
        'what can i ask', 'what can i ask you',
        'what questions can i ask', 'what type of questions',
        'what kind of questions', 'what sort of questions',
        'what should i ask', 'what to ask',
        'what topics can i ask', 'what things can i ask',
        'what subjects can i ask', 'what can you answer',
        'what do you know', 'what do u know',
        'what all do you know', 'what information do you have',
        'what kind of information', 'what type of information',
        'what knowledge do you have', 'whats your knowledge',
        
        // What can you tell me
        'what can you tell me', 'what can u tell me',
        'what can you tell', 'what all can you tell',
        'what can you inform', 'what information can you give',
        'what info can you provide', 'what details can you give',
        
        // How to use
        'how to use', 'how to use you', 'how do i use you',
        'how do i use this', 'how to use this bot',
        'how to operate', 'how does this work',
        'how do you work', 'how you work',
        'how do u work', 'how this works',
        'how does it work', 'how it works',
        
        // What are you for
        'what are you for', 'what are u for',
        'what is this for', 'whats this for',
        "what's this for", 'what is this bot for',
        'whats this bot for', 'purpose of this bot',
        'what is your purpose', 'whats your purpose',
        "what's your purpose", 'why do you exist',
        'why were you created', 'why are you here',
        'what is your function', 'whats your function',
        
        // Area of expertise
        'what is your area', 'what areas do you cover',
        'what topics do you cover', 'what subjects do you cover',
        'what domains do you cover', 'your domain',
        'your area of expertise', 'your expertise',
        'what are you good at', 'what are u good at',
        'what can you handle', 'what can u handle',
        
        // Commands/Instructions
        'what commands can i use', 'what are the commands',
        'list commands', 'show commands',
        'available commands', 'what can i type',
        'what should i type', 'what to type',
        'give me instructions', 'how to interact',
        'how to talk to you', 'how should i ask',
        'how to ask questions', 'how to ask you',
        
        // Bengali
        'ki ki paro', 'ki ki jano', 'tumi ki ki korte paro',
        'tumi ki ki paro', 'apni ki ki paren',
        'tumi ki ki jano', 'apni ki ki janen',
        'tomar ki ki kaj', 'apnar ki ki kaj',
        'tumi kibhabe help korte paro',
        'apni kibhabe help korte paren',
        'tumi ki ki bolte paro', 'apni ki ki bolte paren',
        'tomar capability ki', 'apnar capability ki',
        
        // Hindi
        'tum kya kya kar sakte ho', 'aap kya kya kar sakte hain',
        'tum kya kar sakte ho', 'aap kya kar sakte hain',
        'tumhari capabilities kya hain', 'aapki capabilities kya hain',
        'tum kya jante ho', 'aap kya jante hain',
        'tum kya bata sakte ho', 'aap kya bata sakte hain',
        'tumhare features kya hain', 'aapke features kya hain',
        'tum kaise help kar sakte ho', 'aap kaise help kar sakte hain',
        'tum kaise use karein', 'aap kaise use karein',
        
        // Short questions
        'help', 'features', 'capabilities',
        'functions', 'what now', 'options',
        'menu', 'services', 'whats available',
        "what's available", 'what you got',
        'what u got', 'whatcha got',
        'what can ya do', 'what can ya tell me'
    ];
    
    foreach ($capabilityPatterns as $pattern) {
        if (strpos($message, $pattern) !== false) {
            return 'capability';
        }
    }
    
    return 'query';
}

// ============================================
// SEARCH KNOWLEDGE BASE - IMPROVED SCORING
// ============================================
function searchKnowledgeBase($question) {
    $conn = getDBConnection();
    $question = strtolower(trim($question));
    
    // Handle "next event" or "upcoming event"
    if ((strpos($question, 'next') !== false || strpos($question, 'upcoming') !== false) && 
        strpos($question, 'event') !== false) {
        $stmt = $conn->prepare("SELECT * FROM knowledge_base WHERE keywords LIKE '%event%' AND status = 'active' LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result) return $result;
    }
    
    // Handle "student number" or "personal number" - privacy
    if (strpos($question, 'student') !== false && 
        (strpos($question, 'number') !== false || strpos($question, 'phone') !== false)) {
        return ['answer' => 'We cannot share personal phone numbers of students due to privacy reasons. For any student-related matters, please contact the college office at 35212 91074 or email ticgmpcollege@gmail.com.', 'category' => 'Privacy'];
    }
    
    // Don't search for identity questions
    if (strpos($question, 'your name') !== false || strpos($question, 'who are you') !== false) {
        return null;
    }
    
    // Don't search for very short standalone words
    $words_check = explode(' ', $question);
    if (count($words_check) === 1 && strlen($question) <= 3) {
        return null;
    }
    
    // Try exact phrase match first
    $stmt = $conn->prepare("SELECT * FROM knowledge_base WHERE LOWER(question) LIKE ? AND status = 'active' LIMIT 1");
    $stmt->execute(["%" . $question . "%"]);
    $result = $stmt->fetch();
    if ($result) return $result;
    
    // Extract meaningful words
    $stopWords = [
        'what', 'is', 'the', 'a', 'an', 'in', 'on', 'at', 'to', 'for', 
        'of', 'and', 'or', 'but', 'how', 'when', 'where', 'who', 'why',
        'can', 'could', 'would', 'should', 'will', 'shall', 'may', 'might',
        'do', 'does', 'did', 'are', 'am', 'be', 'been', 'being',
        'tell', 'me', 'about', 'give', 'provide', 'show', 'please',
        'i', 'you', 'we', 'they', 'it', 'its', 'my', 'your', 'our',
        'dont', 'know', 'yourself', 'myself', 'next', 'upcoming',
        'this', 'that', 'not', 'have', 'has', 'had', 'get',
        'there', 'are', 'any', 'some', 'does', 'how', 'much',
        'many', 'cost', 'fee', 'fees', 'available', 'college',
        'record', 'campus', 'information', 'details', 'tell'
    ];
    
    $words = explode(' ', $question);
    $importantWords = [];
    
    foreach ($words as $word) {
        $word = trim(preg_replace('/[^a-z0-9]/', '', $word));
        if (strlen($word) > 2 && !in_array($word, $stopWords)) {
            $importantWords[] = $word;
        }
    }
    
    // If no important words after filtering, return null
    if (empty($importantWords)) {
        return null;
    }
    
    // Get all entries for scoring
    $stmt = $conn->prepare("SELECT * FROM knowledge_base WHERE status = 'active'");
    $stmt->execute();
    $allEntries = $stmt->fetchAll();
    
    // Score-based matching
    $bestMatch = null;
    $bestScore = 0;
    
    foreach ($allEntries as $entry) {
        $score = 0;
        $matchCount = 0;
        $entryKeywords = strtolower($entry['keywords']);
        $entryQuestion = strtolower($entry['question']);
        
        foreach ($importantWords as $word) {
            // Exact keyword match (highest weight)
            $keywordWords = explode(',', $entryKeywords);
            foreach ($keywordWords as $kw) {
                if (trim($kw) === $word) {
                    $score += 10;
                    $matchCount++;
                    break;
                }
            }
            
            // Partial keyword match
            if (strpos($entryKeywords, $word) !== false) {
                $score += 5;
                $matchCount++;
            }
            
            // Question text match
            if (strpos($entryQuestion, $word) !== false) {
                $score += 3;
                $matchCount++;
            }
        }
        
        // Bonus for matching multiple words
        if ($matchCount >= 2) {
            $score += 10;
        }
        
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestMatch = $entry;
        }
    }
    
    // HIGHER THRESHOLD: score >= 10 OR at least 2 words matching
    if ($bestMatch && ($bestScore >= 10)) {
        return $bestMatch;
    }
    
    // Only return if multiple important words matched
    if ($bestMatch && count($importantWords) >= 2 && $bestScore >= 5) {
        return $bestMatch;
    }
    
    return null;
}

// ============================================
// SAVE CONVERSATION
// ============================================
function saveConversation($sessionId, $userMessage, $botResponse) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO conversation_history (session_id, user_message, bot_response) VALUES (?, ?, ?)");
        $stmt->execute([$sessionId, $userMessage, $botResponse]);
    } catch (Exception $e) {
        error_log("Failed to save conversation: " . $e->getMessage());
    }
}

// ============================================
// CLEAR PENDING REQUESTS
// ============================================
function clearPendingRequests($sessionId) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE learning_requests SET status = 'ignored' WHERE session_id = ? AND status = 'pending'");
        $stmt->execute([$sessionId]);
    } catch (Exception $e) {
        error_log("Failed to clear pending requests: " . $e->getMessage());
    }
}

// ============================================
// GET PENDING LEARNING REQUEST
// ============================================
function getPendingLearningRequest($sessionId) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM learning_requests WHERE session_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$sessionId]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

// ============================================
// CREATE LEARNING REQUEST
// ============================================
function createLearningRequest($sessionId, $unknownQuestion) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO learning_requests (session_id, unknown_question, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$sessionId, $unknownQuestion]);
    } catch (Exception $e) {
        error_log("Failed to create learning request: " . $e->getMessage());
    }
}

// ============================================
// MAIN PROCESSING
// ============================================
try {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (!$input || !isset($input['message'])) {
        throw new Exception("No message received");
    }
    
    $userMessage = sanitizeInput($input['message']);
    $sessionId = getSessionId();
    
    if (empty($userMessage)) {
        throw new Exception("Empty message");
    }
    
    $messageType = detectMessageType($userMessage);
    
    switch ($messageType) {
        
        // ============================================
        // IDENTITY
        // ============================================
        case 'identity':
            $response = "I am the Gangarampur College Virtual Assistant! 🤖\n\n";
            $response .= "I'm here to help you with information about Gangarampur College, including courses, admissions, fees, facilities, events, and more.\n\n";
            $response .= "You can call me 'College Bot' or just 'Assistant'. How can I help you today?";
            clearPendingRequests($sessionId);
            break;
        
        // ============================================
        // BOT CORRECTION - NEW ADDITION
        // ============================================
        case 'bot_correction':
            $response = "I apologize for the confusion! 😔\n\n";
            $response .= "I'm still learning and sometimes I may not have all the correct information. ";
            $response .= "If you'd like to provide the correct information about Gangarampur College, ";
            $response .= "please share it and I'll update my knowledge.\n\n";
            $response .= "For accurate and official information, you can contact the college directly:\n";
            $response .= "📞 35212 91074, 81599 90678\n";
            $response .= "📧 ticgmpcollege@gmail.com\n";
            $response .= "🌐 https://gmpcollege.ac.in/";
            clearPendingRequests($sessionId);
            break;
        
        // ============================================
        // CASUAL ACKNOWLEDGMENTS
        // ============================================
        case 'casual':
            $casualResponses = [
                "Is there anything specific about Gangarampur College you'd like to know?",
                "Feel free to ask me anything about the college!",
                "I'm here if you have any questions about admissions, courses, or facilities."
            ];
            $response = $casualResponses[array_rand($casualResponses)];
            clearPendingRequests($sessionId);
            break;
        
        // ============================================
        // DECLINE (no thanks, etc.)
        // ============================================
        case 'decline':
            $response = "No problem! If you need any information about Gangarampur College later, just ask. 😊";
            clearPendingRequests($sessionId);
            break;
        
        // ============================================
        // GREETINGS
        // ============================================
        case 'greeting':
            $greetings = [
                "Hello! 👋 I'm the Gangarampur College assistant. How can I help you today?",
                "Hi there! 😊 Welcome to Gangarampur College Virtual Assistant. What would you like to know?",
                "Welcome! 🙏 Feel free to ask about courses, admissions, fees, events, and more.",
                "Good to see you! 💫 How may I assist you with college information today?"
            ];
            $response = $greetings[array_rand($greetings)];
            clearPendingRequests($sessionId);
            break;
        
        // ============================================
        // GRATITUDE
        // ============================================
        case 'gratitude':
            $thanks = [
                "You're welcome! 😊 Is there anything else I can help you with?",
                "Glad I could help! 🙏 Feel free to ask more questions about Gangarampur College.",
                "Happy to assist! 💫 Let me know if you need any other information."
            ];
            $response = $thanks[array_rand($thanks)];
            clearPendingRequests($sessionId);
            break;
        
        // ============================================
        // GOODBYES
        // ============================================
        case 'goodbye':
            $goodbyes = [
                "Goodbye! 👋 Feel free to come back anytime you need college information. Have a great day!",
                "Bye! Take care. 😊 I'm always here if you have questions about Gangarampur College.",
                "See you later! 🙏 Don't hesitate to reach out with more questions."
            ];
            $response = $goodbyes[array_rand($goodbyes)];
            clearPendingRequests($sessionId);
            break;
        
        // ============================================
        // CAPABILITY
        // ============================================
        case 'capability':
            $response = "I can help you with Gangarampur College information! 📚\n\n";
            $response .= "Here's what you can ask me about:\n\n";
            $response .= "• 📖 Courses - B.A., B.Sc., B.Com., B.C.A. with all subjects\n";
            $response .= "• 📝 Admissions - WBCAP portal, documents, process\n";
            $response .= "• 💰 Fees - Course-wise fee structure\n";
            $response .= "• 🎓 Scholarships - Government & merit scholarships\n";
            $response .= "• 🏛️ Departments - Arts, Science, Commerce, Computer\n";
            $response .= "• 🏠 Facilities - Library, Labs, NCC, Sports, WiFi\n";
            $response .= "• 📅 Events - Seminars, Cultural programs, NCC activities\n";
            $response .= "• 📢 Notices - Admissions, exams, results, announcements\n";
            $response .= "• 📞 Contact - Phone, Email, Address, Website\n\n";
            $response .= "Just type your question and I'll do my best to help!";
            clearPendingRequests($sessionId);
            break;
        
        // ============================================
        // QUERY
        // ============================================
        case 'query':
        default:
            $pendingRequest = getPendingLearningRequest($sessionId);
            
            if ($pendingRequest) {
                $messageLen = strlen($userMessage);
                
                // Short replies = not teaching
                if ($messageLen < 15) {
                    $conn = getDBConnection();
                    $stmt = $conn->prepare("UPDATE learning_requests SET status = 'rejected', user_response = ? WHERE id = ?");
                    $stmt->execute([$userMessage, $pendingRequest['id']]);
                    
                    $result = searchKnowledgeBase($userMessage);
                    if ($result) {
                        $response = is_array($result) ? $result['answer'] : $result;
                    } else {
                        $casualResponses = [
                            "Okay! Is there anything about Gangarampur College you'd like to know?",
                            "Got it! Feel free to ask me about courses, admissions, or facilities.",
                            "I understand. Let me know if you have any college-related questions!"
                        ];
                        $response = $casualResponses[array_rand($casualResponses)];
                    }
                }
                // Long replies = teaching (with complaint check)
                else {
                    // Check if user is complaining instead of teaching
                    $complaintWords = ['why', 'wrong', 'sorry', 'mistake', 'incorrect', 'dont', "don't", 'not correct', 'not right', 'bad'];
                    $isComplaint = false;
                    
                    foreach ($complaintWords as $word) {
                        if (strpos(strtolower($userMessage), $word) !== false) {
                            $isComplaint = true;
                            break;
                        }
                    }
                    
                    if ($isComplaint) {
                        // User is complaining, not teaching - reject learning
                        $conn = getDBConnection();
                        $stmt = $conn->prepare("UPDATE learning_requests SET status = 'rejected', user_response = ? WHERE id = ?");
                        $stmt->execute([$userMessage, $pendingRequest['id']]);
                        
                        $response = "I understand your concern. I apologize for any confusion. ";
                        $response .= "If you have questions about Gangarampur College, feel free to ask, ";
                        $response .= "or provide correct information and I'll learn it properly.";
                    } else {
                        // User is actually teaching - save the information
                        try {
                            $conn = getDBConnection();
                            $words = explode(' ', strtolower($userMessage));
                            $keywords = [];
                            foreach ($words as $word) {
                                $word = preg_replace('/[^a-z]/', '', $word);
                                if (strlen($word) > 3) $keywords[] = $word;
                            }
                            $keywordsStr = implode(',', array_unique($keywords));
                            
                            $stmt = $conn->prepare("INSERT INTO knowledge_base (question, answer, category, keywords, created_by, status) VALUES (?, ?, 'Learned', ?, 'user', 'active')");
                            $stmt->execute([$pendingRequest['unknown_question'], $userMessage, $keywordsStr]);
                            
                            $stmt = $conn->prepare("UPDATE learning_requests SET status = 'learned', user_response = ? WHERE id = ?");
                            $stmt->execute([$userMessage, $pendingRequest['id']]);
                            
                            $response = "✅ Thank you! I've learned this information and will use it for future questions.";
                        } catch (Exception $e) {
                            $response = "I had trouble saving that information. Could you try again?";
                        }
                    }
                }
            } else {
                $result = searchKnowledgeBase($userMessage);
                
                if ($result) {
                    $response = is_array($result) ? $result['answer'] : $result;
                    if (is_array($result) && !empty($result['category']) && $result['category'] !== 'General' && $result['category'] !== 'Learned' && $result['category'] !== 'Privacy') {
                        $response .= "\n\n📌 Category: " . $result['category'];
                    }
                } else {
                    createLearningRequest($sessionId, $userMessage);
                    $response = "🤔 I don't have information about that yet.\n\n";
                    $response .= "If you know about this and it's related to Gangarampur College, you can teach me! ";
                    $response .= "Just provide the information, and I'll remember it for future conversations.";
                }
            }
            break;
    }
    
    saveConversation($sessionId, $userMessage, $response);
    
    echo json_encode([
        'response' => $response,
        'status' => 'success',
        'type' => $messageType
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'response' => "I'm having a small technical issue. Please try again.\n\nIf this persists, contact Gangarampur College at 35212 91074.",
        'status' => 'error',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>