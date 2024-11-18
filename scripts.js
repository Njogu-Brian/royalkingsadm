document.addEventListener("DOMContentLoaded", function() {
    let currentSection = 0;
    const sections = document.querySelectorAll('.section');
    let childrenData = []; // Array to hold information for multiple children

    function showSection(index) {
        sections.forEach((section, i) => {
            section.style.display = (i === index) ? 'block' : 'none';
        });
    }

    showSection(currentSection);

    document.getElementById('nextSection1').addEventListener('click', function() {
        if (validateSection(currentSection)) {
            currentSection++;
            showSection(currentSection);
        }
    });

    document.getElementById('prevSection2').addEventListener('click', function() {
        currentSection--;
        showSection(currentSection);
    });

    document.getElementById('nextSection2').addEventListener('click', function() {
        if (validateSection(currentSection)) {
            currentSection++;
            showSection(currentSection);
        }
    });

    document.getElementById('prevSection3').addEventListener('click', function() {
        currentSection--;
        showSection(currentSection);
    });

    document.getElementById('nextSection3').addEventListener('click', function() {
        if (validateSection(currentSection)) {
            currentSection++;
            showSection(currentSection);
        }
    });

    document.getElementById('prevSection4').addEventListener('click', function() {
        currentSection--;
        showSection(currentSection);
    });

    document.getElementById('addSibling').addEventListener('click', function() {
        if (validateSection(3)) { // Validate Section 4 before adding another child
            storeCurrentChildData();
            resetForm(); // Reset all fields for new child
            showSection(0); // Show Section 1
        }
    });

    function validateSection(index) {
        const inputs = sections[index].querySelectorAll('input[required], select[required]');
        let allFilled = true;

        inputs.forEach(function(input) {
            if (!input.value.trim()) {
                input.style.border = '2px solid red'; // Highlight in red
                allFilled = false; // Mark that not all fields are filled
            } else {
                input.style.border = ''; // Reset border if filled
            }
        });

        if (!allFilled) {
            alert("Please fill all required fields.");
        }
        return allFilled;
    }

    function storeCurrentChildData() {
        // Gather data from all sections to store for the current child
        const childData = {
            section1: {
                class: document.getElementById('class').value,
                surname: document.getElementById('surname').value,
                firstName: document.getElementById('firstName').value,
                middleName: document.getElementById('middleName').value,
                gender: document.getElementById('gender').value,
                dob: document.getElementById('dob').value,
                passportPhoto: document.getElementById('passportPhoto').files[0], // For upload later
                birthCertificate: document.getElementById('birthCertificate').files[0] // For upload later
            },
            section2: {
                address: document.getElementById('address').value,
                transport: document.getElementById('transport').value,
                religion: document.getElementById('religion').value,
            },
            section3: {
                motherName: document.getElementById('motherName').value,
                motherPhone: document.getElementById('motherPhone').value,
                motherEmail: document.getElementById('motherEmail').value,
                motherID: document.getElementById('motherID').value,
                fatherName: document.getElementById('fatherName').value,
                fatherPhone: document.getElementById('fatherPhone').value,
                fatherEmail: document.getElementById('fatherEmail').value,
                fatherID: document.getElementById('fatherID').value,
                guardianName: document.getElementById('guardianName').value,
                guardianPhone: document.getElementById('guardianPhone').value,
                guardianEmail: document.getElementById('guardianEmail').value,
                guardianID: document.getElementById('guardianID').value,
                nomineeName: document.getElementById('nomineeName').value,
                nomineeContact: document.getElementById('nomineeContact').value,
            },
            section4: {
                previousSchool: document.getElementById('previousSchool').value,
                assessmentNo: document.getElementById('assessmentNo').value,
                reasonLeaving: document.getElementById('reasonLeaving').value,
                reasonChoosing: document.getElementById('reasonChoosing').value,
            }
        };
        childrenData.push(childData); // Store current child data into the array
        console.log(childrenData); // Debug: view stored data
    }

    function resetForm() {
        // Clear fields for all sections to allow new child data entry
        sections.forEach(section => {
            const inputs = section.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.value = ''; // Reset input value
                input.style.border = ''; // Reset border
            });
        });
    }

    // Dark mode toggle
    const toggleButton = document.createElement('button');
    toggleButton.innerText = 'Toggle Dark Mode';
    toggleButton.style.margin = '15px auto';
    toggleButton.classList.add('bounce');
    document.body.insertBefore(toggleButton, document.body.firstChild);

    toggleButton.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const container = document.querySelector('.container');
        container.classList.toggle('dark-mode');
    });

    // Add event listener for the addChildButton
    document.getElementById('addChildButton').addEventListener('click', function() {
        if (validateSection(currentSection)) {
            storeCurrentChildData();
            resetForm();
            showSection(0); // Show Section 1 for new child
        }
    });
});