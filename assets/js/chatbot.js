// ============================================
// GANGARAMPUR COLLEGE CHATBOT - FULLY FIXED
// ============================================

class CollegeVirtualAssistant {
    constructor() {
        // DOM Elements
        this.overlay = document.getElementById('chatbotOverlay');
        this.chatButton = document.getElementById('chatbotButton');
        this.messagesContainer = document.getElementById('chatMessages');
        this.messageInput = document.getElementById('messageInput');
        this.sendButton = document.getElementById('sendButton');
        this.typingIndicator = document.getElementById('typingIndicator');
        this.closeButton = document.getElementById('closeChatBtn');
        this.clearButton = document.getElementById('clearChatBtn');
        this.notificationBadge = document.getElementById('notificationBadge');

        // State
        this.isProcessing = false;
        this.isFirstOpen = true;
        this.conversationCount = 0;

        // Initialize
        this.initializeEventListeners();
        this.showWelcomeMessage();

        console.log('✅ Gangarampur College Chatbot Initialized Successfully');
    }

    // ============================================
    // EVENT LISTENERS
    // ============================================
    initializeEventListeners() {
        // Open chat
        this.chatButton.addEventListener('click', () => this.openChat());

        // Close chat
        this.closeButton.addEventListener('click', () => this.closeChat());

        // Overlay click to close
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.closeChat();
            }
        });

        // Send message
        this.sendButton.addEventListener('click', () => this.handleSendMessage());

        // Enter key to send
        this.messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.handleSendMessage();
            }
        });

        // Clear conversation
        this.clearButton.addEventListener('click', () => this.clearConversation());

        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.overlay.classList.contains('active')) {
                this.closeChat();
            }
        });
    }

    // ============================================
    // CHAT OPEN/CLOSE
    // ============================================
    openChat() {
        this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Hide notification
        this.notificationBadge.style.display = 'none';

        // Focus input after animation
        setTimeout(() => {
            this.messageInput.focus();
        }, 400);

        // Scroll to bottom
        this.scrollToBottom();

        // Track first open
        if (this.isFirstOpen) {
            this.isFirstOpen = false;
        }
    }

    closeChat() {
        this.overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // ============================================
    // MESSAGE HANDLING
    // ============================================
    async handleSendMessage() {
        const message = this.messageInput.value.trim();

        // Validation
        if (!message || this.isProcessing) {
            return;
        }

        // Clear input immediately
        this.messageInput.value = '';

        // Add user message to chat
        this.addMessage(message, 'user');

        // Show typing indicator
        this.showTypingIndicator();
        this.isProcessing = true;
        this.sendButton.disabled = true;

        try {
            // Make API call
            const data = await this.sendToServer(message);

            // Hide typing indicator
            this.hideTypingIndicator();

            // Add bot response
            if (data && data.response) {
                // Check if response contains error
                if (data.status === 'error') {
                    console.warn('Server returned error:', data.error);
                }
                this.addMessage(data.response, 'bot');
                this.conversationCount++;
            } else {
                // No response received
                this.addMessage(
                    "I received your message but I'm not sure how to respond. Could you try asking differently?",
                    'bot'
                );
            }

        } catch (error) {
            // Hide typing indicator
            this.hideTypingIndicator();

            // Log the error for debugging
            console.error('Chat Error Details:', error);

            // Show user-friendly error message
            this.addMessage(
                "I'm having trouble connecting to the server right now. 😕\n\n" +
                "Please check:\n" +
                "• Your internet connection is working\n" +
                "• The XAMPP server is running (Apache & MySQL)\n\n" +
                "Try refreshing the page or contact Gangarampur College:\n" +
                "📞 35212 91074\n" +
                "📧 ticgmpcollege@gmail.com",
                'bot'
            );

        } finally {
            // Reset state
            this.isProcessing = false;
            this.sendButton.disabled = false;
            this.messageInput.focus();
        }
    }

    // ============================================
    // API COMMUNICATION
    // ============================================
    async sendToServer(message) {
        console.log('📤 Sending message:', message);

        try {
            const response = await fetch('api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message
                })
            });

            console.log('📡 Response status:', response.status);

            // Check if response is OK
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Server error response:', errorText);
                throw new Error(`Server returned ${response.status}: ${errorText}`);
            }

            // Parse JSON
            const data = await response.json();
            console.log('📥 Received data:', data);

            return data;

        } catch (error) {
            console.error('❌ Fetch error:', error);
            throw error;
        }
    }

    // ============================================
    // DISPLAY MESSAGES
    // ============================================
    addMessage(content, type) {
        const messageWrapper = document.createElement('div');
        messageWrapper.className = `message-wrapper ${type}`;

        // Avatar
        const avatar = document.createElement('div');
        avatar.className = 'message-avatar-small';
        avatar.innerHTML = type === 'bot'
            ? '<i class="fas fa-robot"></i>'
            : '<i class="fas fa-user-graduate"></i>';

        // Message bubble
        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';

        // Support newlines in messages
        bubble.innerHTML = content.replace(/\n/g, '<br>');

        // Timestamp
        const time = document.createElement('span');
        time.className = 'message-time';
        time.textContent = this.getCurrentTime();
        bubble.appendChild(time);

        // Append elements
        if (type === 'bot') {
            messageWrapper.appendChild(avatar);
            messageWrapper.appendChild(bubble);
        } else {
            messageWrapper.appendChild(bubble);
            messageWrapper.appendChild(avatar);
        }

        this.messagesContainer.appendChild(messageWrapper);
        this.scrollToBottom();
    }

    // ============================================
    // TYPING INDICATOR
    // ============================================
    showTypingIndicator() {
        this.typingIndicator.classList.add('active');
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        this.typingIndicator.classList.remove('active');
    }

    // ============================================
    // UTILITY FUNCTIONS
    // ============================================
    showWelcomeMessage() {
        const welcomeMessage =
            '👋 Welcome to Gangarampur College Virtual Assistant!\n\n' +
            'I can help you with information about:\n\n' +
            '📚 Courses & Admissions\n' +
            '🏛️ Departments & Subjects\n' +
            '💰 Fees & Scholarships\n' +
            '🏠 College Facilities\n' +
            '📅 Events & Activities\n' +
            '📢 Notices & Announcements\n' +
            '📞 Contact & Location\n\n' +
            'How may I assist you today?';

        this.addMessage(welcomeMessage, 'bot');
    }

    getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString('en-IN', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    scrollToBottom() {
        setTimeout(() => {
            if (this.messagesContainer) {
                this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
            }
        }, 100);
    }

    async clearConversation() {
        if (confirm('Are you sure you want to clear the entire conversation?')) {
            // Clear UI
            this.messagesContainer.innerHTML = '';

            // Clear server history
            try {
                const response = await fetch('api/clear-chat.php');
                console.log('Clear chat response:', response.status);
            } catch (error) {
                console.error('Clear Error:', error);
            }

            // Reset counter and show welcome
            this.conversationCount = 0;
            this.showWelcomeMessage();

            // Show notification
            this.showNotification('Conversation cleared successfully ✅');
        }
    }

    showNotification(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1e3a8a;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
            z-index: 10001;
            animation: slideIn 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            font-family: 'Inter', 'Noto Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// ============================================
// INITIALIZE ON PAGE LOAD
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    // Small delay to ensure DOM is fully ready
    setTimeout(() => {
        new CollegeVirtualAssistant();
    }, 100);
});

// ============================================
// CSS ANIMATIONS FOR NOTIFICATIONS
// ============================================
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideIn {
        from { 
            transform: translateX(100%); 
            opacity: 0; 
        }
        to { 
            transform: translateX(0); 
            opacity: 1; 
        }
    }
    
    @keyframes slideOut {
        from { 
            transform: translateX(0); 
            opacity: 1; 
        }
        to { 
            transform: translateX(100%); 
            opacity: 0; 
        }
    }
`;
document.head.appendChild(styleSheet);