<?php
// Start the session to access session variables
session_start();

// Get the user_id from the session.
// This user_id is expected to be the ID of the child whose progress is being viewed.
if (isset($_SESSION["user_id"])) {
    $current_child_user_id = $_SESSION["user_id"];
} else {
    // Redirect to login or show an error if no user_id is in session
    // For now, setting to 0 for development, but ideally this would require proper login flow.
    $current_child_user_id = 0;
    // In a production environment, you might redirect: header('Location: ../login.php'); exit();
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Child Progress Dashboard</title>
         <link rel="stylesheet" href="../icons/css/fontawesome.min.css">
        <link rel="stylesheet" href="../icons/css/solid.min.css">
        <link rel="stylesheet" href="../icons/css/brands.min.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0px;
                background-color: #f4f7f6;
                color: #333;
            }
            .dashboard-container {
                max-width: 1200px;
                margin: 0 auto;
                background-color: #fff;
                padding: 25px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            h1,
            h2,
            h3 {
                color: #0056b3;
            }
            .controls {
                display: flex;
                gap: 15px;
                margin-bottom: 25px;
                align-items: center;
                flex-wrap: wrap; /* Allow controls to wrap on smaller screens */
            }
            .controls label {
                font-weight: bold;
            }
            .controls input[type="date"],
            .controls button {
                padding: 10px 15px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 16px;
                box-sizing: border-box; /* Include padding/border in element's total width */
            }
            .controls button {
                background-color: #007bff;
                color: white;
                cursor: pointer;
                border: none;
            }
            .controls button:hover {
                background-color: #0056b3;
            }
            .progress-section {
                margin-bottom: 30px;
                border-bottom: 1px solid #eee;
                padding-bottom: 20px;
            }
            .progress-section:last-child {
                border-bottom: none;
            }
            .grid-container {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }
            .card {
                background-color: #e9f7ef;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            }
            .card h3 {
                margin-top: 0;
                color: #28a745;
            }
            ul {
                list-style: none;
                padding: 0;
            }
            ul li {
                background-color: #f8f9fa;
                margin-bottom: 5px;
                padding: 8px 12px;
                border-radius: 4px;
                border: 1px solid #e2e6ea;
            }
            .error-message {
                color: red;
                font-weight: bold;
                margin-top: 10px;
                margin-bottom: 20px;
            }
            .drawing-gallery {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .drawing-item {
                border: 1px solid #ddd;
                padding: 5px;
                border-radius: 5px;
                background-color: #f8f9fa;
            }
            .drawing-item img {
                max-width: 150px;
                height: auto;
                display: block;
                border-radius: 3px;
            }
            @media (max-width: 768px) {
                .controls {
                    flex-direction: column;
                    align-items: stretch;
                }
                .controls input[type="date"],
                .controls button {
                    width: 100%;
                }
            }
            /* Header Specific Styles */
            header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: brown; /* Use theme color */
                color: white;
                padding: 10px 20px;
                margin-bottom: 20px;
                height: 50px;
            }
            header .logo img {
                height: 160px; /* Adjust as needed */
            }
            header .center-icons {
                display: flex;
                gap: 20px;
            }
            header .nav-item {
                color: white;
                font-size: 1.5em;
                text-decoration: none;
                position: relative;
            }
            header .nav-item:hover {
                color: #e2e6ea;
            }
            header .title {
                font-size: 1.8em;
                font-weight: bold;
            }
            .drawing-item a {
                 cursor: pointer;
                 transition: all 0.2s ease;
                 display: inline-block; /* Helps with shadow */
            }
            .drawing-item img:hover {
                opacity: 0.8;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                 transform: scale(1.03);
            }
        </style>
    </head>
    <body>
        <header>
            <div class="logo">
                <img src="../images/logo.png" alt="Logo" />
            </div>
            <div class="center-icons">
                <a href="../home.php" class="nav-item" data-tooltip="Home"
                    ><i class="fas fa-home"></i
                ></a>
            </div>
            <div class="title">CHILD PROGRESS</div>
        </header>

        <div class="dashboard-container">
            <h1>
                Progress Dashboard for
                <span id="childName">Loading Child...</span>
            </h1>

            <div class="controls">
                <label for="dateSelect">Select Date:</label>
                <input type="date" id="dateSelect" />
                <button id="fetchDataBtn">Fetch Progress</button>
            </div>

            <div id="errorMessage" class="error-message"></div>

            <div class="progress-section">
                <h2>
                    Activity Summary for <span id="selectedDate">Today</span>
                </h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>Total Screen Time</h3>
                        <p><span id="totalMinutes">0</span> minutes</p>
                    </div>
                    <div class="card">
                        <h3>Time Breakdown by Section</h3>
                        <ul id="timeBreakdownList">
                            <li>No data</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="progress-section">
                <h2>BrainPlay Performance</h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>Questions Attended</h3>
                        <p><span id="questionsAttended">0</span></p>
                    </div>
                    <div class="card">
                        <h3>Average Attempts per Question</h3>
                        <p><span id="avgAttempts">0</span></p>
                    </div>
                    <div class="card">
                        <h3>First Try Success Rate</h3>
                        <p><span id="firstTrySuccess">0</span>%</p>
                    </div>
                </div>
            </div>

            <div class="progress-section">
            <h2>LetterBeats Progress (Phonics)</h2>
            <div class="grid-container">
                <div class="card">
                    <h3 id="newItemsLabel">New Letters Learned Today</h3>
                    <p><span id="newLettersToday">0</span></p>
                </div>
                <div class="card">
                    <h3 id="totalItemsLabel">Total Letters Mastered (Cumulative)</h3>
                    <p><span id="totalLettersMastered">0</span></p>
                </div>
            </div>
        </div>

            <div class="progress-section">
                <h2>StoryLand Activity</h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>
                            Stories Read Today (<span id="storyCount">0</span>)
                        </h3>
                        <ul id="storyList">
                            <li>No stories read.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="progress-section">
                <h2>TuneTown Activity</h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>
                            Songs Played Today (<span id="musicCount">0</span>)
                        </h3>
                        <ul id="musicList">
                            <li>No songs played.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="progress-section">
                <h2>ColorFun Creations</h2>
                <div class="grid-container">
                    <div class="card">
                        <h3>
                            Drawings Made Today (<span id="drawingCount">0</span>)
                        </h3>
                        <div id="drawingGallery" class="drawing-gallery">
                            <p>No drawings created today.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // The CHILD_USER_ID is now dynamically passed from the PHP session.
                const CHILD_USER_ID = <?php echo json_encode(
                    $current_child_user_id,
                ); ?>;

                const dateSelect = document.getElementById("dateSelect");
                const fetchDataBtn = document.getElementById("fetchDataBtn");
                const childNameSpan = document.getElementById("childName");
                const selectedDateSpan = document.getElementById("selectedDate");
                const errorMessageDiv = document.getElementById("errorMessage");

                // Set default date to today
                const today = new Date();
                dateSelect.value = today.toISOString().split("T")[0];

                // --- Helper function to clear all dashboard data ---
                function clearDashboardData() {
                    document.getElementById("totalMinutes").textContent = 0;
                    document.getElementById("timeBreakdownList").innerHTML =
                        "<li>No activity recorded.</li>";
                    document.getElementById("questionsAttended").textContent = 0;
                    document.getElementById("avgAttempts").textContent = 0;
                    document.getElementById("firstTrySuccess").textContent = 0;
                    document.getElementById("newLettersToday").textContent = 0;
                    document.getElementById("totalLettersMastered").textContent = 0;
                    document.getElementById("storyCount").textContent = 0;
                    document.getElementById("storyList").innerHTML =
                        "<li>No stories read.</li>";
                    document.getElementById("musicCount").textContent = 0;
                    document.getElementById("musicList").innerHTML =
                        "<li>No songs played.</li>";
                    document.getElementById("drawingCount").textContent = 0;
                    document.getElementById("drawingGallery").innerHTML =
                        "<p>No drawings created today.</p>";
                }

                // --- Function to fetch child's details (name, age) ---
                async function fetchChildDetails(childId) {
                    if (childId === 0) {
                        childNameSpan.textContent = "No Child Selected (Login Required)";
                        errorMessageDiv.textContent = "No user ID found in session. Please log in.";
                        clearDashboardData();
                        return;
                    }
                    try {
                        const response = await fetch(`get_child_name.php?user_id=${childId}`);
                        const data = await response.json();
                        if (data.status === "success" && data.username) {
                            childNameSpan.textContent = `${data.username} (Age: ${data.age})`;
                        } else {
                            childNameSpan.textContent = `Unknown Child (ID: ${childId})`;
                            errorMessageDiv.textContent = data.message || "Failed to fetch child details.";
                        }
                    } catch (error) {
                        childNameSpan.textContent = `Unknown Child (ID: ${childId})`;
                        errorMessageDiv.textContent = "Failed to load child details: " + error.message;
                        console.error("Fetch child details error:", error);
                    }
                }

                // --- Function to fetch and display progress for the child and selected date ---
                async function fetchAndDisplayProgress() {
                    const selectedDate = dateSelect.value;

                    if (CHILD_USER_ID === 0) {
                         errorMessageDiv.textContent = "Cannot fetch progress: No child ID available in session. Please log in.";
                         clearDashboardData();
                         return;
                    }

                    if (!selectedDate) {
                        errorMessageDiv.textContent = "Please select a date.";
                        clearDashboardData();
                        return;
                    }

                    errorMessageDiv.textContent = ""; // Clear previous errors
                    selectedDateSpan.textContent = new Date(
                        selectedDate,
                    ).toDateString();

                    try {
                        const response = await fetch(
                            `fetch_progress.php?user_id=${CHILD_USER_ID}&date=${selectedDate}`,
                        );
                        const data = await response.json();

                        if (data.status === "error") {
                            errorMessageDiv.textContent =
                                "Error fetching progress: " + data.message;
                            clearDashboardData();
                            return;
                        }

                        // --- Update Activity Summary ---
                        document.getElementById("totalMinutes").textContent =
                            data.time_summary.total_minutes_used || 0;
                        const timeBreakdownList =
                            document.getElementById("timeBreakdownList");
                        timeBreakdownList.innerHTML = "";
                        if (data.time_summary.time_breakdown.length > 0) {
                            data.time_summary.time_breakdown.forEach((item) => {
                                const li = document.createElement("li");
                                li.textContent = `${item.section}: ${item.minutes} minutes`;
                                timeBreakdownList.appendChild(li);
                            });
                        } else {
                            timeBreakdownList.innerHTML =
                                "<li>No activity recorded.</li>";
                        }

                        // --- Update BrainPlay Performance ---
                        document.getElementById("questionsAttended").textContent =
                            data.brainplay.questions_attended || 0;
                        document.getElementById("avgAttempts").textContent =
                            data.brainplay.avg_attempts || 0;
                        document.getElementById("firstTrySuccess").textContent =
                            data.brainplay.first_try_success_percent || 0;

                        // --- Update LetterBeats Progress ---
                        
                        // NEW: Get the dynamic label ("Letters" or "Sounds") from the data
                        const phonicsLabel = data.letterbeats.label || 'Items'; 

                        // NEW: Update the <h3> titles dynamically
                        document.getElementById("newItemsLabel").textContent = 
                            `New ${phonicsLabel} Learned Today`;
                        document.getElementById("totalItemsLabel").textContent = 
                            `Total ${phonicsLabel} Mastered (Cumulative)`;

                        // This part stays the same
                        document.getElementById("newLettersToday").textContent =
                            data.letterbeats.new_letters_today || 0;
                        document.getElementById("totalLettersMastered").textContent =
                            data.letterbeats.total_mastered || 0;

                        
                        // --- Update StoryLand Activity ---
                        document.getElementById("storyCount").textContent =
                            data.storyland.count || 0;
                        const storyList = document.getElementById("storyList");
                        storyList.innerHTML = "";
                        if (data.storyland.titles && data.storyland.titles.length > 0) {
                            data.storyland.titles.forEach((title) => {
                                const li = document.createElement("li");
                                li.textContent = title;
                                storyList.appendChild(li);
                            });
                        } else {
                            storyList.innerHTML = "<li>No stories read.</li>";
                        }

                        // --- Update TuneTown Activity ---
                        document.getElementById("musicCount").textContent =
                            data.tunetown.count || 0;
                        const musicList = document.getElementById("musicList");
                        musicList.innerHTML = "";
                        if (data.tunetown.titles && data.tunetown.titles.length > 0) {
                            data.tunetown.titles.forEach((title) => {
                                const li = document.createElement("li");
                                li.textContent = title;
                                musicList.appendChild(li);
                            });
                        } else {
                            musicList.innerHTML = "<li>No songs played.</li>";
                        }

                        // --- Update ColorFun Creations ---
                        document.getElementById("drawingCount").textContent =
                            data.colorfun.count || 0;
                        const drawingGallery =
                            document.getElementById("drawingGallery");
                        drawingGallery.innerHTML = "";
                        if (data.colorfun.drawings && data.colorfun.drawings.length > 0) {
                           // This is your NEW code with the download link
                            data.colorfun.drawings.forEach((drawing) => {
                             // 1. Get just the filename (e.g., "drawing_4_abc.png")
                            const filename = drawing.path.split('/').pop();

                             // 2. Create the link (<a>) tag
                             const link = document.createElement("a");
                             link.href = `../${drawing.path}`; // Path to the full image
                              link.download = filename; // This tells the browser to download it
                              link.title = `Click to download ${filename}`; // Tooltip

                             // 3. Create the image (<img>) tag
                             const img = document.createElement("img");
                             img.src = `../${drawing.path}`;
                               img.alt = `Drawing ${drawing.id}`;

                              // 4. Put the <img> INSIDE the <a>
                              link.appendChild(img);

                            // 5. Create the container div
                             const div = document.createElement("div");
                             div.className = "drawing-item";

                            // 6. Put the <a> (which contains the <img>) INSIDE the <div>
                            div.appendChild(link);
                            drawingGallery.appendChild(div);
                            });
                        } else {
                            drawingGallery.innerHTML =
                                "<p>No drawings created today.</p>";
                        }
                    } catch (error) {
                        errorMessageDiv.textContent =
                            "Failed to fetch progress data: " + error.message;
                        console.error("Fetch progress error:", error);
                        clearDashboardData();
                    }
                }

                // --- Event Listeners ---
                fetchDataBtn.addEventListener("click", fetchAndDisplayProgress);

                // --- Initial Load ---
                // Fetch child details and then progress when the page loads
                if (CHILD_USER_ID) {
                    fetchChildDetails(CHILD_USER_ID);
                    fetchAndDisplayProgress();
                } else {
                    errorMessageDiv.textContent = "CHILD_USER_ID is not set in the script. Cannot display progress.";
                    childNameSpan.textContent = "Error";
                }
            });
        </script>
    </body>
</html>
