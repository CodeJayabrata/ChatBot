# AI-Based College Information Chatbot

## Setup Instructions

### Prerequisites
- XAMPP (with Apache, PHP 7.4+, MySQL)
- Web Browser

### Installation Steps

1. **Start XAMPP**
   - Start Apache Server
   - Start MySQL Server

2. **Create Project Folder**
   - Create folder `college-chatbot` in `C:\xampp\htdocs\`
   - Copy all project files to this folder

3. **Import Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Click "New" to create database
   - Database name: `college_chatbot`
   - Click "Import" tab
   - Choose file: `database/schema.sql`
   - Click "Go"

4. **Configure Database Connection**
   - Open `includes/config/database.php`
   - Update credentials if needed (default works for XAMPP)

5. **Access the Application**
   - Open browser
   - Go to: http://localhost/college-chatbot/

6. **Test the Chatbot**
   - Click the chat button (bottom-right)
   - Ask questions like:
     - "What courses are available?"
     - "Tell me about fees"
     - "What are the office timings?"
     - "Tell me about hostel facilities"

### Features
- Smart knowledge base search
- Self-learning capability
- College-related content validation
- Conversation history
- Responsive design
- Professional UI/UX

### Project Structure