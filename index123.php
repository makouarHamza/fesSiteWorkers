<?php
// config.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // إعادة التوجيه إلى صفحة تسجيل الدخول أو صفحة غير مصرح بها
    header("Location: login.php");
    exit();
}

// This file contains your database connection settings.

// Database credentials
define('DB_SERVER', 'localhost'); // Your database server (e.g., 'localhost' or an IP address)
define('DB_USERNAME', 'root');   // Your database username
define('DB_PASSWORD', '1972');       // Your database password (leave empty for no password)
define('DB_NAME', 'MarketMap');  // The name of your database

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>

<?php
// index.php
// This file contains the HTML structure, Tailwind CSS styling, and PHP logic for
// displaying cities/souks and adding new cities.

// Include the database connection file
// require_once "config.php";

$message = ""; // Variable to store success or error messages

// Handle form submission for adding a new city
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_city'])) {
    // Validate and sanitize inputs
    $name_en = trim($_POST["name_en"]);
    $name_ar = trim($_POST["name_ar"]);
    $country_en = trim($_POST["country_en"]);
    $country_ar = trim($_POST["country_ar"]);

    // Check if fields are empty
    if (empty($name_en) || empty($country_en)) {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                        <strong class='font-bold'>Error!</strong>
                        <span class='block sm:inline'>English name and country for the city cannot be empty.</span>
                    </div>";
    } else {
        // Prepare an insert statement
        $sql = "INSERT INTO cities (name_en, name_ar, country_en, country_ar) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, "ssss", $param_name_en, $param_name_ar, $param_country_en, $param_country_ar);

            // Set parameters
            $param_name_en = $name_en;
            $param_name_ar = !empty($name_ar) ? $name_ar : null; // Set to null if empty
            $param_country_en = $country_en;
            $param_country_ar = !empty($country_ar) ? $country_ar : null; // Set to null if empty

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4' role='alert'>
                                <strong class='font-bold'>Success!</strong>
                                <span class='block sm:inline'>City added successfully.</span>
                            </div>";
            } else {
                $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                                <strong class='font-bold'>Error!</strong>
                                <span class='block sm:inline'>Could not add city. Please try again.</span>
                                <span class='block text-sm'>" . mysqli_error($link) . "</span>
                            </div>";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                            <strong class='font-bold'>Error!</strong>
                            <span class='block sm:inline'>Prepared statement failed.</span>
                        </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketMap Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="[https://cdn.tailwindcss.com](https://cdn.tailwindcss.com)"></script>
    <style>
        /* Custom font import */
        @import url('[https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap](https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap)');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
        }
        .container-wrapper {
            max-width: 1200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0; /* Light border */
        }
        th {
            background-color: #cbd5e1; /* Gray background for headers */
            font-weight: 600;
            color: #333;
        }
        tr:hover {
            background-color: #f1f5f9; /* Light hover effect */
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container-wrapper mx-auto p-6 lg:p-10">
        <h1 class="text-4xl font-bold text-center text-indigo-700 mb-10 rounded-lg py-4 bg-white shadow-lg">
            MarketMap Admin Dashboard
        </h1>

        <?php echo $message; // Display success/error messages ?>

        <!-- Section to Add New City -->
        <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
            <h2 class="text-3xl font-semibold text-indigo-600 mb-6 border-b-2 pb-2 border-indigo-200">Add New City</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name_en" class="block text-gray-700 text-sm font-medium mb-2">City Name (English):</label>
                    <input type="text" id="name_en" name="name_en" required
                           class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="name_ar" class="block text-gray-700 text-sm font-medium mb-2">City Name (Arabic):</label>
                    <input type="text" id="name_ar" name="name_ar"
                           class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="country_en" class="block text-gray-700 text-sm font-medium mb-2">Country (English):</label>
                    <input type="text" id="country_en" name="country_en" required
                           class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="country_ar" class="block text-gray-700 text-sm font-medium mb-2">Country (Arabic):</label>
                    <input type="text" id="country_ar" name="country_ar"
                           class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" name="add_city"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                        Add City
                    </button>
                </div>
            </form>
        </div>

        <!-- Section to Display Cities -->
        <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
            <h2 class="text-3xl font-semibold text-indigo-600 mb-6 border-b-2 pb-2 border-indigo-200">Cities List</h2>
            <div class="overflow-x-auto rounded-lg shadow-sm">
                <table>
                    <thead>
                        <tr>
                            <th class="rounded-tl-lg">ID</th>
                            <th>Name (EN)</th>
                            <th>Name (AR)</th>
                            <th>Country (EN)</th>
                            <th class="rounded-tr-lg">Country (AR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Attempt select query execution
                        $sql = "SELECT * FROM cities";
                        if ($result = mysqli_query($link, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name_en']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name_ar']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['country_en']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['country_ar']) . "</td>";
                                    echo "</tr>";
                                }
                                // Free result set
                                mysqli_free_result($result);
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4'>No cities found.</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4 text-red-500'>ERROR: Could not able to execute $sql. " . mysqli_error($link) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section to Display Souks -->
        <div class="bg-white p-8 rounded-xl shadow-lg">
            <h2 class="text-3xl font-semibold text-indigo-600 mb-6 border-b-2 pb-2 border-indigo-200">Souks List</h2>
            <div class="overflow-x-auto rounded-lg shadow-sm">
                <table>
                    <thead>
                        <tr>
                            <th class="rounded-tl-lg">ID</th>
                            <th>Name (EN)</th>
                            <th>Name (AR)</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th class="rounded-tr-lg">City</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Attempt select query execution for souks, joining with cities to get city name
                        $sql = "SELECT s.id, s.name_en, s.name_ar, s.category, s.location, c.name_en AS city_name_en
                                FROM souks s
                                JOIN cities c ON s.city_id = c.id";
                        if ($result = mysqli_query($link, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name_en']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name_ar']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['city_name_en']) . "</td>";
                                    echo "</tr>";
                                }
                                // Free result set
                                mysqli_free_result($result);
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-4'>No souks found.</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-4 text-red-500'>ERROR: Could not able to execute $sql. " . mysqli_error($link) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php
    // Close connection
    mysqli_close($link);
    ?>
</body>
</html>
