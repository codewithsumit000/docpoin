<?php
include "../patient/connect.php";


if(isset($_POST['add'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $qualification = $_POST['qualification'];
    $department = $_POST['department'];
    $description = $_POST['description'];

    $photo_name = time() . "_" . $_FILES['photo']['name'];
    move_uploaded_file($_FILES['photo']['tmp_name'], "../doctor_photo/".$photo_name);

    mysqli_query($conn, "INSERT INTO doctors 
        (name,email,password,qualification,department,description,photo) 
        VALUES ('$name','$email','$password','$qualification','$department','$description','$photo_name')");
    header("Location: doctor_manage.php");
    exit();
}


if(isset($_POST['update'])){
    $id = (int)$_POST['doctor_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $qualification = $_POST['qualification'];
    $department = $_POST['department'];
    $description = $_POST['description'];

    $sql = "UPDATE doctors SET 
        name='$name', email='$email', password='$password', qualification='$qualification', department='$department', description='$description'";

    if(isset($_FILES['photo']) && $_FILES['photo']['name'] != ""){
        $photo_name = time() . "_" . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "../doctor_photo/".$photo_name);

        $old = mysqli_query($conn, "SELECT photo FROM doctors WHERE doctor_id='$id'");
        $old_photo = mysqli_fetch_assoc($old)['photo'];
        if($old_photo && file_exists("../doctor_photo/".$old_photo)) unlink("../doctor_photo/".$old_photo);

        $sql .= ", photo='$photo_name'";
    }

    $sql .= " WHERE doctor_id='$id'";
    mysqli_query($conn,$sql);
    header("Location: doctor_manage.php");
    exit();
}


if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $res = mysqli_query($conn,"SELECT photo FROM doctors WHERE doctor_id='$id'");
    $photo = mysqli_fetch_assoc($res)['photo'];
    if($photo && file_exists("../doctor_photo/".$photo)) unlink("../doctor_photo/".$photo);
    mysqli_query($conn,"DELETE FROM doctors WHERE doctor_id='$id'");
    header("Location: doctor_manage.php");
    exit();
}


$result = mysqli_query($conn,"SELECT * FROM doctors");
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctors Management</title>
<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}


body {
    display: flex;
    background: #f5f8ff;
}


.content { 
    margin-left: 250px; /* Sidebar width */
    padding: 30px;
    width: calc(100% - 250px);
}


h2 { 
    color: #0077b6; 
    margin-bottom: 20px; 
}


table {
    border-collapse: collapse;
    width: 100%;
    background: white;
    border-radius: 5px;
    overflow: hidden;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

th {
    background: #0077b6;
    color: white;
}


button {
    padding: 6px 12px;
    margin: 2px;
    cursor: pointer;
    border: none;
    border-radius: 4px;
}

.add { 
    background: #0077b6; 
    color: white; 
}

.edit { 
    background: #00b4d8; 
    color: white; 
}

.delete { 
    background: #ff5252; 
    color: white; 
}

.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 999;
}


.modal-content {
    background: white;
    width: 400px;
    margin: 70px auto;
    padding: 20px;
    border-radius: 8px;
    position: relative;
}


.close {
    position: absolute;
    top: 10px;
    right: 15px;
    cursor: pointer;
    font-size: 20px;
    color: #0077b6;
}


input, textarea {
    width: 100%;
    margin-bottom: 10px;
    padding: 8px;
}


img {
    max-width: 100px;
    display: block;
    margin-bottom: 10px;
    border-radius: 6px;
}
</style>
</head>

<link rel="stylesheet" href="admin_sidebar.css">
<body>


<div class="sidebar">
    <div class="logo">🩺 DocPoint</div>
    <div class="menu">
        <a href="../admin/index.php" class="active">Dashboard</a>
        <a href="../admin/appointment_manage.php">Appointments</a>
        <a href="../admin/doctor_manage.php">Doctors</a>
       
        <a href="../patient/logout.php" class="logout">Logout</a>
    </div>
</div>


<div class="content">
<h2>Doctors List</h2>
<button class="add" onclick="openModal('add')">➕ Add Doctor</button>

<table>
<tr>
    <th>Photo</th>
    <th>Name</th>
    <th>Email</th>
    <th>Department</th>
    <th>Qualification</th>
    <th>Action</th>
</tr>
<?php while($row=mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?php if($row['photo']) echo "<img src='../doctor_photo/".$row['photo']."'>"; ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['department'] ?></td>
    <td><?= $row['qualification'] ?></td>
    <td>
        <button class="edit" onclick='openModal("edit", <?= json_encode($row) ?>)'>Edit</button>
        <a href="?delete=<?= $row['doctor_id'] ?>" onclick="return confirm('Are you sure?')">
            <button class="delete">Delete</button>
        </a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- Modal -->
<div class="modal" id="modal">
<div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3 id="modalTitle">Add Doctor</h3>
    <form method="POST" enctype="multipart/form-data" id="doctorForm">
        <input type="hidden" name="doctor_id" id="doctor_id">
        <input type="text" name="name" id="name" placeholder="Doctor Name" required>
        <input type="email" name="email" id="email" placeholder="Email" required>
        <input type="text" name="password" id="password" placeholder="Password">
        <input type="text" name="qualification" id="qualification" placeholder="Qualification">
        <input type="text" name="department" id="department" placeholder="Department">
        <textarea name="description" id="description" placeholder="Description"></textarea>
        <input type="file" name="photo" id="photo">
        <img id="currentPhoto" src="" style="display:none;">
        <button type="submit" name="add" id="addBtn">Add Doctor</button>
        <button type="submit" name="update" id="updateBtn" style="display:none;">Update Doctor</button>
    </form>
</div>
</div>

<script>
function openModal(mode, data=null){
    document.getElementById("modal").style.display="block";
    if(mode=="add"){
        document.getElementById("modalTitle").innerText="Add Doctor";
        document.getElementById("addBtn").style.display="inline-block";
        document.getElementById("updateBtn").style.display="none";
        document.getElementById("doctorForm").reset();
        document.getElementById("currentPhoto").style.display="none";
    } else if(mode=="edit"){
        document.getElementById("modalTitle").innerText="Edit Doctor";
        document.getElementById("addBtn").style.display="none";
        document.getElementById("updateBtn").style.display="inline-block";
        document.getElementById("doctor_id").value = data.doctor_id;
        document.getElementById("name").value = data.name;
        document.getElementById("email").value = data.email;
        document.getElementById("password").value = data.password; // display as is
        document.getElementById("qualification").value = data.qualification;
        document.getElementById("department").value = data.department;
        document.getElementById("description").value = data.description;
        if(data.photo){
            document.getElementById("currentPhoto").src="../doctor_photo/"+data.photo;
            document.getElementById("currentPhoto").style.display="block";
        } else {
            document.getElementById("currentPhoto").style.display="none";
        }
    }
}
function closeModal(){ document.getElementById("modal").style.display="none"; }
</script>

</body>
</html>
