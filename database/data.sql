-- phpMyAdmin SQL Dump
-- Gangarampur College - Knowledge Base Data
-- Replace your existing knowledge_base data with this

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- First, clear existing data
TRUNCATE TABLE `knowledge_base`;
TRUNCATE TABLE `conversation_history`;
TRUNCATE TABLE `learning_requests`;

-- Reset auto-increment
ALTER TABLE `knowledge_base` AUTO_INCREMENT = 1;
ALTER TABLE `conversation_history` AUTO_INCREMENT = 1;
ALTER TABLE `learning_requests` AUTO_INCREMENT = 1;

-- ============================================
-- GANGARAMPUR COLLEGE KNOWLEDGE BASE
-- ============================================

INSERT INTO `knowledge_base` (`question`, `answer`, `category`, `keywords`, `created_by`, `status`) VALUES

-- ============================================
-- COLLEGE INTRODUCTION & ABOUT
-- ============================================

('What is Gangarampur College?', 'Gangarampur College is a prestigious institution of higher education located in Gangarampur, Dakshin Dinajpur, West Bengal, India. The college is dedicated to providing quality education in Arts, Science, and Commerce streams at the undergraduate level. We are committed to academic excellence, holistic development of students, and creating a vibrant learning environment with dedicated faculty and modern facilities.', 'About', 'gangarampur,college,introduction,about,institution,higher,education,west,bengal,india,dakshin,dinajpur', 'admin', 'active'),

('Tell me about Gangarampur College', 'Gangarampur College is a renowned educational institution situated in Gangarampur, Dakshin Dinajpur district of West Bengal. The college offers undergraduate programs in Arts (B.A.), Science (B.Sc.), Commerce (B.Com.), and Computer Applications (B.C.A.). We focus on building academic excellence through innovation, creativity, and student success. The college has a smart campus with modern facilities, dedicated faculty members, and various co-curricular activities including NCC training.', 'About', 'about,gangarampur,college,information,detail,overview,description', 'admin', 'active'),

('What is the history of Gangarampur College?', 'Gangarampur College was established to serve the higher education needs of students in Gangarampur and surrounding areas of Dakshin Dinajpur district. Since its inception, the college has been committed to providing quality education and has grown to become a respected institution in the region. The college continues to expand its academic offerings and facilities to meet the evolving educational needs of the community.', 'History', 'history,established,founded,started,beginning,origin,college,gangarampur', 'admin', 'active'),

('What is the mission of Gangarampur College?', 'The mission of Gangarampur College is to provide accessible, affordable, and quality higher education to students from all backgrounds. We aim to foster academic excellence, critical thinking, creativity, and ethical values among students. The college is dedicated to empowering students with knowledge and skills that prepare them for successful careers and responsible citizenship.', 'About', 'mission,vision,goal,objective,purpose,aim,college,gangarampur', 'admin', 'active'),

-- ============================================
-- COURSES & DEPARTMENTS
-- ============================================

('What courses are offered at Gangarampur College?', 'Gangarampur College offers the following undergraduate programs:\n\n1. B.A. Major (Bachelor of Arts) - 3 years\n   Subjects: Bengali, English, History, Geography, Political Science, Philosophy, Economics, Sanskrit, Sociology, and Education\n\n2. B.Sc. Major (Bachelor of Science) - 3 years\n   Subjects: Computer Science, Mathematics, Physics, Chemistry, Economics, Botany, and Zoology\n\n3. B.Com. Major (Bachelor of Commerce) - 3 years\n   Subjects: Accountancy, Finance, Business Studies, and Commercial Education\n\n4. B.C.A. Major (Bachelor of Computer Applications) - 3 years\n   Subjects: Computer Application, Programming, Database, Software Development, and Technology', 'Courses', 'courses,programs,subjects,offered,available,ba,bsc,bcom,bca,arts,science,commerce,computer,application,undergraduate', 'admin', 'active'),

('What subjects are available in B.A.?', 'The B.A. (Bachelor of Arts) program at Gangarampur College offers Major courses in the following subjects:\n\n• Bengali\n• English\n• History\n• Geography\n• Political Science\n• Philosophy\n• Economics\n• Sanskrit\n• Sociology\n• Education\n\nStudents can choose their Major subject based on their interest and eligibility.', 'Courses', 'ba,arts,subjects,bengali,english,history,geography,political,science,philosophy,economics,sanskrit,sociology,education,major', 'admin', 'active'),

('What subjects are available in B.Sc.?', 'The B.Sc. (Bachelor of Science) program at Gangarampur College offers Major courses in the following subjects:\n\n• Computer Science\n• Mathematics\n• Physics\n• Chemistry\n• Economics\n• Botany\n• Zoology\n\nStudents can select their Major subject based on their higher secondary background and eligibility criteria.', 'Courses', 'bsc,science,subjects,computer,mathematics,physics,chemistry,economics,botany,zoology,major', 'admin', 'active'),

('What is B.Com. at Gangarampur College?', 'The B.Com. (Bachelor of Commerce) program at Gangarampur College is a 3-year undergraduate degree focusing on commerce and business education. The program covers subjects including:\n\n• Accountancy\n• Finance\n• Business Studies\n• Commercial Education\n\nThis program prepares students for careers in accounting, finance, banking, and business management.', 'Courses', 'bcom,commerce,accountancy,finance,business,studies,commercial,education,major', 'admin', 'active'),

('What is B.C.A. at Gangarampur College?', 'The B.C.A. (Bachelor of Computer Applications) program at Gangarampur College is a 3-year undergraduate degree focused on computer applications and software development. The program covers:\n\n• Computer Application\n• Programming Languages\n• Database Management\n• Software Development\n• Information Technology\n\nThis course prepares students for careers in the IT industry and software development.', 'Courses', 'bca,computer,application,programming,database,software,development,technology,major', 'admin', 'active'),

('What are the departments at Gangarampur College?', 'Gangarampur College has the following academic departments organized by streams:\n\nArts Department: Bengali, English, History, Geography, Political Science, Philosophy, Economics, Sanskrit, Sociology, Education\n\nScience Department: Computer Science, Mathematics, Physics, Chemistry, Botany, Zoology\n\nCommerce Department: Accountancy, Finance, Business Studies\n\nComputer Applications Department: BCA with focus on Programming and Software Development', 'Departments', 'departments,streams,branches,arts,science,commerce,computer,application,bengali,english,history,geography', 'admin', 'active'),

-- ============================================
-- ADMISSION
-- ============================================

('How can I get admission to Gangarampur College?', 'Admissions to Gangarampur College are conducted through the Centralised Admission Portal (WBCAP - West Bengal Centralised Admission Portal). The admission process is as follows:\n\n1. Visit the WBCAP website: https://wbcap.in/\n2. Register and fill the online application form\n3. Select Gangarampur College and your preferred course\n4. Submit required documents\n5. Merit lists are published based on higher secondary marks\n6. Selected candidates must complete verification and pay fees\n\nFor direct inquiries, contact the college office or visit the official website: https://gmpcollege.ac.in/', 'Admission', 'admission,apply,online,process,merit,list,wbcap,centralised,portal,registration,form', 'admin', 'active'),

('What is WBCAP?', 'WBCAP stands for West Bengal Centralised Admission Portal. It is the official online platform for undergraduate admissions to colleges in West Bengal. Students seeking admission to Gangarampur College must apply through the WBCAP portal at https://wbcap.in/. The portal manages the entire admission process including application, merit list publication, and seat allocation.', 'Admission', 'wbcap,west,bengal,centralised,admission,portal,online,apply,registration', 'admin', 'active'),

('What documents are required for admission?', 'For admission to Gangarampur College, students typically need the following documents:\n\n• 10th Standard Mark Sheet and Certificate\n• 12th Standard (Higher Secondary) Mark Sheet\n• School Leaving/Transfer Certificate\n• Migration Certificate (if applicable)\n• Caste Certificate (SC/ST/OBC) if applicable\n• Recent Passport Size Photographs\n• Aadhar Card\n• Income Certificate (for scholarship)\n\nPlease check the official admission notice for the complete and updated list of required documents.', 'Admission', 'documents,required,certificate,marksheet,transfer,migration,caste,photo,adhar,income,admission', 'admin', 'active'),

('When do admissions start at Gangarampur College?', 'Admissions at Gangarampur College typically begin in June-July each year, following the publication of Higher Secondary (12th Standard) results. The exact dates are announced through:\n\n• The official college website: https://gmpcollege.ac.in/\n• The WBCAP portal: https://wbcap.in/\n• College notice board\n• Official college email communications\n\nStudents are advised to regularly check these sources for admission notifications.', 'Admission', 'admission,date,start,when,apply,deadline,schedule,timeline,notification', 'admin', 'active'),

('What is the intake capacity of Gangarampur College?', 'The intake capacity (seat matrix) for different courses at Gangarampur College varies by subject and is determined by the college administration in accordance with university guidelines. For the latest seat matrix and intake capacity details, visit the college website at https://gmpcollege.ac.in/seat-matrix or check the admission section during the application period.', 'Admission', 'intake,capacity,seat,matrix,available,vacancy,admission,limit', 'admin', 'active'),

-- ============================================
-- FEES & SCHOLARSHIP
-- ============================================

('What are the fees at Gangarampur College?', 'Gangarampur College offers affordable education as a government-aided institution. The fee structure varies by course:\n\n• B.A. (Arts): Approximately ₹2,000 - ₹4,000 per year\n• B.Sc. (Science): Approximately ₹3,000 - ₹5,000 per year\n• B.Com. (Commerce): Approximately ₹2,500 - ₹4,500 per year\n• B.C.A. (Computer Applications): Approximately ₹8,000 - ₹12,000 per year\n\nNote: Fees are subject to change. Contact the college office for the exact current fee structure. Additional fees may apply for laboratory, library, and other facilities.', 'Fees', 'fees,payment,cost,expense,amount,rupees,per,year,structure,ba,bsc,bcom,bca', 'admin', 'active'),

('Are scholarships available at Gangarampur College?', 'Yes, Gangarampur College facilitates various scholarship schemes for eligible students:\n\n• Government Scholarships for SC/ST/OBC students\n• Minority Scholarships\n• Kanyashree Prakalpa (for girl students)\n• Swami Vivekananda Merit-cum-Means Scholarship\n• Student Credit Card Scheme\n• Various other state and central government scholarship schemes\n\nStudents can apply through the official scholarship portals. The college office assists students with scholarship applications and documentation. Contact the college office for current scholarship opportunities and application procedures.', 'Scholarship', 'scholarship,financial,aid,assistance,government,sc,st,obc,minority,kanyashree,swami,vivekananda,merit,mean', 'admin', 'active'),

-- ============================================
-- FACILITIES
-- ============================================

('What facilities are available at Gangarampur College?', 'Gangarampur College provides various facilities for students:\n\n• Library with collection of books and journals\n• Computer Laboratory with internet access\n• Science Laboratories (Physics, Chemistry, Botany, Zoology)\n• Smart Classrooms\n• NCC Training Facility\n• Student Common Room\n• Sports Facilities\n• Canteen/Cafeteria\n• WiFi Connectivity (in designated areas)\n• Drinking Water Facility\n• Separate Toilet Facilities for Boys and Girls\n\nThese facilities support both academic and extracurricular development of students.', 'Facilities', 'facilities,amenities,library,lab,laboratory,computer,classroom,sports,canteen,wifi,internet,ncc,training', 'admin', 'active'),

('Tell me about the library at Gangarampur College', 'Gangarampur College maintains a well-stocked library that serves as a vital resource center for students and faculty. The library houses a collection of textbooks, reference books, journals, and periodicals covering all the subjects offered by the college. Students can access the library during college hours for reading, reference, and borrowing books as per library rules. The library provides a quiet environment conducive to study and research.', 'Library', 'library,books,journals,reading,study,reference,collection,resource,center', 'admin', 'active'),

('Does Gangarampur College have computer facilities?', 'Yes, Gangarampur College has computer laboratory facilities for students, particularly for B.C.A. and Computer Science students. The computer lab is equipped with computers, internet connectivity, and necessary software for programming, database management, and other academic purposes. Students can use these facilities for practical classes, project work, and research activities.', 'Facilities', 'computer,lab,laboratory,internet,programming,software,bca,science,facility', 'admin', 'active'),

('Does Gangarampur College have NCC?', 'Yes, Gangarampur College has an active NCC (National Cadet Corps) unit. NCC cadets participate in regular parade training, drills, and various activities that promote discipline, leadership, and national integration. The NCC unit at Gangarampur College helps students develop personality, character, and a spirit of service to the nation. Interested students can enroll in NCC at the time of admission.', 'NCC', 'ncc,national,cadet,corps,training,parade,discipline,leadership,unit', 'admin', 'active'),

-- ============================================
-- CONTACT & LOCATION
-- ============================================

('How to contact Gangarampur College?', 'You can contact Gangarampur College through the following channels:\n\n📞 Phone: 35212 91074, 81599 90678\n📧 Email: ticgmpcollege@gmail.com\n🌐 Website: https://gmpcollege.ac.in/\n📍 Address: Gangarampur College, P.O. & P.S. Gangarampur, District - Dakshin Dinajpur, West Bengal, India\n\nFor admissions, academic queries, or general information, you can call or email during office hours.', 'Contact', 'contact,phone,email,address,reach,number,call,mail,website,location', 'admin', 'active'),

('What is the address of Gangarampur College?', 'Gangarampur College is located at:\n\nP.O. & P.S. Gangarampur,\nDistrict - Dakshin Dinajpur,\nWest Bengal, India\n\nYou can visit the college during working hours or contact through phone: 35212 91074, 81599 90678. The college website is https://gmpcollege.ac.in/', 'Contact', 'address,location,post,office,police,station,district,dakshin,dinajpur,west,bengal', 'admin', 'active'),

('What is the email of Gangarampur College?', 'The official email address of Gangarampur College is ticgmpcollege@gmail.com. For admission-related queries, general information, or any other college-related communication, you can send an email to this address. The college administration responds to emails during working hours.', 'Contact', 'email,mail,contact,tic,gmp,college,gmail,address', 'admin', 'active'),

('What is the phone number of Gangarampur College?', 'The contact phone numbers for Gangarampur College are:\n\n📞 35212 91074\n📞 81599 90678\n\nYou can call these numbers during office hours for admission inquiries, academic information, or any other college-related queries.', 'Contact', 'phone,number,contact,call,telephone,mobile,landline', 'admin', 'active'),

-- ============================================
-- OFFICE TIMINGS
-- ============================================

('What are the office timings of Gangarampur College?', 'The general working hours of Gangarampur College are:\n\n• Monday to Friday: 10:00 AM to 5:00 PM\n• Saturday: 10:00 AM to 2:00 PM (may vary)\n• Sunday and Public Holidays: Closed\n\nThe administrative office follows these timings. For specific departments or sections, timings may vary slightly. It is recommended to visit during the morning hours for administrative work.', 'Office', 'office,timing,time,hours,working,open,close,monday,friday,saturday,sunday,closed', 'admin', 'active'),

('When is Gangarampur College open?', 'Gangarampur College operates from Monday to Saturday:\n\n• Monday to Friday: 10:00 AM to 5:00 PM\n• Saturday: 10:00 AM to 2:00 PM\n• Sunday and Government Holidays: College remains closed\n\nStudents should check the academic calendar for class timings and examination schedules separately.', 'Office', 'open,close,working,day,hour,timing,monday,saturday,sunday,holiday', 'admin', 'active'),

-- ============================================
-- EXAMINATION & RESULTS
-- ============================================

('How are examinations conducted at Gangarampur College?', 'Gangarampur College follows the semester system for examinations as prescribed by its affiliated university. The examination pattern includes:\n\n• Internal Assessments (throughout the semester)\n• End-Semester Examinations (at the end of each semester)\n• Practical Examinations (for Science and Computer subjects)\n• Project Work (as applicable)\n\nExamination schedules, admit cards, and related information are published on the college notice board and website. Students must regularly check for updates.', 'Examination', 'exam,examination,test,semester,internal,assessment,practical,end,schedule,pattern', 'admin', 'active'),

('When are examinations held?', 'Examinations at Gangarampur College are conducted as per the semester system:\n\n• Odd Semester (1st, 3rd, 5th): Usually November-December\n• Even Semester (2nd, 4th, 6th): Usually April-May\n\nExact dates are announced by the college examination cell and the affiliated university. Students should check the college notice board and official website for detailed examination timetables.', 'Examination', 'exam,examination,date,schedule,when,semester,odd,even,november,december,april,may', 'admin', 'active'),

('How to check results at Gangarampur College?', 'Results for semester examinations are usually published on:\n\n• The college notice board\n• The official college website (https://gmpcollege.ac.in/)\n• The university examination portal\n\nStudents can check their results using their roll number or registration number. The college also notifies students about result publication through official communications.', 'Results', 'results,marks,grade,score,check,online,website,portal,roll,number,registration', 'admin', 'active'),

-- ============================================
-- EVENTS & ACTIVITIES
-- ============================================

('What events are organized at Gangarampur College?', 'Gangarampur College organizes various academic and cultural events throughout the academic year:\n\n• Seminars and Conferences (like ICWSS - International Conference on Wetland, Society and Sustainability)\n• Cultural Programs and Festivals\n• NCC Parades and Training Activities\n• Sports Events\n• Departmental Workshops\n• Academic Competitions\n• Social and Community Service Activities\n\nEvent notifications are published on the college website and notice board.', 'Events', 'events,activities,seminar,conference,cultural,festival,ncc,sports,workshop,competition,program', 'admin', 'active'),

('Does Gangarampur College organize seminars?', 'Yes, Gangarampur College organizes academic seminars and conferences. The college has hosted events such as ICWSS (International Conference on Wetland, Society and Sustainability) organized by Bangiyo Bhugol Mancha. Such events provide students with exposure to research, academic discussions, and networking opportunities with scholars and experts.', 'Events', 'seminar,conference,workshop,academic,research,icwss,international,wetland,sustainability', 'admin', 'active'),

-- ============================================
-- STUDENT SERVICES & PORTALS
-- ============================================

('What is the Student Portal of Gangarampur College?', 'Gangarampur College provides a Student Portal for enrolled students. Students can access the portal for:\n\n• Academic information\n• Course materials\n• Attendance records\n• Internal assessment marks\n• Notifications and updates\n\nAccess the Student Portal at: http://www.casgmpcollege.org.in/', 'Student Portal', 'student,portal,login,access,academic,information,course,material,attendance,marks', 'admin', 'active'),

('What is LMS at Gangarampur College?', 'LMS stands for Learning Management System. Gangarampur College provides an LMS portal for students and faculty to facilitate online learning and academic management. Through the LMS, students can access study materials, submit assignments, and participate in online learning activities.\n\nAccess the LMS at: http://lmsgmpcollege.org.in/lms/', 'LMS', 'lms,learning,management,system,online,study,material,assignment,portal,login', 'admin', 'active'),

('How to apply for online admission?', 'Students seeking admission to Gangarampur College can apply online through:\n\n1. WBCAP (West Bengal Centralised Admission Portal): https://wbcap.in/\n2. College Online Admission Portal: http://www.onlineadmissiongrpcollege.org/\n\nThe application process involves registration, filling personal and academic details, uploading documents, and paying the application fee. Check the official notifications for application dates and detailed procedures.', 'Admission', 'online,admission,apply,registration,portal,wbcap,website,application,form', 'admin', 'active'),

-- ============================================
-- ALUMNI
-- ============================================

('Does Gangarampur College have an Alumni Association?', 'Yes, Gangarampur College has an Alumni Association that connects former students with the college community. The alumni network helps in:\n\n• Networking opportunities for students and alumni\n• Sharing professional experiences\n• Contributing to college development\n• Mentoring current students\n\nAlumni can register and stay connected with the college through the alumni section on the college website.', 'Alumni', 'alumni,association,former,students,network,registration,connect,community', 'admin', 'active'),

-- ============================================
-- GENERAL INFORMATION
-- ============================================

('Is Gangarampur College a government college?', 'Gangarampur College is a government-aided college located in Gangarampur, Dakshin Dinajpur, West Bengal. It receives support from the state government and follows the regulations and guidelines of the Department of Higher Education, Government of West Bengal, and its affiliated university.', 'About', 'government,aided,college,state,west,bengal,higher,education,department', 'admin', 'active'),

('What is the website of Gangarampur College?', 'The official website of Gangarampur College is:\n\n🌐 https://gmpcollege.ac.in/\n\nYou can visit the website for:\n• Admission notifications\n• Academic information\n• Department details\n• Events and notices\n• Contact information\n• Photo gallery\n• Important links and portals', 'Website', 'website,url,link,official,site,online,gmp,college,ac,in', 'admin', 'active'),

('Where can I find notices of Gangarampur College?', 'Notices and announcements of Gangarampur College are published through multiple channels:\n\n• Official College Website: https://gmpcollege.ac.in/notice\n• College Notice Board (on campus)\n• Department Notice Boards\n• Student Portal notifications\n• Official email communications\n\nStudents are advised to regularly check the website and notice boards for important updates regarding admissions, examinations, events, and other announcements.', 'Notice', 'notice,notification,announcement,circular,news,update,website,board,information', 'admin', 'active'),

('Does Gangarampur College have sports facilities?', 'Yes, Gangarampur College provides sports facilities for the physical development and recreation of students. The college encourages participation in various sports and games as part of the overall development of students. Sports events and competitions are organized periodically.', 'Sports', 'sports,facilities,games,physical,recreation,competition,event,student,development', 'admin', 'active'),

('Is there hostel facility at Gangarampur College?', 'For information about hostel facilities at Gangarampur College, students are advised to contact the college administrative office directly. The college office can provide current information about accommodation options for students coming from distant areas.\n\nContact: 35212 91074 or email ticgmpcollege@gmail.com', 'Hostel', 'hostel,accommodation,room,facility,residence,stay,living,student,housing', 'admin', 'active'),

('How to verify documents from Gangarampur College?', 'Gangarampur College provides a document verification service. Students or third parties requiring document verification can use the verification portal:\n\n🔗 https://verification.gmpcollege.ac.in/\n\nThis service helps verify the authenticity of academic documents issued by the college.', 'Verification', 'verification,document,certificate,authenticity,online,portal,verify', 'admin', 'active');

COMMIT;

-- ============================================
-- VERIFICATION QUERY
-- ============================================
-- Check if data was inserted correctly
SELECT COUNT(*) as total_entries FROM knowledge_base;
SELECT category, COUNT(*) as count FROM knowledge_base GROUP BY category;