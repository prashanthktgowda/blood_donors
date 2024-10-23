<!-- loading.php -->
<div id="loading-icon"></div>

<style>
    /* Loading Icon Styles */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    #loading-icon {
        display: none; /* Initially hidden */
        border: 8px solid #f3f3f3; /* Light grey */
        border-top: 8px solid #3498db; /* Blue */
        border-radius: 50%; /* Circular shape */
        width: 50px; /* Size of the spinner */
        height: 50px; /* Size of the spinner */
        animation: spin 1s linear infinite; /* Animation */
        position: fixed; /* Fixed position */
        top: 50%; /* Center vertically */
        left: 50%; /* Center horizontally */
        transform: translate(-50%, -50%); /* Center it */
        z-index: 9999; /* On top of other elements */
    }
</style>

<script>
    // Show the loading icon on page load
    document.getElementById('loading-icon').style.display = 'block';

    // Hide the loading icon when the page is fully loaded
    window.addEventListener('load', () => {
        document.getElementById('loading-icon').style.display = 'none';
    });
</script>
