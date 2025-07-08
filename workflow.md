Simplified System Workflow
System Setup (Admin)
 1.1. Admin logs in → navigates to System Settings.
 1.2. Defines Programs (e.g. BSc IT, BSc Biology), Levels/Years (e.g. 100, 200), and Semesters (e.g. Spring, Fall).
 1.3. Creates Courses (each tied to a Program + Level + Semester).


Exam Creation (Teacher → Admin Approval)
 2.1. Teacher logs in → goes to Create Exam.
 2.2. Teacher selects Program/Level/Semester/Course, sets Exam Code, Duration, Start DateTime, End DateTime, and adds Questions (+ 4 choices each).
 2.3. Teacher submits exam for approval → status = “Pending”.
 2.4. Admin reviews pending exams → Approve or Reject.
 2.5. Approved exams become visible for student registration; rejected exams return to teacher with feedback.


Student Registration & Exam Sign‑Up
 3.1. Student self‑registers → creates account and verifies email (optional).
 3.2. Student logs in → enters Exam Code or browses available exams by Program/Level/Course.
 3.3. System checks: exam is Approved, registration window open, and student not already registered → creates ExamRegistration.


Taking the Exam
 4.1. At the scheduled start time (or on‑demand if allowed), student clicks “Start Exam”.
 4.2. Exam interface loads via AJAX → shows one question + four choices + on‑screen countdown timer.
 4.3. Student navigates Next/Previous; answers auto‑save every few seconds.
 4.4. When timer expires or student submits all questions, exam ends automatically.


Grading & Results
 5.1. Upon submission, system grades objective questions instantly.
 5.2. Results record total questions, correct, incorrect, score %, timestamp.
 5.3. Student can view results immediately and access Result History.


Ongoing Management


Admin can edit/delete users, manage approvals, audit logs.


Teacher can review past exams, update questions (pre‑approval), and view class performance.


Student can update profile, view past performances, and retake exams if allowed.



Using the admin dashboard at @/admin as a reference, redesign the student dashboard to look like that of the admin dashboard. The content of the student dashboard must not change. Make the modification where ever necessary from sidebar to navbar, to individual sections keep our project colour in mind and making the dashboard neat, nice and appealing.

Perfect!!!. Well done. Wow. I'm impressed.

Now let's move on to the inner content or sections of the dashboard that changes based on the sidebar link clicks. Use the same admin dashboard at @/admin to redesign the inner content of the dashboard(all inner content based on the link clicked at the sidebar).

do not forget our color and design structure.