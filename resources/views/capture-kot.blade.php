{{-- resources/views/capture-kot.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>KOT Preview</title>

  <style>

  </style>
</head>
<body>
  <div id="capture">
    {{-- 👉 Your actual KOT Blade view goes here --}}
    {!! $content !!}
  </div>

  <div style="text-align: center; padding: 20px; display: none;">
    <p>Generating and saving KOT image...</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.min.js"></script>
      <script>
        const node = document.getElementById('capture');
        let saveAttempted = false; // Prevent multiple save attempts

        async function renderPng() {
          if (document.fonts && document.fonts.ready) { await document.fonts.ready; }

          // Let content determine its natural width (like Browsershot fullWidth)
          node.style.width = 'auto';
          node.style.maxWidth = '576px';
          node.style.overflow = 'visible';
          node.style.display = 'inline-block';

          // Get the actual content width
          const contentWidth = node.scrollWidth;
          const actualWidth = Math.min(contentWidth, 576); // Cap at 576px (80mm standard)

          console.log('Content width:', contentWidth, 'Actual width:', actualWidth);

          // Use deviceScaleFactor(2) equivalent for sharper output
          const dataUrl = await htmlToImage.toPng(node, {
            canvasWidth: actualWidth,
            backgroundColor: '#fff',
            pixelRatio: 2, // Equivalent to Browsershot deviceScaleFactor(2)
            cacheBust: true,
            width: actualWidth,
            height: undefined // Let height be calculated automatically (like fullPage)
          });

          return { dataUrl, actualWidth: actualWidth };
        }

        async function saveToServer(dataUrl, actualWidth) {
          const csrf = document.querySelector('meta[name="csrf-token"]').content;
          const res = await fetch(
            "{{ route('kot.png.store') }}", {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf},
            body: JSON.stringify({
              image_base64: dataUrl,
              kot_id: {{ $kot->id }},
              width: actualWidth,   // dynamic width based on content
              mono: true,           // high-contrast B/W (good for thermal)
            })
          });
          const json = await res.json();
          if (!json.ok) {
            console.error('Save failed:', json.message || 'Unknown error');
            return null;
          }
          return json;
        }

                // Auto-save when page loads
        window.addEventListener('load', async () => {
          // Prevent multiple save attempts
          if (saveAttempted) {
            console.log('Save already attempted, skipping...');
            return;
          }

          saveAttempted = true;

          try {
            console.log('Starting KOT image capture for KOT ID: {{ $kot->id }}');
            const { dataUrl, actualWidth } = await renderPng();
            console.log('PNG generated, saving to server...');
            const result = await saveToServer(dataUrl, actualWidth);
            if (result) {
              console.log('KOT image saved successfully:', result.url);
              // Send success message to parent window
              if (window.parent && window.parent !== window) {
                window.parent.postMessage({
                  type: 'kot_capture_complete',
                  url: result.url,
                  path: result.path
                }, '*');
              }
            } else {
              console.error('Failed to save KOT image');
              // Send error message to parent window
              if (window.parent && window.parent !== window) {
                window.parent.postMessage({
                  type: 'kot_capture_error',
                  error: 'Failed to save KOT image'
                }, '*');
              }
            }
          } catch (error) {
            console.error('Auto-save failed:', error);
            // Send error message to parent window
            if (window.parent && window.parent !== window) {
              window.parent.postMessage({
                type: 'kot_capture_error',
                error: error.message
              }, '*');
            }
          }
        });
    </script>
</body>
</html>
