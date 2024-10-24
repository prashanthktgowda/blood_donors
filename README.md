# blood_bank_mobile
donor search engine

Features and Operations in the index.php Page:

Session Management:

The index.php file starts by initializing the session using session_start() to handle session data, which is useful for persisting information such as search results or error messages across pages.
Database Integration:

The page includes a database connection (db.php) to fetch donor data.
Two SQL queries are used:
One to count the total registered donors.
Another to count eligible donors (those who haven't donated in the last 84 days).
Search Functionality:

The page supports two types of searches:
Blood Group Search: Users can select a blood group and an optional location to search for donors.
Location-Based Search: Users can find donors within a 50 km radius of their current GPS location, filtered by blood group.
For location-based searches, the GPS coordinates are obtained using the browser's navigator.geolocation API, which automatically fills in the latitude and longitude.
Error Handling:

Error handling is enabled by configuring PHP to display errors during development (ini_set() calls), which can be disabled in production.
If any issues occur while executing the search query, the system logs the errors and provides appropriate feedback.
Form Submissions:

The POST method is used to handle form submissions for both search modes (blood group and location).
Upon search submission:
If donors are found, their details are stored in the session ($_SESSION['donors']).
If no donors match the criteria, a message is stored in the session to inform the user ($_SESSION['message']).
After processing, the page redirects to donor_list.php to display the search results.
Dynamic Search Toggle:

The page offers a toggle feature to switch between Blood Group Search and Location-Based Search:
The visibility of each search form is controlled by JavaScript (toggleSearchMode() function), allowing users to select the type of search they want.
The selected search form is dynamically displayed while the other remains hidden.
User Interface Elements:

Background Video: A video (video2.mp4) is used as the background for the search form, adding a dynamic visual element to the page.
Form Validation: Basic form validation ensures that required fields like blood group are selected before the form is submitted.
GPS Error Handling:

The page also provides error handling for GPS functionality. If the user denies location access or an error occurs while retrieving the GPS data, a corresponding error message is displayed on the page.
Chart.js Integration (Future Expansion):

The code includes a script reference for Chart.js, which suggests potential future use of charts (e.g., for visualizing donor statistics).
![image](https://github.com/user-attachments/assets/2ac4dcc1-fe22-4450-b5ea-495a6320eff3)

Features and Operations in the index.php Page:
