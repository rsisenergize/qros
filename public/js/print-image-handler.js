// Print Image Handler - Handles automatic image generation for KOT and Order printing
// This file should be included in your main layout or POS view
// Compatible with Desktop and Mobile browsers (Chrome, Safari, Firefox, Edge)

// Detect if running on mobile device
if (typeof window.isMobileDevice === "undefined") {
    window.isMobileDevice =
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
            navigator.userAgent
        );
}

// Detect if running on iOS Safari
if (typeof window.isIOSSafari === "undefined") {
    window.isIOSSafari =
        /iPhone|iPad|iPod/.test(navigator.userAgent) &&
        /Safari/.test(navigator.userAgent) &&
        !/Chrome/.test(navigator.userAgent);
}

// Check if html-to-image library is available
if (typeof window.htmlToImageLoaded === "undefined") {
    window.htmlToImageLoaded = typeof htmlToImage !== "undefined";
    if (!window.htmlToImageLoaded) {
        console.warn(
            "⚠️  html-to-image library not loaded. Print image generation may not work."
        );
    }
}

// Track if capture is already in progress to prevent multiple requests

if (typeof window.printCaptureInProgress === "undefined") {
    window.printCaptureInProgress = false;
}

// Separate flags for KOT and Order image generation
if (typeof window.kotImageInProgress === "undefined") {
    window.kotImageInProgress = false;
}

if (typeof window.orderImageInProgress === "undefined") {
    window.orderImageInProgress = false;
}

// Queue for handling multiple KOT image generations
if (typeof window.kotImageQueue === "undefined") {
    window.kotImageQueue = [];
}

// Queue for handling multiple Order image generations
if (typeof window.orderImageQueue === "undefined") {
    window.orderImageQueue = [];
}

// Listen for Livewire events when the page loads
document.addEventListener("livewire:init", () => {
    // Listen for KOT image save event
    Livewire.on("saveKotImageFromPrint", (event) => {
        // Add to queue to handle multiple KOTs sequentially
        window.kotImageQueue.push({
            kotId: event[0],
            kotPlaceId: event[1],
            content: event[2],
        });

        // Process queue if not already processing
        if (!window.kotImageInProgress) {
            processKotImageQueue();
        }
    });

    // Listen for Order image save event
    Livewire.on("saveOrderImageFromPrint", (event) => {
        // Add to queue to handle multiple Orders sequentially
        window.orderImageQueue.push({
            orderId: event[0],
            content: event[1],
        });

        // Process queue if not already processing
        if (!window.orderImageInProgress) {
            processOrderImageQueue();
        }
    });

    // Listen for Report image save event (X/Z Reports)
    Livewire.on("saveReportImageFromPrint", (event) => {
        const sessionId = event[0];
        const content = event[1];
        const reportType = event[2]; // 'x-report' | 'z-report'
        saveReportImageFromPrint(sessionId, content, reportType);
    });
});

// Fallback: also bind outside livewire:init in case of timing issues
if (window.Livewire && typeof window.Livewire.on === "function") {
    try {
        window.Livewire.on("saveReportImageFromPrint", (event) => {
            const sessionId = event[0];
            const content = event[1];
            const reportType = event[2];
            console.log(
                "[PrintImageHandler fallback] saveReportImageFromPrint:",
                { sessionId, reportType }
            );
            saveReportImageFromPrint(sessionId, content, reportType);
        });
    } catch (e) {
        console.warn("[PrintImageHandler] fallback bind failed", e);
    }
}

/**
 * Process KOT image queue sequentially
 */
async function processKotImageQueue() {
    if (window.kotImageQueue.length === 0) {
        return;
    }

    const item = window.kotImageQueue.shift();
    console.log("Processing KOT image from queue:", item.kotId);

    await saveKotImageFromPrint(item.kotId, item.kotPlaceId, item.content);

    // Process next item in queue after a small delay
    if (window.kotImageQueue.length > 0) {
        setTimeout(() => {
            processKotImageQueue();
        }, 200); // 200ms delay between KOTs
    }
}

/**
 * Process Order image queue sequentially
 */
async function processOrderImageQueue() {
    if (window.orderImageQueue.length === 0) {
        return;
    }

    const item = window.orderImageQueue.shift();
    console.log("Processing Order image from queue:", item.orderId);

    await saveOrderImageFromPrint(item.orderId, item.content);

    // Process next item in queue after a small delay
    if (window.orderImageQueue.length > 0) {
        setTimeout(() => {
            processOrderImageQueue();
        }, 200); // 200ms delay between Orders
    }
}

/**
 * Save KOT image using html-to-image
 */
async function saveKotImageFromPrint(kotId, kotPlaceId, content) {
    // Check if html-to-image library is available
    if (typeof htmlToImage === "undefined") {
        console.error(
            "❌ html-to-image library not loaded. Cannot generate KOT image. Please ensure the library is included in your page."
        );
        return;
    }

    // Prevent multiple captures
    if (window.kotImageInProgress) {
        console.log("KOT image capture already in progress, skipping...");
        return;
    }

    try {
        window.kotImageInProgress = true;
        console.log("Starting KOT image capture for KOT ID:", kotId);
        console.log(
            "Device:",
            window.isMobileDevice ? "📱 Mobile" : "🖥️ Desktop"
        );

        // Create a hidden iframe for the KOT content
        const iframe = document.createElement("iframe");
        iframe.style.position = "absolute";
        iframe.style.left = "-9999px";
        iframe.style.top = "0";
        iframe.style.width = "auto"; // Let content determine natural width
        iframe.style.maxWidth = "576px"; // 80mm thermal printer standard
        iframe.style.height = "auto";
        iframe.style.border = "none";
        iframe.style.background = "#fff";

        // Disable print functionality in iframe
        iframe.setAttribute("sandbox", "allow-same-origin allow-scripts");

        document.body.appendChild(iframe);

        // Write the content to the iframe
        const iframeDoc =
            iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(content);
        iframeDoc.close();

        // Disable print() inside sandboxed iframe to avoid allow-modals warning
        try {
            if (
                iframe.contentWindow &&
                typeof iframe.contentWindow.print === "function"
            ) {
                iframe.contentWindow.print = function () {
                    /* no-op for image capture */
                };
            }
        } catch (e) {}

        // Wait for iframe to load and fonts to be ready
        await new Promise((resolve) => {
            iframe.onload = () => {
                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(resolve);
                } else {
                    resolve();
                }
            };
        });

        // Get the actual content width from iframe
        const iframeBody = iframeDoc.body;

        // Let content determine its natural width (like Browsershot fullWidth)
        iframeBody.style.width = "auto";
        iframeBody.style.maxWidth = "576px";
        iframeBody.style.overflow = "visible";
        iframeBody.style.display = "inline-block";

        const contentWidth = iframeBody.scrollWidth;
        const actualWidth = Math.min(contentWidth, 576); // Cap at 576px (80mm standard)

        // Mobile-specific optimizations
        const pixelRatio = window.isMobileDevice ? 1.5 : 2; // Lower quality on mobile to reduce memory usage
        const timeout = window.isMobileDevice ? 10000 : 5000; // Longer timeout for mobile devices

        console.log(
            "Generating KOT image - Device:",
            window.isMobileDevice ? "Mobile" : "Desktop",
            "Pixel Ratio:",
            pixelRatio
        );

        // Generate PNG using html-to-image from iframe body with timeout
        const dataUrl = await Promise.race([
            htmlToImage.toPng(iframeBody, {
                canvasWidth: actualWidth,
                backgroundColor: "#fff",
                pixelRatio: pixelRatio,
                cacheBust: true,
                width: actualWidth,
                height: undefined, // Let height be calculated automatically
                skipFonts: window.isIOSSafari, // Skip font loading on iOS Safari to prevent timeout
            }),
            new Promise((_, reject) =>
                setTimeout(
                    () => reject(new Error("KOT image generation timeout")),
                    timeout
                )
            ),
        ]);

        // Save to server
        console.log("Sending request to /kot/png");

        // Get CSRF token from meta tag
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        const res = await fetch("/kot/png", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                image_base64: dataUrl,
                kot_id: kotId,
                width: actualWidth,
                mono: true, // High-contrast B/W for thermal printing
            }),
        }).catch((fetchError) => {
            console.error(
                "❌ Network error while uploading KOT image:",
                fetchError
            );
            if (window.isMobileDevice) {
                console.warn(
                    "📱 Mobile network issue detected. Please check your internet connection and try again."
                );
            }
            throw fetchError;
        });

        const responseText = await res.text();

        if (!res.ok) {
            console.error("❌ HTTP Error:", res.status, res.statusText);
            console.error("Response text:", responseText);
            if (window.isMobileDevice) {
                console.warn(
                    "📱 Upload failed on mobile. Status:",
                    res.status,
                    "- Please try again or use a stable connection."
                );
            }
            return;
        }

        let result;
        try {
            result = JSON.parse(responseText);
            if (result.ok) {
                console.log("KOT image saved successfully:", result.url);
            } else {
                console.error("Failed to save KOT image:", result.message);
            }
        } catch (error) {
            console.error("Failed to parse response as JSON:", error);
            console.error("Response text:", responseText);
        }

        // Clean up
        try {
            document.body.removeChild(iframe);
        } catch (error) {
            console.error("Error removing iframe:", error);
        }
        window.kotImageInProgress = false;
    } catch (error) {
        console.error("Error saving KOT image:", error);

        // Provide user-friendly error messages for mobile
        if (window.isMobileDevice) {
            if (error.message && error.message.includes("timeout")) {
                console.warn(
                    "KOT image generation timed out on mobile device. This may be due to device memory or processing limitations."
                );
            } else if (
                error.name === "QuotaExceededError" ||
                error.message?.includes("memory")
            ) {
                console.warn(
                    "KOT image generation failed due to memory constraints on mobile device. Try closing other apps."
                );
            }
        }

        window.kotImageInProgress = false;
    }
}

/**
 * Save Order image using html-to-image
 */
async function saveOrderImageFromPrint(orderId, content) {
    // Check if html-to-image library is available
    if (typeof htmlToImage === "undefined") {
        console.error(
            "❌ html-to-image library not loaded. Cannot generate Order image. Please ensure the library is included in your page."
        );
        return;
    }

    // Prevent multiple captures
    if (window.orderImageInProgress) {
        console.log("Order image capture already in progress, skipping...");
        return;
    }

    try {
        window.orderImageInProgress = true;
        console.log("Starting Order image capture for Order ID:", orderId);
        console.log(
            "Device:",
            window.isMobileDevice ? "📱 Mobile" : "🖥️ Desktop"
        );

        // Create a hidden iframe for the Order content
        const iframe = document.createElement("iframe");
        iframe.style.position = "absolute";
        iframe.style.left = "-9999px";
        iframe.style.top = "0";
        iframe.style.width = "auto"; // Let content determine natural width
        iframe.style.maxWidth = "576px"; // 80mm thermal printer standard
        iframe.style.height = "auto";
        iframe.style.border = "none";
        iframe.style.background = "#fff";

        // Disable print functionality in iframe
        iframe.setAttribute("sandbox", "allow-same-origin allow-scripts");

        document.body.appendChild(iframe);

        // Write the content to the iframe
        const iframeDoc =
            iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(content);
        iframeDoc.close();

        // Disable print() inside sandboxed iframe to avoid allow-modals warning
        try {
            if (
                iframe.contentWindow &&
                typeof iframe.contentWindow.print === "function"
            ) {
                iframe.contentWindow.print = function () {
                    /* no-op for image capture */
                };
            }
        } catch (e) {}

        // Wait for iframe to load and fonts to be ready
        await new Promise((resolve) => {
            iframe.onload = () => {
                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(resolve);
                } else {
                    resolve();
                }
            };
        });

        // Get the actual content width from iframe
        const iframeBody = iframeDoc.body;

        // Let content determine its natural width (like Browsershot fullWidth)
        iframeBody.style.width = "auto";
        iframeBody.style.maxWidth = "576px";
        iframeBody.style.overflow = "visible";
        iframeBody.style.display = "inline-block";

        const contentWidth = iframeBody.scrollWidth;
        const actualWidth = Math.min(contentWidth, 576); // Cap at 576px (80mm standard)

        // Mobile-specific optimizations
        const pixelRatio = window.isMobileDevice ? 1.5 : 2; // Lower quality on mobile to reduce memory usage
        const timeout = window.isMobileDevice ? 10000 : 5000; // Longer timeout for mobile devices

        console.log(
            "Generating Order image - Device:",
            window.isMobileDevice ? "Mobile" : "Desktop",
            "Pixel Ratio:",
            pixelRatio
        );

        // Generate PNG using html-to-image from iframe body with timeout
        const dataUrl = await Promise.race([
            htmlToImage.toPng(iframeBody, {
                canvasWidth: actualWidth,
                backgroundColor: "#fff",
                pixelRatio: pixelRatio,
                cacheBust: true,
                width: actualWidth,
                height: undefined, // Let height be calculated automatically
                skipFonts: window.isIOSSafari, // Skip font loading on iOS Safari to prevent timeout
            }),
            new Promise((_, reject) =>
                setTimeout(
                    () => reject(new Error("Order image generation timeout")),
                    timeout
                )
            ),
        ]);

        // Get CSRF token from meta tag
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        const res = await fetch("/order/png", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                image_base64: dataUrl,
                order_id: orderId,
                width: actualWidth,
                mono: true, // High-contrast B/W for thermal printing
            }),
        }).catch((fetchError) => {
            console.error(
                "❌ Network error while uploading Order image:",
                fetchError
            );
            if (window.isMobileDevice) {
                console.warn(
                    "📱 Mobile network issue detected. Please check your internet connection and try again."
                );
            }
            throw fetchError;
        });

        const responseText = await res.text();

        if (!res.ok) {
            console.error("❌ HTTP Error:", res.status, res.statusText);
            console.error("Response text:", responseText);
            if (window.isMobileDevice) {
                console.warn(
                    "📱 Upload failed on mobile. Status:",
                    res.status,
                    "- Please try again or use a stable connection."
                );
            }
            return;
        }

        let result;
        try {
            result = JSON.parse(responseText);
            if (result.ok) {
                console.log("Order image saved successfully:", result.url);
            } else {
                console.error("Failed to save Order image:", result.message);
            }
        } catch (error) {
            console.error("Failed to parse response as JSON:", error);
            console.error("Response text:", responseText);
        }

        // Clean up
        document.body.removeChild(iframe);
        window.orderImageInProgress = false;
    } catch (error) {
        console.error("Error saving Order image:", error);

        // Provide user-friendly error messages for mobile
        if (window.isMobileDevice) {
            if (error.message && error.message.includes("timeout")) {
                console.warn(
                    "Order image generation timed out on mobile device. This may be due to device memory or processing limitations."
                );
            } else if (
                error.name === "QuotaExceededError" ||
                error.message?.includes("memory")
            ) {
                console.warn(
                    "Order image generation failed due to memory constraints on mobile device. Try closing other apps."
                );
            }
        }

        window.orderImageInProgress = false;
    }
}

/**
 * Save Report image using html-to-image
 */
async function saveReportImageFromPrint(sessionId, content, reportType) {
    // Check if html-to-image library is available
    if (typeof htmlToImage === "undefined") {
        console.error(
            "❌ html-to-image library not loaded. Cannot generate Report image. Please ensure the library is included in your page."
        );
        return;
    }

    try {
        console.log("Starting Report image capture - Type:", reportType);
        console.log(
            "Device:",
            window.isMobileDevice ? "📱 Mobile" : "🖥️ Desktop"
        );

        // Sanitize HTML: remove print triggers and scripts in capture HTML
        const sanitizeHtmlForImage = (html) => {
            try {
                let out = html;
                out = out.replace(
                    new RegExp("<script[\\s\\S]*?<\\/script>", "gi"),
                    ""
                );
                out = out.replace(
                    new RegExp("window\\.print\\s*\\([^)]*\\);?", "gi"),
                    ""
                );
                out = out.replace(
                    new RegExp('onload\\s*=\\s*"[^"]*print\\(\\)[^"]*"', "gi"),
                    ""
                );
                out = out.replace(
                    new RegExp("onload\\s*=\\s*'[^']*print\\(\\)[^']*'", "gi"),
                    ""
                );
                return out;
            } catch (e) {
                return html;
            }
        };

        const safeContent = sanitizeHtmlForImage(content);
        // Create a hidden iframe for the Report content
        const iframe = document.createElement("iframe");
        iframe.style.position = "absolute";
        iframe.style.left = "-9999px";
        iframe.style.top = "0";
        iframe.style.width = "auto"; // natural width
        iframe.style.maxWidth = "576px"; // 80mm
        iframe.style.height = "auto";
        iframe.style.border = "none";
        iframe.style.background = "#fff";

        // Disable print functionality in iframe
        iframe.setAttribute("sandbox", "allow-same-origin allow-scripts");

        document.body.appendChild(iframe);

        // Write the content to the iframe
        const iframeDoc =
            iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(safeContent);
        iframeDoc.close();

        // Wait for iframe to load and fonts to be ready
        await new Promise((resolve) => {
            iframe.onload = () => {
                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(resolve);
                } else {
                    resolve();
                }
            };
        });

        const iframeBody = iframeDoc.body;
        iframeBody.style.width = "auto";
        iframeBody.style.maxWidth = "576px";
        iframeBody.style.overflow = "visible";
        iframeBody.style.display = "inline-block";

        const contentWidth = iframeBody.scrollWidth;
        const actualWidth = Math.min(contentWidth, 576);

        // Mobile-specific optimizations
        const pixelRatio = window.isMobileDevice ? 1.5 : 2;
        const timeout = window.isMobileDevice ? 10000 : 5000;

        console.log(
            "Generating Report image - Device:",
            window.isMobileDevice ? "Mobile" : "Desktop",
            "Type:",
            reportType
        );

        const dataUrl = await Promise.race([
            htmlToImage.toPng(iframeBody, {
                canvasWidth: actualWidth,
                backgroundColor: "#fff",
                pixelRatio: pixelRatio,
                cacheBust: true,
                width: actualWidth,
                height: undefined,
                skipFonts: window.isIOSSafari,
            }),
            new Promise((_, reject) =>
                setTimeout(
                    () => reject(new Error("Report image generation timeout")),
                    timeout
                )
            ),
        ]);

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        const res = await fetch("/report/png", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                image_base64: dataUrl,
                session_id: sessionId,
                report_type: reportType,
                width: actualWidth,
                mono: true,
            }),
        }).catch((fetchError) => {
            console.error(
                "❌ Network error while uploading Report image:",
                fetchError
            );
            if (window.isMobileDevice) {
                console.warn(
                    "📱 Mobile network issue detected. Please check your internet connection and try again."
                );
            }
            throw fetchError;
        });

        // Cleanup
        document.body.removeChild(iframe);

        if (!res.ok) {
            const responseText = await res.text();
            console.error(
                "❌ Failed to save report image:",
                res.status,
                responseText
            );
            if (window.isMobileDevice) {
                console.warn(
                    "📱 Upload failed on mobile. Status:",
                    res.status,
                    "- Please try again or use a stable connection."
                );
            }
            return;
        }

        try {
            const result = await res.json();
            console.log(
                "Report image saved:",
                result.url || result.path || result
            );
        } catch (e) {
            console.log("Report image saved (non-JSON response)");
        }
    } catch (error) {
        console.error("Error saving Report image:", error);

        // Provide user-friendly error messages for mobile
        if (window.isMobileDevice) {
            if (error.message && error.message.includes("timeout")) {
                console.warn(
                    "Report image generation timed out on mobile device. This may be due to device memory or processing limitations."
                );
            } else if (
                error.name === "QuotaExceededError" ||
                error.message?.includes("memory")
            ) {
                console.warn(
                    "Report image generation failed due to memory constraints on mobile device. Try closing other apps."
                );
            }
        }
    }
}
