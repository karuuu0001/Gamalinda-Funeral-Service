<?php
session_start();
include('includes/db_connection.php');

// Fetch reserved dates from the database
$reserved_dates = [];
$query = "SELECT date FROM appointments";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reserved_dates[] = $row['date'];
    }
}

// Get the current month and year
$current_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$current_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Get the first day of the month and the number of days in the month
$first_day_of_month = strtotime("$current_year-$current_month-01");
$last_day_of_month = strtotime("last day of this month", $first_day_of_month);
$days_in_month = date('t', $first_day_of_month);

// Get the day of the week for the 1st of the month
$first_day_weekday = date('w', $first_day_of_month);

// Create the calendar array
$calendar = [];
$day_count = 1;

// Create the calendar grid
for ($i = 0; $i < 6; $i++) {
    $calendar[$i] = [];
    for ($j = 0; $j < 7; $j++) {
        if ($i === 0 && $j < $first_day_weekday) {
            $calendar[$i][$j] = ''; // Empty spaces before the 1st day
        } elseif ($day_count <= $days_in_month) {
            $calendar[$i][$j] = $day_count++;
        } else {
            $calendar[$i][$j] = ''; // Empty spaces after the last day
        }
    }
}

// Format for displaying selected date
$selected_date = isset($_GET['selected_date']) ? $_GET['selected_date'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Schedule Appointment</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Calendar styling */
        body {
            background-image: url('../image/1.jpg'); /* Replace with your image path */
            background-size: cover; /* Ensures the image covers the entire body */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat; /* Prevents the image from repeating */
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr); /* Each column for a day of the week */
            gap: 5px;
            background-color: rgba(0, 0, 0, 0.4); /* Semi-transparent black background */
            backdrop-filter: blur(10px); /* Apply blur effect */
            border-radius: 10px; /* Optional rounded corners */
        }

        .calendar div {
            padding: 10px;
            text-align: center;
            height: 100px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .calendar div.reserved {
            background-color: rgba(255, 0, 0, 0.7); /* Semi-transparent red for reserved days */
            color: white;
            cursor: not-allowed;
        }

        .calendar div.available {
            background-color: rgba(240, 240, 240, 0.7); /* Light gray with transparency */
        }

        .calendar div.selected {
            background-color: rgba(76, 175, 80, 0.7); /* Green with transparency */
            color: white;
        }

        .calendar div:hover {
            background-color: rgba(221, 221, 221, 0.7); /* Light hover effect */
        }

        /* Calendar Header (Days of the Week) */
        .calendar-header {
            display: flex;
            justify-content: space-around;
            font-weight: bold;
            background-color: rgba(245, 245, 245, 0.8); /* Semi-transparent background */
            padding: 5px 0;
            backdrop-filter: blur(5px); /* Apply blur effect to header */
            border-bottom: 3px solid black; /* Adds a black border at the bottom of the header */
            border: 2px solid black;
        }

        /* Add borders between days (Mon, Tue, Wed, etc.) */
        .calendar-header div {
            flex: 1;
            text-align: center;
            border-right: 2px solid black; /* Add border between days */
        }



        .calendar-nav {
            margin: 20px 0;
        }

        .btn-prev,
        .btn-next {
            padding: 5px 10px;
            cursor: pointer;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black buttons */
            color: white;
            border-radius: 3px;
        }

        .calendar {
            margin-top: 0px;
        }

        .calendar-header div {
            flex: 1;
            text-align: center;
        }

        #selected-date-info {
            margin-top: 20px;
        }

        /* User Info Section Styling */
        .user-info {
            position: fixed;
            top: 5px;
            right: 10px;
            border-radius: 5px;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            z-index: 1000;
            color: white;
            background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent black background */
            backdrop-filter: blur(5px); /* Apply blur effect */
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-info span {
            font-size: 16px;
            margin-right: 10px;
        }

        .btn-logout {
            color: #fff;
            background-color: rgba(255, 77, 77, 0.8); /* Transparent red logout button */
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-logout:hover {
            background-color: rgba(224, 67, 67, 0.8); /* Darker red on hover */
        }

        .login-register-message {
            margin-top: 20px;
            font-size: 18px;
            color: red;
        }

    </style>
</head>

<body>

    <header class="banner">
        <div class="banner-text">
            <h1>Schedule Appointment</h1>
            <p>Select a date to book your appointment</p>
        </div>
    </header>

    <div class="container">
 
        <!-- User Info Section -->
        <div class="user-info">
            <?php if (isset($_SESSION['username'])): ?>
                <img src="Assets/Image/avatar.jpg" alt="User Avatar">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" class="btn-logout">Logout</a>
            <?php endif; ?>
        </div>

        <!-- Calendar Navigation -->
         
        <div class="calendar-nav">
            <a href="schedule.php?month=<?php echo $current_month - 1; ?>&year=<?php echo $current_year; ?>" class="btn-prev">Prev</a>
            <span><?php echo date('F Y', $first_day_of_month); ?></span>
            <a href="schedule.php?month=<?php echo $current_month + 1; ?>&year=<?php echo $current_year; ?>" class="btn-next">Next</a>
        </div>

        <!-- Days of the Week (Calendar Header) -->
        <div class="calendar-header">
            <div style="color: red;">Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div style="color: red;">Sat</div>
        </div>

        <!-- Calendar Grid (Days of the Month) -->
        <div class="calendar">
            <?php foreach ($calendar as $week): ?>
                <?php foreach ($week as $day): ?>
                    <?php
                    if ($day == '') {
                        echo '<div></div>';
                    } else {
                        $date_str = "$current_year-$current_month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                        $is_reserved = in_array($date_str, $reserved_dates);
                        $is_selected = $date_str === $selected_date ? 'selected' : '';

                        // Add classes for styling
                        $class = $is_reserved ? 'reserved' : 'available';
                        if ($is_selected) {
                            $class = 'selected';
                        }

                        $appointment_status = $is_reserved ? 'No appointments available' : 'Appointments available';
                        // Add the onclick handler only for available dates
                        $onclick = $is_reserved ? '' : "selectDate('$date_str')";
                        echo "<div class='$class' data-date='$date_str' onclick=\"$onclick\">$day<br><small>$appointment_status</small></div>";
                    }
                    ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
    </div>


        <div id="selected-date-info">
            <?php if ($selected_date): ?>
                <p>You selected: <?php echo $selected_date; ?></p>

                <?php if (isset($_SESSION['username'])): ?>
                    <form method="post" action="confirm_schedule.php">
                        <input type="hidden" name="date" value="<?php echo $selected_date; ?>">
                        <input type="submit" value="Book Appointment">
                    </form>
                <?php else: ?>
                    <div class="login-register-message">
                        Please <a href="login.php">login</a> or <a href="userregister.php">register</a> to book an appointment.
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>

    <script>
        function selectDate(date) {
            // Update the URL without reloading the page
            history.pushState({selected_date: date}, null, '?month=<?php echo $current_month; ?>&year=<?php echo $current_year; ?>&selected_date=' + date);

            // Update selected date info
            const selectedDateInfo = document.getElementById('selected-date-info');
            const formHtml = ` 
                <p>You selected: ${date}</p>
                <?php if (isset($_SESSION['username'])): ?>
                    <form method="post" action="confirm_schedule.php">
                        <input type="hidden" name="date" value="${date}">
                        <input type="submit" value="Book Appointment">
                    </form>
                <?php else: ?>
                    <div class="login-register-message">
                        Please <a href="login.php">login</a> or <a href="userregister.php">register</a> to book an appointment.
                    </div>
                <?php endif; ?>
            `;
            selectedDateInfo.innerHTML = formHtml;

            // Optionally, you can highlight the selected date by adding/removing classes
            let allDates = document.querySelectorAll('.calendar div');
            allDates.forEach(function(div) {
                div.classList.remove('selected');  // Remove 'selected' class from all
            });

            // Add 'selected' class to the clicked date
            let selectedDiv = document.querySelector(`div[data-date='${date}']`);
            if (selectedDiv) {
                selectedDiv.classList.add('selected');
            }
        }
    </script>

</body>

</html>
