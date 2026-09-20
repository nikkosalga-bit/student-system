<?php

require "db.php";


// CLEAR ALL SAVED RECORDS
if (isset($_POST['clear_records'])) {

    $conn->query("DELETE FROM students");

    header("Location: index.php");
    exit();
}


// UPDATE STUDENT
if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $age = $_POST['age'];

    $sql = "UPDATE students
            SET first_name = ?,
                middle_name = ?,
                last_name = ?,
                age = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssii",
        $firstName,
        $middleName,
        $lastName,
        $age,
        $id
    );

    $stmt->execute();

    header("Location: index.php");
    exit();
}


// EDIT STUDENT
if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $editStudent = $stmt->get_result()->fetch_assoc();
}


// DELETE STUDENT
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}


// ADD STUDENT
if (isset($_POST['add'])) {

    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $age = $_POST['age'];

    $sql = "INSERT INTO students
            (first_name, middle_name, last_name, age)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssi",
        $firstName,
        $middleName,
        $lastName,
        $age
    );

    $stmt->execute();

    header("Location: index.php");
    exit();
}


// GET ALL STUDENTS
$result = $conn->query("SELECT * FROM students ORDER BY id DESC");

?>


<!DOCTYPE html>
<html>

<head>

    <title>Student Records</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>

    <h1>Student Records</h1>


    <!-- STUDENT FORM -->

    <form method="POST">

        <label>First Name:</label>

        <input
            type="text"
            name="firstName"
            required
            value="<?= isset($editStudent) ? htmlspecialchars($editStudent['first_name']) : '' ?>"
        >

        <br>


        <label>Middle Name:</label>

        <input
            type="text"
            name="middleName"
            value="<?= isset($editStudent) ? htmlspecialchars($editStudent['middle_name']) : '' ?>"
        >

        <br>


        <label>Last Name:</label>

        <input
            type="text"
            name="lastName"
            required
            value="<?= isset($editStudent) ? htmlspecialchars($editStudent['last_name']) : '' ?>"
        >

        <br>


        <label>Age:</label>

        <input
            type="number"
            name="age"
            required
            value="<?= isset($editStudent) ? htmlspecialchars($editStudent['age']) : '' ?>"
        >

        <br><br>


        <?php if (isset($editStudent)): ?>

            <input
                type="hidden"
                name="id"
                value="<?= $editStudent['id'] ?>"
            >

            <button
                type="submit"
                name="update"
            >
                Update
            </button>

        <?php else: ?>

            <button
                type="submit"
                name="add"
            >
                Add
            </button>

        <?php endif; ?>


        <!-- CLEAR INPUT FIELDS -->

        <button
            type="reset"
        >
            Clear Form
        </button>

    </form>


    <br>


    <!-- CLEAR SAVED RECORDS -->

    <form method="POST">

        <button
            type="submit"
            name="clear_records"
            onclick="return confirm('Are you sure you want to clear all saved records?')"
        >
            Clear Records
        </button>

    </form>


    <h2>Records</h2>


    <!-- RECORDS TABLE -->

    <table>

        <thead>

            <tr>

                <th>First Name</th>

                <th>Middle Name</th>

                <th>Last Name</th>

                <th>Age</th>

                <th>Action</th>

            </tr>

        </thead>


        <tbody>

            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($row['first_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['middle_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['last_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['age']) ?>
                    </td>

                    <td>

                        <a
                            href="index.php?delete=<?= $row['id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this student?')"
                        >
                            <button type="button">
                                Delete
                            </button>
                        </a>


                        <a
                            href="index.php?edit=<?= $row['id'] ?>"
                        >
                            <button type="button">
                                Edit
                            </button>
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </tbody>

    </table>


</body>

</html>