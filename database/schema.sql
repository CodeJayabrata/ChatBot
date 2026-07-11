-- Create Database
CREATE DATABASE IF NOT EXISTS college_chatbot;
USE college_chatbot;

-- Knowledge Base Table
CREATE TABLE knowledge_base (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100),
    keywords TEXT,
    created_by ENUM('admin', 'user', 'system') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Conversation History Table
CREATE TABLE conversation_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(100) NOT NULL,
    user_message TEXT NOT NULL,
    bot_response TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Learning Requests Table
CREATE TABLE learning_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(100) NOT NULL,
    unknown_question VARCHAR(500) NOT NULL,
    user_response TEXT,
    status ENUM('pending', 'learned', 'ignored', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert Default College Knowledge Base
INSERT INTO knowledge_base (question, answer, category, keywords, created_by) VALUES
('What is the college introduction?', 'Our college is a premier educational institution established in 2000, dedicated to providing quality education in engineering, management, and sciences. We are affiliated with XYZ University and approved by AICTE.', 'About', 'college,introduction,about,institute', 'admin'),

('What courses are available?', 'We offer B.Tech in Computer Science, Electronics, Mechanical, and Civil Engineering. We also offer BBA, BCA, and M.Tech programs.', 'Courses', 'courses,programs,degrees,available', 'admin'),

('What are the departments?', 'Our college has departments of Computer Science, Electronics & Communication, Mechanical Engineering, Civil Engineering, Management Studies, and Applied Sciences.', 'Departments', 'departments,branches,streams', 'admin'),

('How can I get admission?', 'Admissions are based on entrance exam scores (JEE Main for B.Tech) and merit lists. You can apply online through our website or visit the admission office with required documents.', 'Admission', 'admission,apply,entrance,enrollment', 'admin'),

('Who are the teachers?', 'Our college has highly qualified faculty with PhD holders and industry experts. Each department has experienced professors, associate professors, and assistant professors.', 'Faculty', 'teachers,faculty,professors,staff', 'admin'),

('What are the fees?', 'B.Tech fees are approximately ₹85,000 per year. BBA fees are ₹45,000 per year. Hostel fees are separate at ₹40,000 per year including mess.', 'Fees', 'fees,payment,cost,expenses,amount', 'admin'),

('Tell me about the library', 'Our central library has over 50,000 books, 200+ journals, and digital resources. It remains open from 9:00 AM to 8:00 PM on weekdays and 10:00 AM to 5:00 PM on weekends.', 'Facilities', 'library,books,journals,reading', 'admin'),

('Are scholarships available?', 'Yes, we offer merit-based scholarships, government scholarships for SC/ST/OBC students, and financial aid for economically weaker sections.', 'Scholarship', 'scholarship,financial aid,merit,assistance', 'admin'),

('What about hostel facilities?', 'We have separate hostels for boys and girls with 24/7 security, WiFi, mess facility, gym, and medical room. Rooms are available on sharing basis.', 'Hostel', 'hostel,accommodation,rooms,mess', 'admin'),

('How to contact the college?', 'You can call us at +91-1234567890, email at info@college.edu, or visit the administrative block during office hours.', 'Contact', 'contact,phone,email,address,reach', 'admin'),

('What are the office timings?', 'The college office remains open from 10:00 AM to 5:00 PM, Monday to Saturday. The office is closed on Sundays and public holidays.', 'Office', 'office timings,working hours,schedule,time', 'admin'),

('What events are organized?', 'We organize annual tech fest, cultural fest, sports meet, workshops, seminars, and industry visits throughout the academic year.', 'Events', 'events,fest,activities,cultural,technical', 'admin'),

('Where can I see notices?', 'Notices are displayed on the college website, notice boards in each department, and sent through official email to students.', 'Notice', 'notice,announcement,circular,notification', 'admin'),

('What about placements?', 'Our placement cell has tie-ups with 100+ companies. Average package is ₹6 LPA. Companies like TCS, Infosys, Wipro, and Amazon recruit from our campus.', 'Placement', 'placement,job,recruitment,companies,package', 'admin'),

('Describe the campus', 'Our 50-acre campus has modern classrooms, laboratories, sports complex, auditorium, cafeteria, and green spaces for a holistic learning environment.', 'Campus', 'campus,infrastructure,building,area', 'admin'),

('What about examinations?', 'Semester examinations are conducted in December and May. Internal assessments are held throughout the semester. Results are published within 30 days.', 'Examination', 'exam,examination,test,assessment,semester', 'admin'),

('When are results declared?', 'Results are usually declared within 30-45 days after examinations. You can check results on the university portal using your roll number.', 'Results', 'results,marks,grades,score,declared', 'admin'),

('What facilities are available?', 'We provide WiFi campus, modern labs, sports facilities, medical center, cafeteria, transport, ATM, and stationery shop within the campus.', 'Facilities', 'facilities,amenities,services,resources', 'admin');