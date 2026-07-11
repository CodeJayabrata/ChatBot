// ============================================
// PROFESSIONAL COLLEGE CHATBOT
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

        // Focus input
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

        // Clear input
        this.messageInput.value = '';

        // Add user message to chat
        this.addMessage(message, 'user');

        // Show processing state
        this.showTypingIndicator();
        this.isProcessing = true;
        this.sendButton.disabled = true;

        try {
            // API Call
            const response = await this.sendToServer(message);

            // Hide typing indicator
            this.hideTypingIndicator();

            // Add bot response
            if (response && response.response) {
                this.addMessage(response.response, 'bot');
                this.conversationCount++;
            }

        } catch (error) {
            console.error('Chat Error:', error);
            this.hideTypingIndicator();
            this.addMessage(
                'I apologize, but I am experiencing technical difficulties at the moment. Please try again later or contact the administration office for immediate assistance.',
                'bot'
            );
        } finally {
            this.isProcessing = false;
            this.sendButton.disabled = false;
            this.messageInput.focus();
        }
    }

    // ============================================
    // API COMMUNICATION
    // ============================================
    async sendToServer(message) {
        const response = await fetch('api/chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                timestamp: new Date().toISOString()
            })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        return await response.json();
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
        avatar.innerHTML = type === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';

        // Message bubble
        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.textContent = content;

        // Timestamp
        const time = document.createElement('span');
        time.className = 'message-time';
        time.textContent = this.getCurrentTime();
        bubble.appendChild(time);

        // Append
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
            'Hello! I am your College Virtual Assistant. I can help you with information about:\n\n' +
            '📚 Courses & Admissions\n' +
            '🏛️ Departments & Faculty\n' +
            '💰 Fees & Scholarships\n' +
            '🏠 Hostel & Facilities\n' +
            '📅 Events & Notices\n' +
            '🎓 Placements & Results\n\n' +
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
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }, 100);
    }

    async clearConversation() {
        if (confirm('Are you sure you want to clear the entire conversation?')) {
            // Clear UI
            this.messagesContainer.innerHTML = '';

            // Clear server history
            try {
                await fetch('api/clear-chat.php');
            } catch (error) {
                console.error('Clear Error:', error);
            }

            // Reset and show welcome
            this.conversationCount = 0;
            this.showWelcomeMessage();

            // Show notification
            this.showNotification('Conversation cleared successfully');
        }
    }

    showNotification(message) {
        // Simple notification (you can enhance this)
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1B3A5C;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            border-left: 4px solid #C8A951;
            z-index: 10001;
            animation: slideIn 0.3s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        `;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// ============================================
// INITIALIZE ON PAGE LOAD
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    new CollegeVirtualAssistant();
});

// ============================================
// CSS ANIMATIONS FOR NOTIFICATIONS
// ============================================
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(styleSheet);