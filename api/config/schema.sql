-- SQL Schema for Online Exam Management System (Simplified & Relational)

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE programs (
    program_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE levels (
    level_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE semesters (
    semester_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    start_date DATE,
    end_date DATE
);

CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    program_id INT NOT NULL,
    level_id INT NOT NULL,
    semester_id INT NOT NULL,
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (level_id) REFERENCES levels(level_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id)
);

CREATE TABLE exams (
    exam_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_code VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    course_id INT NOT NULL,
    teacher_id INT NOT NULL,
    duration_minutes INT NOT NULL,
    start_datetime DATETIME,
    end_datetime DATETIME,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    approved_by INT,
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    FOREIGN KEY (teacher_id) REFERENCES users(user_id),
    FOREIGN KEY (approved_by) REFERENCES users(user_id)
);

CREATE TABLE questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    question_text TEXT NOT NULL,
    sequence_number INT,
    FOREIGN KEY (exam_id) REFERENCES exams(exam_id)
);

CREATE TABLE choices (
    choice_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    choice_text VARCHAR(255) NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES questions(question_id)
);

CREATE TABLE exam_registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    student_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES exams(exam_id),
    FOREIGN KEY (student_id) REFERENCES users(user_id)
);

CREATE TABLE student_answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    question_id INT NOT NULL,
    choice_id INT NOT NULL,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES exam_registrations(registration_id),
    FOREIGN KEY (question_id) REFERENCES questions(question_id),
    FOREIGN KEY (choice_id) REFERENCES choices(choice_id)
);

-- Option 1: Persistent results table, populated at exam submission time
CREATE TABLE results (
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    total_questions INT,
    correct_answers INT,
    incorrect_answers INT,
    score_percentage DECIMAL(5,2),
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES exam_registrations(registration_id)
);

-- Option 2: Real-time view for live calculated results (hybrid logic)
-- Useful for instant scoring preview before storing permanent result
CREATE VIEW live_results AS
SELECT
    sa.registration_id,
    COUNT(sa.question_id) AS total_questions,
    SUM(CASE WHEN c.is_correct THEN 1 ELSE 0 END) AS correct_answers,
    SUM(CASE WHEN NOT c.is_correct THEN 1 ELSE 0 END) AS incorrect_answers,
    ROUND((SUM(CASE WHEN c.is_correct THEN 1 ELSE 0 END) / COUNT(sa.question_id)) * 100, 2) AS score_percentage
FROM student_answers sa
JOIN choices c ON sa.choice_id = c.choice_id
GROUP BY sa.registration_id;

-- Best practice: Use the 'live_results' view for real-time display immediately after exam,
-- then store the result in the 'results' table to preserve history and improve performance.

CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seen BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
