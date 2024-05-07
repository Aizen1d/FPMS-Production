<!-- Notification container -->
<div class="notification-container"></div>

<!-- Notification system styles -->
<style>
    /* Style the notification container */
    .notification-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 300px;
        z-index: 999999;
    }

    /* Style the notification */
    .notification {
        position: relative;
        background-color: #333;
        color: white;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        opacity: 0;
        transform: translateX(300px);
        transition: all 0.5s;
    }

    .notification.show {
        opacity: 1;
        transform: translateX(0);
    }

    /* Style the close button */
    .close {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }

</style>

<!-- Notification system script -->
<script>
    // Function to show a notification
    function showNotification(message, backgroundColor) {
        // Check if the number of notifications has exceeded 5
        var notifications = document.querySelectorAll('.notification');
        if (notifications.length >= 5) {
            // Remove the oldest notification
            notifications[0].parentNode.removeChild(notifications[0]);
        }

        // Create the notification element
        var notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = message + '<span class="close" onclick="closeNotification(this)">&times;</span>';

        // Set the background color of the notification
        notification.style.backgroundColor = backgroundColor;

        // Append the notification to the container
        var container = document.querySelector('.notification-container');
        container.appendChild(notification);

        // Show the notification
        setTimeout(function() {
            notification.classList.add('show');
            notification.style.opacity = 1;
        }, 100);

        // Automatically close the notification after 5 seconds
        setTimeout(function() {
            closeNotification(notification.querySelector('.close'));
        }, 5000);
    }

   // Function to close a notification
    function closeNotification(closeButton) {
        // Get the notification element
        var notification = closeButton.parentNode;

        // Hide the notification
        notification.style.opacity = 0;

        // Remove the notification from the DOM
        setTimeout(function() {
            if (notification && notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 600);
    }

</script>