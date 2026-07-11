class CollegeChatbot {
    constructor() {
        this.overlay = document.getElementById('chatbotOverlay');
        this.messagesContainer = document.getElementById('chatMessages');
        this.messageInput = document.getElementById('messageInput');
        this.sendButton = document.getElementById('sendButton');
        this.typingIndicator = document.getElementById('typingIndicator');
        this.isProcessing = false;

        this.init();
    }

    init() {
        // Event listeners
        document.getElementById('chatbotButton').addEventListener('click', () => this.open());
        document.getElementById('closeChat').addEventListener('click', () => this.close());
        document.getElementById('clearChat').addEventListener('click', () => this.clearChat());
        this.sendButton.addEventListener('click', () => this.sendMessage());
        this.messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Close on overlay click
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });

        // Initial greeting
        this.addBotMessage("Hello! 👋 I'm your college assistant. How can I help you today?");
    }

    open() {
        this.overlay.classList.add('active');
        this.messageInput.focus();
        document.body.style.overflow = 'hidden';
    }

    close() {
        this.overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;

        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        avatar.textContent = type === 'bot' ? '🤖' : '👤';

        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.textContent = content;

        const time = document.createElement('span');
        time.className = 'message-time';
        time.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        bubble.appendChild(time);

        if (type === 'bot') {
            messageDiv.appendChild(avatar);
            messageDiv.appendChild(bubble);
        } else {
            messageDiv.appendChild(bubble);
            messageDiv.appendChild(avatar);
        }

        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    addBotMessage(content) {
        this.addMessage(content, 'bot');
    }

    addUserMessage(content) {
        this.addMessage(content, 'user');
    }

    showTyping() {
        this.typingIndicator.classList.add('active');
        this.scrollToBottom();
    }

    hideTyping() {
        this.typingIndicator.classList.remove('active');
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    async sendMessage() {
        const message = this.messageInput.value.trim();

        if (!message || this.isProcessing) return;

        // Add user message
        this.addUserMessage(message);
        this.messageInput.value = '';

        // Show typing indicator
        this.showTyping();
        this.isProcessing = true;
        this.sendButton.disabled = true;

        try {
            const response = await fetch('api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();

            // Hide typing indicator
            this.hideTyping();

            if (data.response) {
                this.addBotMessage(data.response);
            }
        } catch (error) {
            this.hideTyping();
            this.addBotMessage("Sorry, I'm having trouble connecting. Please try again.");
            console.error('Error:', error);
        }

        this.isProcessing = false;
        this.sendButton.disabled = false;
        this.messageInput.focus();
    }

    async clearChat() {
        if (confirm('Are you sure you want to clear the conversation?')) {
            this.messagesContainer.innerHTML = '';

            try {
                await fetch('api/clear-chat.php');
                this.addBotMessage("Conversation cleared! How can I help you?");
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CollegeChatbot();
});