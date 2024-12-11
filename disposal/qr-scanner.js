function startQRScanner(videoElement, callback) {
    console.log("Starting QR Scanner...");
    // Simulate QR code scanning.
    setTimeout(() => {
        const dummyQR = "12345"; // Simulated scanned ID.
        callback(dummyQR);
    }, 3000);
}

function stopQRScanner() {
    console.log("QR Scanner stopped.");
}
