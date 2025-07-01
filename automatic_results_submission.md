✅ Solution Design: Automatic Result Finalization When Time Is Up
🔁 Option 1: Backend Scheduled Task (Recommended)
Use a cron job or background worker (Node.js, PHP, etc.) to:

Periodically (e.g., every minute) check for exams whose end_datetime has passed.

For each exam, find registered students who:

Have not submitted (i.e., don't have a results entry yet).

For each such student:

Analyze their student_answers.

Generate result via backend logic.

Insert into the results table.

✅ This is the safest and most scalable method.

📦 Sample Logic (Pseudo PHP / SQL)
php
Copy
Edit
// Fetch expired exams
$expiredExams = "SELECT exam_id FROM exams WHERE end_datetime <= NOW()";

// For each expired exam
$pendingStudents = "
    SELECT r.registration_id
    FROM exam_registrations r
    LEFT JOIN results res ON res.registration_id = r.registration_id
    WHERE r.exam_id = ? AND res.registration_id IS NULL
";

// For each registration_id, calculate and insert result:
$calc = "
    INSERT INTO results (registration_id, total_questions, correct_answers, incorrect_answers, score_percentage)
    SELECT
        sa.registration_id,
        COUNT(sa.question_id),
        SUM(CASE WHEN c.is_correct THEN 1 ELSE 0 END),
        SUM(CASE WHEN NOT c.is_correct THEN 1 ELSE 0 END),
        ROUND(SUM(CASE WHEN c.is_correct THEN 1 ELSE 0 END) / COUNT(sa.question_id) * 100, 2)
    FROM student_answers sa
    JOIN choices c ON sa.choice_id = c.choice_id
    WHERE sa.registration_id = ?
    GROUP BY sa.registration_id
";
⏱️ Option 2: On-Demand Trigger
Whenever a student tries to view results:

Check if exam time has expired and result not stored.

If yes, calculate and store before showing.

⚠️ This works for smaller systems, but not good for dashboards and bulk result views.

⚡ Option 3: Store Start Time + Duration Per Student
Track actual exam start time for each student, not just exam.start_datetime.

sql
Copy
Edit
ALTER TABLE exam_registrations ADD COLUMN started_at DATETIME;
Then compare:
started_at + INTERVAL duration_minutes MINUTE
vs
NOW()
to decide whether their exam should be auto-submitted.

✅ Summary
Method	Use Case	Pros	Cons
Cron/Worker Task	Periodically finalize expired exams	Fully automated, scalable	Needs server script
On-Demand Check	When user requests result	Easy to implement	Less reliable for large scale
Time-per-Student	More granular control	Better with flexible exams	Requires more tracking

