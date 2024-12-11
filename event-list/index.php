<?php
session_start();
if (empty($_SESSION['id']) || empty($_SESSION['mobile']) || empty($_SESSION['email'])) {
    header('location: ../login');
    exit();
}

include '../db/connect.php'; // Include database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;

    try {
        if ($action === 'delete' && $id !== null) {
            $stmt = $conn->prepare("DELETE FROM events WHERE id = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Event deleted successfully.']);
            exit();
        } elseif ($action === 'update' && $id !== null) {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $totalSeats = $_POST['total_seats'] ?? '';
            $location = $_POST['location'] ?? '';
            $stipend = $_POST['stipend'] ?? '';
            $fromDate = $_POST['from_date'] ?? '';
            $toDate = $_POST['to_date'] ?? '';
            $workingTimeFrom = $_POST['working_time_from'] ?? '';
            $workingTimeTo = $_POST['working_time_to'] ?? '';

            // Validate required fields
            if (empty($title) || empty($description) || empty($totalSeats) || empty($location) || empty($stipend) || empty($fromDate) || empty($toDate) || empty($workingTimeFrom) || empty($workingTimeTo)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required.']);
                exit();
            }

            $stmt = $conn->prepare("UPDATE events SET 
                title = :title, 
                description = :description, 
                total_seats = :total_seats, 
                location = :location, 
                stipend = :stipend, 
                from_date = :from_date, 
                to_date = :to_date, 
                working_time_from = :working_time_from, 
                working_time_to = :working_time_to 
                WHERE id = :id");

            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':total_seats' => $totalSeats,
                ':location' => $location,
                ':stipend' => $stipend,
                ':from_date' => $fromDate,
                ':to_date' => $toDate,
                ':working_time_from' => $workingTimeFrom,
                ':working_time_to' => $workingTimeTo,
                ':id' => $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Event updated successfully.']);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://www.swizosoft.com/images/swizosoft.jpg" type="image/x-icon">
    <title>Voluntrix Admin's Events List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h1 class="text-center">Voluntrix</h1>
        </div>
        <div class="card-body">
            <h2 class="text-center mb-4">Voluntrix Admin's Events List</h2>
            <div class="input-group mb-3">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search..." class="form-control">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="dataTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Sl. No</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Total Seats</th>
                            <th>Location</th>
                            <th>Stipend</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Working Time (From)</th>
                            <th>Working Time (To)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $companyId = $_SESSION['id'];
                            $query = "SELECT * FROM events WHERE company_id = :company_id";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([':company_id' => $companyId]);

                            $slNo = 1;
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr id='row-" . htmlspecialchars($row['id']) . "'>
                                    <td>{$slNo}</td>
                                    <td contenteditable='true' data-column='title'>" . htmlspecialchars($row['title']) . "</td>
                                    <td contenteditable='true' data-column='description'>" . htmlspecialchars($row['description']) . "</td>
                                    <td contenteditable='true' data-column='total_seats'>" . htmlspecialchars($row['total_seats']) . "</td>
                                    <td contenteditable='true' data-column='location'>" . htmlspecialchars($row['location']) . "</td>
                                    <td contenteditable='true' data-column='stipend'>" . htmlspecialchars($row['stipend']) . "</td>
                                    <td contenteditable='true' data-column='from_date'>" . htmlspecialchars($row['from_date']) . "</td>
                                    <td contenteditable='true' data-column='to_date'>" . htmlspecialchars($row['to_date']) . "</td>
                                    <td contenteditable='true' data-column='working_time_from'>" . htmlspecialchars($row['working_time_from']) . "</td>
                                    <td contenteditable='true' data-column='working_time_to'>" . htmlspecialchars($row['working_time_to']) . "</td>
                                    <td>
                                        <button class='btn btn-success btn-sm mt-1' onclick='saveChanges(" . htmlspecialchars($row['id']) . ")'><i class='fas fa-save'></i> Save</button>
                                        <button class='btn btn-danger btn-sm mt-1' onclick='deleteEvent(" . htmlspecialchars($row['id']) . ")'><i class='fas fa-trash'></i> Delete</button>
                                    </td>
                                  </tr>";
                                $slNo++;
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='11' class='text-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to delete an event
    function deleteEvent(eventId) {
        if (confirm('Are you sure you want to delete this event?')) {
            $.ajax({
                url: '',
                method: 'POST',
                data: { id: eventId, action: 'delete' },
                success: function (response) {
                    const res = JSON.parse(response);
                    if (res.success) {
                        alert(res.message);
                        $('#row-' + eventId).remove();
                    } else {
                        alert(res.message);
                    }
                },
                error: function () {
                    alert('Error deleting event.');
                }
            });
        }
    }

    // Function to save changes to an event
    function saveChanges(eventId) {
        const row = $('#row-' + eventId);
        const title = row.find('td[data-column="title"]').text();
        const description = row.find('td[data-column="description"]').text();
        const totalSeats = row.find('td[data-column="total_seats"]').text();
        const location = row.find('td[data-column="location"]').text();
        const stipend = row.find('td[data-column="stipend"]').text();
        const fromDate = row.find('td[data-column="from_date"]').text();
        const toDate = row.find('td[data-column="to_date"]').text();
        const workingTimeFrom = row.find('td[data-column="working_time_from"]').text();
        const workingTimeTo = row.find('td[data-column="working_time_to"]').text();

        // Validate required fields
        if (!title || !description || !totalSeats || !location || !stipend || !fromDate || !toDate || !workingTimeFrom || !workingTimeTo) {
            alert('All fields are required.');
            return;
        }

        $.ajax({
            url: '',
            method: 'POST',
            data: {
                id: eventId,
                action: 'update',
                title: title,
                description: description,
                total_seats: totalSeats,
                location: location,
                stipend: stipend,
                from_date: fromDate,
                to_date: toDate,
                working_time_from: workingTimeFrom,
                working_time_to: workingTimeTo
            },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.success) {
                    alert(res.message);
                } else {
                    alert(res.message);
                }
            },
            error: function () {
                alert('Error updating event.');
            }
        });
    }

    // Function to search the table for events
    function searchTable() {
        let input = document.getElementById('searchInput');
        let filter = input.value.toLowerCase();
        let rows = document.getElementById('dataTable').getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            let cells = rows[i].getElementsByTagName('td');
            let match = false;

            for (let j = 0; j < cells.length - 1; j++) {
                if (cells[j].textContent.toLowerCase().includes(filter)) {
                    match = true;
                    break;
                }
            }
            rows[i].style.display = match ? '' : 'none';
        }
    }
</script>

</body>
</html>
