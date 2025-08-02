<?php
include 'assets/config.php';

echo "<h2>Database Check Results</h2>";

// Check 1: Database connection
echo "<h3>1. Database Connection</h3>";
if ($conn) {
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Check 2: Students table exists
echo "<h3>2. Students Table</h3>";
$result = mysqli_query($conn, "SHOW TABLES LIKE 'students'");
if (mysqli_num_rows($result) > 0) {
    echo "✅ Students table exists<br>";
} else {
    echo "❌ Students table does not exist<br>";
    exit;
}

// Check 3: Students table structure
echo "<h3>3. Students Table Structure</h3>";
$result = mysqli_query($conn, "DESCRIBE students");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Error getting table structure: " . mysqli_error($conn) . "<br>";
}

// Check 4: Total students count
echo "<h3>4. Students Count</h3>";
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM students");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "Total students: " . $row['count'] . "<br>";
} else {
    echo "❌ Error counting students: " . mysqli_error($conn) . "<br>";
}

// Check 5: Classes
echo "<h3>5. Classes</h3>";
$result = mysqli_query($conn, "SELECT DISTINCT class FROM students ORDER BY class");
if ($result) {
    echo "Classes found: ";
    $classes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $classes[] = $row['class'];
    }
    if (empty($classes)) {
        echo "❌ No classes found<br>";
    } else {
        echo "✅ " . implode(", ", $classes) . "<br>";
    }
} else {
    echo "❌ Error getting classes: " . mysqli_error($conn) . "<br>";
}

// Check 6: Sections for first class
echo "<h3>6. Sections for First Class</h3>";
if (!empty($classes)) {
    $first_class = $classes[0];
    $result = mysqli_query($conn, "SELECT DISTINCT section FROM students WHERE class = '$first_class' ORDER BY section");
    if ($result) {
        echo "Sections for class '$first_class': ";
        $sections = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $sections[] = $row['section'];
        }
        if (empty($sections)) {
            echo "❌ No sections found<br>";
        } else {
            echo "✅ " . implode(", ", $sections) . "<br>";
        }
    } else {
        echo "❌ Error getting sections: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "❌ No classes available to check sections<br>";
}

// Check 7: Students for first class and section
echo "<h3>7. Students for First Class and Section</h3>";
if (!empty($classes) && !empty($sections)) {
    $first_class = $classes[0];
    $first_section = $sections[0];
    $result = mysqli_query($conn, "SELECT id, fname, lname FROM students WHERE class = '$first_class' AND section = '$first_section' ORDER BY fname, lname");
    if ($result) {
        echo "Students in class '$first_class' section '$first_section':<br>";
        $student_count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "- " . $row['fname'] . " " . $row['lname'] . " (ID: " . $row['id'] . ")<br>";
            $student_count++;
        }
        if ($student_count == 0) {
            echo "❌ No students found<br>";
        } else {
            echo "✅ Found $student_count students<br>";
        }
    } else {
        echo "❌ Error getting students: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "❌ No classes or sections available to check students<br>";
}

// Check 8: Sample data students
echo "<h3>8. Sample Data Students</h3>";
$sample_ids = ['S1746314678', 'S1746314845', 'S1746315055', 'S1746315238', 'S1746315403'];
$found_count = 0;
foreach ($sample_ids as $id) {
    $result = mysqli_query($conn, "SELECT id, fname, lname, class, section FROM students WHERE id = '$id'");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo "✅ Found: " . $row['fname'] . " " . $row['lname'] . " (Class: " . $row['class'] . ", Section: " . $row['section'] . ")<br>";
        $found_count++;
    } else {
        echo "❌ Not found: $id<br>";
    }
}
echo "Sample students found: $found_count out of " . count($sample_ids) . "<br>";

// Check 9: Fee record table
echo "<h3>9. Fee Record Table</h3>";
$result = mysqli_query($conn, "SHOW TABLES LIKE 'fee_record'");
if (mysqli_num_rows($result) > 0) {
    echo "✅ Fee record table exists<br>";
    
    // Check fee record count
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM fee_record");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "Fee records: " . $row['count'] . "<br>";
    }
} else {
    echo "❌ Fee record table does not exist<br>";
}

echo "<br><strong>Check complete!</strong>";
?> 