<?php
session_start();
include 'layout/header.php';
include 'models/Models.php';
include 'models/Admin.php';

if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

$admin = new Admin('admins');
$user = $_SESSION['user_login'];
$admin = $admin->where(['username' => $user])->get()[0];
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/farmers.css?v=4">
<link rel="stylesheet" href="css/farmer.css">
</head>
<body>
<?php include 'layout/sidebar.php'; ?>
<?php include 'layout/nav.php'; ?>
    <div class="container">
    
        <h1 class="mt-5">Upload Images</h1>
        <form id="upload-form" enctype="multipart/form-data" method="POST">
            <div class="form-group">
                <label for="class_name">Class Name:</label>
                <input type="text" class="form-control" id="class_name" name="class_name" required>
            </div>
            <div class="form-group">
                <label for="files">Select images:</label>
                <input type="file" class="form-control-file" id="files" name="files[]" multiple required>
                <small class="form-text text-muted">Maximum of 4,000 images allowed.</small> <!-- Max pictures indicator -->
            </div>
            <div id="preview" class="mt-3">
                <!-- Preview of selected images will be displayed here -->
            </div>
            <button type="submit" class="btn btn-primary mt-3">Upload</button>
        </form>

        <h2 class="mt-5">Train Model</h2>
        <button id="train-btn" class="btn btn-success">Train Model</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        // Handle file input change event to preview images
        document.getElementById('files').addEventListener('change', function(event) {
            const preview = document.getElementById('preview');
            preview.innerHTML = ''; // Clear previous previews
            const files = event.target.files;

            if (files.length > 4000) { // Check if file limit is exceeded
            alert('You can only upload a maximum of 4,000 images.');
            event.target.value = ''; // Clear the selected files
            return;
        }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px'; // Set a width for the preview
                    img.style.margin = '5px';
                    preview.appendChild(img);
                };
                
                reader.readAsDataURL(file); // Convert file to base64
            }
        });

        // Handle the training functionality without any form input
        document.getElementById('train-btn').addEventListener('click', function () {
            // Send AJAX request to Flask backend for training
            fetch('https://desktop-3mj7q7f.tail98551e.ts.net/web_train', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message); // Display the training message returned by Flask
                } else {
                    alert('Error: ' + (data.error || 'Unknown error occurred.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $className = $_POST['class_name'];

        // Check if the class_name is set and if files are uploaded
        if (!empty($className) && isset($_FILES['files']['tmp_name'])) {
            // Prepare the POST request for the Flask endpoint
            $url = 'https://desktop-3mj7q7f.tail98551e.ts.net/admin_upload';

            $postFields = ['class_name' => $className]; // Initialize post fields

            // Loop through each uploaded file
            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                $file = curl_file_create($tmpName, $_FILES['files']['type'][$key], $_FILES['files']['name'][$key]);
                $postFields['files[]'] = $file; // Add file to the POST fields
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
    
            // Handle the response
            if ($httpCode == 200) {
                echo "<script>alert('Files uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Error during upload. Response: $response');</script>";
            }
        } else {
            echo "<script>alert('Please select files to upload.');</script>";
        }
    }    
    ?>

<?php include 'layout/footer.php'; ?>
