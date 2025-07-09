SET FOREIGN_KEY_CHECKS = 0; -- Temporarily disable foreign key checks

-- Core Tables (no external FKs)
CREATE TABLE departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
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

CREATE TABLE admins (
    admin_id INT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tables with FKs to core tables
CREATE TABLE programs (
    program_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    department_id INT NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
);

CREATE TABLE teachers (
    teacher_id INT PRIMARY KEY,
    staff_id VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20),
    username VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL,
    department_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    resetOnLogin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
);

CREATE TABLE students (
    student_id INT PRIMARY KEY,
    index_number VARCHAR(50) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female'),
    status ENUM('active', 'inactive', 'graduated') DEFAULT 'active',
    level_id INT NOT NULL,
    program_id INT NOT NULL,
    department_id INT NOT NULL,
    resetOnLogin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (level_id) REFERENCES levels(level_id) ,
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
);

CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    department_id INT NOT NULL,
    credits INT NOT NULL,
    program_id INT NOT NULL,
    level_id INT NOT NULL,
    semester_id INT NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(department_id),
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (level_id) REFERENCES levels(level_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id)
);

-- Tables with FKs to previous tables
CREATE TABLE exams (
    exam_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_code VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    department_id INT NOT NULL,
    program_id INT NOT NULL,
    semester_id INT NOT NULL,
    course_id INT NOT NULL,
    teacher_id INT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected', 'Draft', 'Completed') DEFAULT 'Pending',
    duration_minutes INT NOT NULL,
    pass_mark DECIMAL(5,2) DEFAULT 50.00,
    total_marks INT NOT NULL, -- Added column
    start_datetime DATETIME,
    end_datetime DATETIME,
    max_attempts INT DEFAULT 1,
    randomize BOOLEAN DEFAULT FALSE,
    show_results BOOLEAN DEFAULT TRUE,
    anti_cheating BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    FOREIGN KEY (department_id) REFERENCES departments(department_id),
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id),
    FOREIGN KEY (approved_by) REFERENCES admins(admin_id)
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

-- Note: Modified FOREIGN KEY for student_id to reference 'students' table
CREATE TABLE exam_registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    student_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES exams(exam_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Tables dependent on exam_registrations, questions, choices
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

-- Note: Modified FOREIGN KEY for user_id to reference 'students' table
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- Assuming this refers to a student for simplicity, given no 'users' table
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seen BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES students(student_id)
);

SET FOREIGN_KEY_CHECKS = 1; -- Re-enable foreign key checks

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

CREATE TABLE teacher_courses (
    teacher_course_id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    course_id INT NOT NULL,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
   

    CONSTRAINT fk_teacher
        FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_course
        FOREIGN KEY (course_id) REFERENCES courses(course_id)
        ON DELETE CASCADE
);