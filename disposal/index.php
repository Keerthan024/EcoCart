<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plastic Disposal System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        #banner {
            position: relative;
            width: 100%;
            height: 400px;
            background: url('img.jpg') no-repeat center center/cover;
            color: white;
            text-align: center;
            padding: 20px;
        }

        #banner h1 {
            font-size: 3rem;
            margin-top: 100px;
        }

        #scanner-section {
            margin: auto;
            max-width: 500px;
        }
    </style>
</head>
<body class="bg-light">
    <header>
        <div id="banner">
            <h1>Plastic Disposal System</h1>
        </div>
    </header>

    <main class="container my-5">
        <!-- QR Scanner Section -->
        <section id="scanner-section" class="text-center">
            <h2 class="h4 text-secondary mb-3">QR Scanner</h2>
            <div id="qr-reader" class="border rounded shadow mx-auto" style="width: 300px; height: 300px;"></div>
            <h3 class="mt-3">Scanned ID: <span id="scanned-id">None</span></h3>
            <button id="fetch-data" class="btn btn-success mt-3">Fetch Data <i class="bi bi-qr-code-scan"></i></button>
        </section>

        <!-- Product Actions Section -->
        <section id="product-actions-section" hidden class="text-center my-5">
            <h3>Product Details</h3>
            <p id="product-details"></p>
            <button id="penalty-btn" class="btn btn-warning">Apply Penalty</button>
            <button id="dispose-btn" class="btn btn-danger">Dispose</button>
        </section>
    </main>

    <footer class="text-center py-3">
        <p>&copy; 2024 Plastic Disposal System</p>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- QR Scanner Library -->
    <script src="lib/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
    const qrReader = new Html5Qrcode("qr-reader");
    const scannedIdSpan = document.getElementById("scanned-id");
    const fetchDataBtn = document.getElementById("fetch-data");
    const productActionsSection = document.getElementById("product-actions-section");
    const productDetails = document.getElementById("product-details");
    const penaltyBtn = document.getElementById("penalty-btn");
    const disposeBtn = document.getElementById("dispose-btn");

    let scannedId = null;

    // Initialize QR Scanner
    qrReader
        .start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                scannedId = decodedText;
                scannedIdSpan.textContent = decodedText;
            },
            (errorMessage) => {
                console.warn(errorMessage);
            }
        )
        .catch((err) => console.error("QR Scanner Error: ", err));

    // Fetch Data
    fetchDataBtn.addEventListener("click", () => {
        if (!scannedId) {
            alert("No QR Code Scanned!");
            return;
        }
        fetch("fetch_product.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ qr_id: scannedId }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    productDetails.textContent = `ID: ${data.product.id}, Description: ${data.product.description}`;
                    productActionsSection.hidden = false;
                } else {
                    alert(data.message);
                }
            })
            .catch((error) => console.error("Error fetching data: ", error));
    });

    // Apply Penalty
    penaltyBtn.addEventListener("click", () => {
        const accountNumber = prompt("Enter Account Number:");
        const password = prompt("Enter Password:");
        const penaltyAmount = prompt("Enter Penalty Amount:");

        if (!accountNumber || !password || !penaltyAmount) {
            alert("All fields are required!");
            return;
        }

        fetch("apply_penalty.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
        qr_id: scannedId, // Ensure `scannedId` is properly assigned before calling this
        account_number: accountNumber, // Ensure `accountNumber` is defined
        password: password, // Ensure `password` is defined
        penalty_amount: penaltyAmount, // Ensure `penaltyAmount` is defined
    }),
})
    .then((response) => response.json())
    .then((data) => {
        alert(data.message);
        if (data.success) {
            // Perform any additional actions if penalty applied successfully
            productActionsSection.hidden = true;
        }
    })
    .catch((error) => console.error("Error applying penalty:", error));
    });

    // Dispose Product
    disposeBtn.addEventListener("click", () => {
        fetch("dispose_product.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ qr_id: scannedId }),
        })
            .then((response) => response.json())
            .then((data) => {
                alert(data.message);
                if (data.success) productActionsSection.hidden = true;
            })
            .catch((error) => console.error("Error disposing product: ", error));
    });
});

    </script>
</body>
</html>
