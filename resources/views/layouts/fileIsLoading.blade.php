<!DOCTYPE html>
<html>
    <head>
        <title>Retrieving file..</title>
    </head>
    <body>
        <div id="loading-overlay" class="loading-save-task" style="display: flex; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div class="spinner-border text-dark" role="status">
                </div>
                <div id="loading-text" style="margin-top: 3px; font-size: 2vh;">Please wait while we retrieve the file.</div>
            </div>
        </div>

        <script>
            // Get the loading text element
            let loadingText = document.querySelector('#loading-text');

            // Set the initial message
            let message = 'Please wait while we retrieve the file';
            loadingText.textContent = message;

            // Update the message every second
            let dots = '';
            setInterval(() => {
                // Add a dot to the message
                dots += '.';
                if (dots.length > 3) {
                    dots = '';
                }
                loadingText.textContent = message + dots;
            }, 300);
        </script>
    </body>
</html>
