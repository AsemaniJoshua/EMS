
        function editExam(examId) {
            window.location.href = `editExam.php?id=${examId}`;
        }

        function publishExam(examId) {
            Swal.fire({
                title: 'Publish Exam',
                text: 'Are you sure you want to publish this exam? Once published, students will be able to see it.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, publish it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/api/exams/publishExam.php', {
                            exam_id: examId
                        })
                        .then(response => {
                            if (response.data.status === 'success') {
                                Swal.fire('Published!', response.data.message, 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                Swal.fire('Error!', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred while publishing the exam.', 'error');
                        });
                }
            });
        }

        function deleteExam(examId) {
            Swal.fire({
                title: 'Delete Exam',
                text: 'Are you sure you want to delete this exam? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/api/exams/deleteExam.php', {
                            exam_id: examId
                        })
                        .then(response => {
                            if (response.data.status === 'success') {
                                Swal.fire('Deleted!', response.data.message, 'success');
                                setTimeout(() => window.location.href = 'index.php', 1500);
                            } else {
                                Swal.fire('Error!', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred while deleting the exam.', 'error');
                        });
                }
            });
        }

        // Question management functions
        let choiceCount = 0;

        function toggleQuestionForm() {
            const form = document.getElementById('questionForm');
            const isHidden = form.classList.contains('hidden');

            form.classList.toggle('hidden');

            if (isHidden) {
                // Clear the form first
                document.getElementById('newQuestionForm').reset();
                document.getElementById('choicesContainer').innerHTML = '';
                choiceCount = 0;

                // Add at least two choice fields by default
                addChoiceField();
                addChoiceField();
            }
        }

        function addChoiceField(text = '', isCorrect = false) {
            const container = document.getElementById('choicesContainer');
            const choiceId = `choice-${choiceCount++}`;

            const html = `
                <div class="flex gap-2 items-center">
                    <input type="text" name="choices[]" value="${text}" placeholder="Choice text" class="flex-1 border rounded p-1" required />
                    <label class="flex items-center gap-1 text-sm">
                        <input type="radio" name="correct_choice" value="${choiceCount - 1}" ${isCorrect ? 'checked' : ''} required />
                        Correct
                    </label>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500"><i class="fas fa-trash-alt"></i></button>
                </div>`;

            container.insertAdjacentHTML('beforeend', html);
        }

        function addEditChoiceField(form, text = '', isCorrect = false) {
            const container = form.querySelector('.edit-choices-container');
            const inputs = container.querySelectorAll('input[name="choices[]"]');
            const index = inputs.length;

            const html = `
                <div class="flex gap-2 items-center">
                    <input type="text" name="choices[]" value="${text}" placeholder="Choice text" class="flex-1 border rounded p-1" required />
                    <label class="flex items-center gap-1 text-sm">
                        <input type="radio" name="correct_choice" value="${index}" ${isCorrect ? 'checked' : ''} required />
                        Correct
                    </label>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500"><i class="fas fa-trash-alt"></i></button>
                </div>`;

            container.insertAdjacentHTML('beforeend', html);
        }

        // Initialize question form submission
        document.getElementById('newQuestionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const questionText = form.question_text.value.trim();
            const choiceInputs = form.querySelectorAll('input[name="choices[]"]');
            const correctIndex = form.querySelector('input[name="correct_choice"]:checked')?.value;

            if (!questionText || choiceInputs.length < 2 || correctIndex === undefined) {
                alert("Please enter a question, at least two choices, and select the correct one.");
                return;
            }

            const choices = Array.from(choiceInputs).map((input, i) => ({
                choice_text: input.value.trim(),
                is_correct: parseInt(correctIndex) === i
            }));

            axios.post('/api/exams/addQuestionWithOptions.php', {
                exam_id: <?php echo $examId; ?>,
                question_text: questionText,
                choices
            }).then(response => {
                if (response.data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Question added successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    Swal.fire('Error', response.data.message || "Error occurred", 'error');
                }
            }).catch(() => {
                Swal.fire('Error', "Server error occurred", 'error');
            });
        });

        // Toggle the edit form for a question
        function toggleEditForm(questionId) {
            const questionItem = document.getElementById(`question-${questionId}`);
            const displaySection = questionItem.querySelector('.question-display');
            const editSection = questionItem.querySelector('.question-edit-form');

            displaySection.classList.toggle('hidden');
            editSection.classList.toggle('hidden');
        }

        // Update an existing question
        function updateQuestion(e, questionId) {
            e.preventDefault();

            const form = e.target;
            const questionText = form.question_text.value.trim();
            const choiceInputs = form.querySelectorAll('input[name="choices[]"]');
            const correctIndex = form.querySelector('input[name="correct_choice"]:checked')?.value;

            if (!questionText || choiceInputs.length < 2 || correctIndex === undefined) {
                alert("Please enter a question, at least two choices, and select the correct one.");
                return false;
            }

            const choices = Array.from(choiceInputs).map((input, i) => ({
                choice_text: input.value.trim(),
                is_correct: parseInt(correctIndex) === i
            }));

            axios.post('/api/exams/editQuestionWithOptions.php', {
                question_id: questionId,
                question_text: questionText,
                choices
            }).then(response => {
                if (response.data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Question updated successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    Swal.fire('Error', response.data.message || "Error occurred", 'error');
                }
            }).catch(() => {
                Swal.fire('Error', "Server error occurred", 'error');
            });

            return false;
        }

        function deleteQuestion(questionId) {
            Swal.fire({
                title: 'Delete Question',
                text: 'Are you sure you want to delete this question? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/api/exams/deleteQuestion.php', {
                            question_id: questionId
                        })
                        .then(response => {
                            if (response.data.status === 'success') {
                                Swal.fire('Deleted!', response.data.message, 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                Swal.fire('Error!', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred while deleting the question.', 'error');
                        });
                }
            });
        }

        function deleteRegisteredStudent(examId, studentId) {
            Swal.fire({
                title: 'Remove Student',
                text: 'Are you sure you want to remove this student from the exam?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/api/exams/deleteRegisteredStudent.php', {
                            exam_id: examId,
                            student_id: studentId
                        })
                        .then(response => {
                            if (response.data.status === 'success') {
                                Swal.fire('Removed!', response.data.message, 'success');
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                Swal.fire('Error!', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred while removing the student.', 'error');
                        });
                }
            });
        }
    