<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Students') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="inline-block w-full shadow-md rounded-lg p-2 overflow-hidden">
                
                <x-primary-button class="mb-2" id="openModalButton">
                    {{ __('Add Student') }}
                </x-primary-button>

                <input id="searchInput" class="border p-2 mb-4 w-full" type="text" placeholder="Search...">

                <div id="successMessage" class="fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded hidden">
                    <span id="successText"></span>
                </div>

                <table class="w-full table-auto leading-normal ">
                    <thead class="">
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Name
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Subject
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Marks
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody id="TableBody">





                    </tbody>
                </table>
                <nav class="mt-5 flex items-center justify-end text-sm" aria-label="Page navigation example">


                    <ul class="list-style-none  flex justify-end">
                        <li>
                            <button id="prevButton" class="px-4 py-2 bg-gray-500 text-white">Previous</button>
                        </li>

                        <li>
                            <button id="nextButton" class="px-4 py-2 bg-blue-500 text-white">Next</button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>


    <!-- Student Modal -->
    <div id="studentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 id="modalTitle" class="text-2xl mb-4"></h2>
            <div class="mb-4">
                <input id="studentName" name="name" class="border p-2 w-full" type="text" placeholder="Name">
                <p id="nameError" class="text-red-500 text-sm mt-1"></p>
            </div>
            <div class="mb-4">
                <input id="studentSubject" name="subject" class="border p-2 w-full" type="text" placeholder="Subject">
                <p id="subjectError" class="text-red-500 text-sm mt-1"></p>
            </div>
            <div class="mb-4">
                <input id="studentMarks" name="marks" class="border p-2 w-full" type="number" placeholder="Marks">
                <p id="marksError" class="text-red-500 text-sm mt-1"></p>
            </div>
            <div class="flex justify-end">
                <button id="saveStudentButton" class="px-4 py-2 bg-blue-500 text-white mr-2">Save</button>
                <button id="cancelButton" class="px-4 py-2 bg-gray-500 text-white">Cancel</button>
            </div>
        </div>
    </div>


    @section('js')
        <script>
            // Event listener to execute when the DOM is fully loaded
            document.addEventListener('DOMContentLoaded', function() {
        
                // Table Elements
                const searchInput = document.getElementById('searchInput');
                const studentTableBody = document.getElementById('TableBody');
                const prevButton = document.getElementById('prevButton');
                const nextButton = document.getElementById('nextButton');
        
                // Modal Elements
                const openModalButton = document.getElementById('openModalButton');
                const studentModal = document.getElementById('studentModal');
                const modalTitle = document.getElementById('modalTitle');
        
                // Form fields
                const studentName = document.getElementById('studentName');
                const studentSubject = document.getElementById('studentSubject');
                const studentMarks = document.getElementById('studentMarks');
        
                // Modal Buttons
                const saveStudentButton = document.getElementById('saveStudentButton');
                const cancelButton = document.getElementById('cancelButton');
        
                // Error elements
                const nameError = document.getElementById('nameError');
                const subjectError = document.getElementById('subjectError');
                const marksError = document.getElementById('marksError');
        
                // URLs and mode flags
                let isEditMode = false;
                let editStudentId = null;
                let currentPageUrl = "{{ route('students.index') }}";
                let previousPageUrl = null;
        
                // Notification elements
                const successMessage = document.getElementById('successMessage');
                const successText = document.getElementById('successText');
        
                // Function to display a success message
                function showSuccessMessage(message) {
                    successText.textContent = message;
                    successMessage.classList.remove('hidden');
                    setTimeout(() => {
                        successMessage.classList.add('hidden');
                    }, 3000); // Hide after 3 seconds
                }
        
                // Function to get the CSRF token
                function getCSRFToken() {
                    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                }
        
                // Function to fetch students from the server
                function fetchStudents(url) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', url, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            renderStudents(response.data);
                            currentPageUrl = response.next_page_url;
                            previousPageUrl = response.prev_page_url;
                            nextButton.style.display = currentPageUrl ? 'block' : 'none';
                            prevButton.style.display = previousPageUrl ? 'block' : 'none';
                        }
                    };
                    xhr.send();
                }
        
                // Function to render student data in the table
                function renderStudents(students) {
                    studentTableBody.innerHTML = ''; // Clear previous results
        
                    if (!students.length) {
                        const row = document.createElement('tr');
                        row.innerHTML = '<tr> <td colspan="4" class="text-center">No Record Found</td> <tr>';
                        studentTableBody.appendChild(row);
                        return;
                    }
        
                    students.forEach(student => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="border px-4 py-2">${student.name}</td>
                            <td class="border px-4 py-2">${student.subject}</td>
                            <td class="border px-4 py-2">${student.marks}</td>
                            <td class="border px-4 py-2">
                                <button class="editButton px-2 border rounded py-1 bg-blue-500 text-white" data-id="${student.id}">Edit</button>
                                <button class="deleteButton px-2 border rounded py-1 bg-red-500 text-white ml-2" data-id="${student.id}">Delete</button>
                            </td>
                        `;
                        studentTableBody.appendChild(row);
                    });
        
                    document.querySelectorAll('.editButton').forEach(button => {
                        button.addEventListener('click', function() {
                            const studentId = this.getAttribute('data-id');
                            openEditModal(studentId);
                        });
                    });
        
                    document.querySelectorAll('.deleteButton').forEach(button => {
                        button.addEventListener('click', function() {
                            const studentId = this.getAttribute('data-id');
                            confirmDelete(studentId);
                        });
                    });
                }
        
                // Function to confirm deletion of a student
                function confirmDelete(studentId) {
                    if (confirm('Are you sure you want to delete this student?')) {
                        deleteStudent(studentId);
                    }
                }
        
                // Function to delete a student
                function deleteStudent(studentId) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('DELETE', `/students/${studentId}`, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                fetchStudents('/students'); // Refresh the list after deletion
                                showSuccessMessage('Student deleted successfully');
                            } else {
                                alert('Failed to delete the student.');
                            }
                        }
                    };
                    xhr.send();
                }
        
                // Function to validate form fields
                function validateFields() {
                    let isValid = true;
                    nameError.textContent = '';
                    subjectError.textContent = '';
                    marksError.textContent = '';
        
                    if (!studentName.value.trim()) {
                        nameError.textContent = 'Name is required.';
                        isValid = false;
                    }
        
                    if (!studentSubject.value.trim()) {
                        subjectError.textContent = 'Subject is required.';
                        isValid = false;
                    }
        
                    if (!studentMarks.value.trim() || isNaN(studentMarks.value)) {
                        marksError.textContent = 'Marks must be a number.';
                        isValid = false;
                    }
        
                    return isValid;
                }
        
                // Function to open the add student modal
                function openAddModal() {
                    isEditMode = false;
                    modalTitle.textContent = 'Add Student';
                    studentName.value = '';
                    studentSubject.value = '';
                    studentMarks.value = '';
                    nameError.textContent = '';
                    subjectError.textContent = '';
                    marksError.textContent = '';
                    studentModal.classList.remove('hidden');
                }
        
                // Function to open the edit student modal
                function openEditModal(studentId) {
                    isEditMode = true;
                    editStudentId = studentId;
                    modalTitle.textContent = 'Edit Student';
        
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', `/students/${studentId}`, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            const student = JSON.parse(xhr.responseText);
                            studentName.value = student.name;
                            studentSubject.value = student.subject;
                            studentMarks.value = student.marks;
                            studentModal.classList.remove('hidden');
                        }
                    };
                    xhr.send();
                }
        
                // Function to save a student (add or edit)
                function saveStudent() {
                    if (!validateFields()) {
                        return;
                    }
        
                    const name = studentName.value.trim();
                    const subject = studentSubject.value.trim();
                    const marks = parseInt(studentMarks.value.trim());
        
                    if (!name || !subject || isNaN(marks)) {
                        alert('Please fill out all fields.');
                        return;
                    }
        
                    const method = isEditMode ? 'PUT' : 'POST';
                    const url = isEditMode ? `/students/${editStudentId}` : '/students';
                    const xhr = new XMLHttpRequest();
                    xhr.open(method, url, true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 201 || xhr.status === 200) {
                                studentModal.classList.add('hidden');
                                showSuccessMessage(isEditMode ? 'Student updated successfully' : 'Student added successfully');
                                fetchStudents('/students'); // Fetch the first page after adding/updating
                            } else if (xhr.status === 422) { // Validation error
                                const errors = JSON.parse(xhr.responseText).errors;
                                nameError.textContent = errors.name ? errors.name[0] : '';
                                subjectError.textContent = errors.subject ? errors.subject[0] : '';
                                marksError.textContent = errors.marks ? errors.marks[0] : '';
                            }
                        }
                    };
                    xhr.send(JSON.stringify({ name, subject, marks }));
                }
        
                // Event listeners for modal buttons
                openModalButton.addEventListener('click', openAddModal);
                saveStudentButton.addEventListener('click', saveStudent);
                cancelButton.addEventListener('click', function() {
                    studentModal.classList.add('hidden');
                });
        
                // Function to handle search input
                function handleSearch() {
                    const query = searchInput.value;
                    fetchStudents(`/students?search=${query}`);
                }
        
                // Event listeners for pagination buttons
                nextButton.addEventListener('click', function() {
                    if (currentPageUrl) {
                        fetchStudents(currentPageUrl);
                    }
                });
        
                prevButton.addEventListener('click', function() {
                    if (previousPageUrl) {
                        fetchStudents(previousPageUrl);
                    }
                });
        
                // Event listener for search input
                searchInput.addEventListener('input', handleSearch);
        
                // Initial fetch of students
                fetchStudents(currentPageUrl);
            });
        </script>    
    @endsection

</x-app-layout>
