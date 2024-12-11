<?php
session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    header("Location: ../login"); // Redirect to login page if not logged in
    exit();
}
include '../db/connect.php'; // Database connection

// Handle form submission here

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $action = $_POST['action'];

    try {
        if ($action == 'approve') {
            $stmt = $conn->prepare("UPDATE company SET status='Approved' WHERE id=:id");
            $stmt->execute([':id' => $id]);
            $_SESSION['email_status'] = 'Approved';
            $_SESSION['email_faculty_id'] = $email;
            $_SESSION['message'] = "
    <html>
        <head>
            <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
            <style>
                .email-container { padding: 20px; background-color: #f8f9fa; border-radius: 8px; }
                .email-header { background-color: #28a745; color: white; padding: 15px; border-radius: 8px 8px 0 0; }
                .email-footer { text-align: center; margin-top: 20px; font-size: 0.9em; color: #6c757d; }
            </style>
        </head>
        <body>
            <div class='container email-container'>
                <div class='email-header'>
                    <h2>Congratulations!</h2>
                </div>
                <div class='content'>
                    <h5>Dear $email,</h5>
                    <p>Your application has been successfully approved by the director of EcoCart. You can now log in to your account for further processes.</p>
                    <p>Thank you for your patience and understanding.</p>
                </div>
                <div class='email-footer'>
                    <p>Best regards,<br>EcoCart, Bengaluru-560074</p>
                    <p>EcoCart &copy; 2024 - All rights reserved</p>
                </div>
            </div>
        </body>
    </html>";
            @include './email.php';
            exit();
        } elseif ($action == 'reject') {
            $stmt = $conn->prepare("UPDATE company SET status='Rejected' WHERE id=:id");
            $stmt->execute([':id' => $id]);
            $_SESSION['email_status'] = 'Rejected';
            $_SESSION['message'] = "
    <html>
        <head>
            <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
            <style>
                .email-container { padding: 20px; background-color: #f8f9fa; border-radius: 8px; }
                .email-header { background-color: #dc3545; color: white; padding: 15px; border-radius: 8px 8px 0 0; }
                .email-footer { text-align: center; margin-top: 20px; font-size: 0.9em; color: #6c757d; }
            </style>
        </head>
        <body>
            <div class='container email-container'>
                <div class='email-header'>
                    <h2>Oops!</h2>
                </div>
                <div class='content'>
                    <h5>Dear $email,</h5>
                    <p>Your application has been rejected by the director of EcoCart. Contact <strong>8431983441</strong> for further clarifications.</p>
                </div>
                <div class='email-footer'>
                    <p>Best regards,<br>EcoCart, Bengaluru-560074</p>
                    <p>EcoCart &copy; 2024 - All rights reserved</p>
                </div>
            </div>
        </body>
    </html>";
            $_SESSION['email_faculty_id'] = $email;
            @include './email.php';
            exit();
        } elseif ($action == 'delete') {
            $stmt = $conn->prepare("DELETE FROM company WHERE id=:id");
            $stmt->execute([':id' => $id]);
            $_SESSION['email_status'] = 'Deleted';
            $_SESSION['message'] = "
    <html>
        <head>
            <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
            <style>
                .email-container { padding: 20px; background-color: #f8f9fa; border-radius: 8px; }
                .email-header { background-color: #ffc107; color: white; padding: 15px; border-radius: 8px 8px 0 0; }
                .email-footer { text-align: center; margin-top: 20px; font-size: 0.9em; color: #6c757d; }
            </style>
        </head>
        <body>
            <div class='container email-container'>
                <div class='email-header'>
                    <h2>Oops!</h2>
                </div>
                <div class='content'>
                    <h5>Dear $email,</h5>
                    <p>Your application has been deleted by the director of EcoCart. Kindly apply again or contact <strong>8431983441</strong> for further clarifications.</p>
                </div>
                <div class='email-footer'>
                    <p>Best regards,<br>EcoCart, Bengaluru-560074</p>
                    <p>EcoCart &copy; 2024 - All rights reserved</p>
                </div>
            </div>
        </body>
    </html>";
            $_SESSION['email_faculty_id'] = $email;
            @include './email.php';
            exit();
        } 

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="https://www.swizosoft.com/images/swizosoft.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoCart Company Approval Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h1 class="text-center">EcoCart</h1>
        </div>

        <div class="card-body">
            <h2 class="text-center mb-4">EcoCart Company Approval Portal</h2>

            <div class="input-group mb-3">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for names, institutions, etc." class="form-control">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="dataTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Sl. No</th>
                            <th>Logo</th>
                            <th>Organization Name</th>
                            <th>Mobile Number</th>
                            <th>Email</th>
                            <th>Applied At</th>
                            <th>Action</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $query = "SELECT * FROM company WHERE status != 'Approved'";
                            $stmt = $conn->prepare($query);
                            if ($stmt->execute()) {
                                if ($stmt->rowCount() > 0) {
                                    $slNo = 1;
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr id='row-".$row['id']."'>
                                                <td>$slNo</td>
                                                <td> <img src='http://localhost/srinathon/company-registration/uploads/logo/".$row['logo']."' width='50px'></td>
                                                <td>".$row['name']."</td>
                                                <td>".$row['mobileNumber']."</td>
                                                <td>".$row['email']."</td>
                                                <td>".$row['created_at']."</td>
                                                <td>
                                                    <button class='btn btn-success btn-sm mt-1' onclick='updateApplicationStatus(".$row['id'].", \"approve\", \"".$row['email']."\")'><i class='fas fa-check'></i> Approve</button>
                                                    <button class='btn btn-danger btn-sm mt-1' onclick='updateApplicationStatus(".$row['id'].", \"reject\", \"".$row['email']."\")'><i class='fas fa-times'></i> Reject</button>
                                                    <button class='btn btn-danger btn-sm mt-1' onclick='updateApplicationStatus(".$row['id'].", \"delete\", \"".$row['email']."\")'><i class='fas fa-trash'></i> Delete</button>
                                                </td>
                                                <td>".$row['status']."</td>
                                              </tr>";
                                        $slNo++;
                                    }
                                } else {
                                    echo "<tr><td colspan='12' class='text-center'>No applications found</td></tr>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal for Viewing Documents -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="documentModalLabel">Document Viewer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <iframe id="documentIframe" src="" frameborder="0" style="width:100%; height:500px;"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
    function openDocument(url) {
        // Set the iframe source to the document URL
        document.getElementById("documentIframe").src = url;
        // Show the modal
        var modal = new bootstrap.Modal(document.getElementById("documentModal"));
        modal.show();
    }

    function updateApplicationStatus(id, action, email) {
        if (confirm('Are you sure you want to ' + action + ' this application?')) {
            const form = $('<form>', {
                'action': '',
                'method': 'POST',
                'style': 'display: none;'
            }).append($('<input>', {'name': 'id', 'value': id}))
              .append($('<input>', {'name': 'email', 'value': email}))
              .append($('<input>', {'name': 'action', 'value': action}));
            $('body').append(form);
            form.submit();
        }
    }

    function searchTable() {
        let input = document.getElementById("searchInput");
        let filter = input.value.toLowerCase();
        let rows = document.getElementById("dataTable").getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) {
            let cells = rows[i].getElementsByTagName("td");
            let match = false;

            for (let j = 0; j < cells.length; j++) {
                let cell = cells[j];
                if (cell && cell.textContent.toLowerCase().includes(filter)) {
                    match = true;
                    break;
                }
            }
            rows[i].style.display = match ? "" : "none";
        }
    }
    function updateDueDate(event, id, email) {
    event.preventDefault(); // Prevent form submission

    // Get the due date from the input field
    const dueDate = document.querySelector(`input[name='due_date']`).value;

    if (!dueDate) {
        alert('Please select a valid due date.');
        return;
    }

    $.ajax({
        url: '', // Same page URL to handle the request
        type: 'POST',
        data: {
            id: id,
            action: 'update_due_date',
            due_date: dueDate
        },
        success: function(response) {
            alert('Due date updated successfully.');
            // Optionally, refresh the row data if needed
        },
        error: function() {
            alert('There was an error updating the due date.');
        }
    });
}

</script>

</body>
</html>
