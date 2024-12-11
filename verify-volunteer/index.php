<?php
session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    header("Location: ../login");
    exit();
}

include '../db/connect.php'; // Database connection

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $applicationId = $_POST['application_id'];
    $volunteerId = $_POST['volunteer_id'];
    $email = $_POST['email'];

    try {
        if ($action === 'confirm') {
            // Confirm volunteer
            $stmt = $conn->prepare("UPDATE applicants SET status='Confirmed' WHERE id=:application_id");
            $stmt->execute([':application_id' => $applicationId]);

            // Reduce remaining seats in the events table
            $stmt = $conn->prepare("UPDATE events SET remaining_seats = remaining_seats - 1 WHERE id = 
                (SELECT event_id FROM applicants WHERE id = :application_id)");
            $stmt->execute([':application_id' => $applicationId]);

            $_SESSION['message'] = "Volunteer confirmed successfully!";
        } elseif ($action === 'call') {
            $phone = $_POST['mobile'];

            // Redirect to the dialer with the phone number
            header("Location: tel:$phone");
            exit();
        } elseif ($action === 'delete') {
            // Retrieve the event_id before deleting the applicant
            $stmt = $conn->prepare("SELECT event_id FROM applicants WHERE id = :application_id");
            $stmt->execute([':application_id' => $applicationId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($event) {
                $eventId = $event['event_id'];

                // Delete the applicant
                $stmt = $conn->prepare("DELETE FROM applicants WHERE id = :application_id");
                $stmt->execute([':application_id' => $applicationId]);

                // Increase remaining seats in the events table
                $stmt = $conn->prepare("UPDATE events SET remaining_seats = remaining_seats + 1 WHERE id = :event_id");
                $stmt->execute([':event_id' => $eventId]);

                $_SESSION['message'] = "Application deleted and seat released successfully!";
            } else {
                $_SESSION['error'] = "Failed to find the application!";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Confirmation Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h1 class="text-center">Volunteer Confirmation Portal</h1>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <h2 class="mb-4 text-center">Manage Volunteer Applications</h2>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                    <tr>
                        <th>Sl. No</th>
                        <th>Volunteer Name</th>
                        <th>Event</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    try {
                        $query = "SELECT applicants.id AS application_id, 
                                         applicants.status AS application_status, 
                                         volunteers.name AS volunteer_name, 
                                         volunteers.email AS volunteer_email, 
                                         volunteers.mobile AS volunteer_mobile, 
                                         volunteers.id AS volunteer_id, 
                                         events.title AS event_title 
                                  FROM applicants 
                                  JOIN volunteers ON applicants.user_id = volunteers.id 
                                  JOIN events ON applicants.event_id = events.id";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();

                        if ($stmt->rowCount() > 0) {
                            $slNo = 1;
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>
                                        <td>$slNo</td>
                                        <td>{$row['volunteer_name']}</td>
                                        <td>{$row['event_title']}</td>
                                        <td>{$row['volunteer_email']}</td>
                                        <td>{$row['volunteer_mobile']}</td>
                                        <td>{$row['application_status']}</td>
                                        <td>";
                                if ($row['application_status'] === 'Applied') {
                                    echo "<form action='' method='POST' class='d-inline'>
                                            <input type='hidden' name='action' value='confirm'>
                                            <input type='hidden' name='application_id' value='{$row['application_id']}'>
                                            <input type='hidden' name='volunteer_id' value='{$row['volunteer_id']}'>
                                            <input type='hidden' name='email' value='{$row['volunteer_email']}'>
                                            <button class='btn btn-success btn-sm'>Confirm</button>
                                          </form>";
                                }
                                echo "<form action='' method='POST' class='d-inline'>
                                        <input type='hidden' name='action' value='call'>
                                        <input type='hidden' name='mobile' value='{$row['volunteer_mobile']}'>
                                        <button class='btn btn-info btn-sm'>Call</button>
                                      </form>
                                      <form action='' method='POST' class='d-inline'>
                                        <input type='hidden' name='action' value='delete'>
                                        <input type='hidden' name='application_id' value='{$row['application_id']}'>
                                        <button class='btn btn-danger btn-sm'>Delete</button>
                                      </form>";
                                echo "</td>
                                    </tr>";
                                $slNo++;
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No applications found</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='7' class='text-danger'>Error: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
