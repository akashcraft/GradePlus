<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/styles-classlist.css">
</head>

<body>
    <div class="table_professor">
        <table class="responsive-table bwcolor">
            <tr><!-- Professor Info -->
                <td><i class="material-symbols-outlined accounticon" class="profile prof-img">account_circle</i><!--<img src="img/profilepics/batman_profilepic.jpg" class="profile prof-img">--></td>
                <td>
                    <div class="profname">
                        <h4>Professor: Dr. John Doe</h4>
                    </div>
                    <div class="profinfo">
                        <p> Email: examplegmail.com </p>
                        <p> Office Hours: 1:00 PM - 3:00 PM </p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table_students bwcolortext">
        <table class="highlight centered responsive-table bwcolor">
            <thead>
                <th></th>
                <th>Name</th>
                <th>Username</th>
            </thead>
            <tbody id="student-body">
                <!-- Data Will populate here -->
                <tr>
                    <td><i class="material-symbols-outlined accounticon" class="profile student-img">account_circle</i><!--<img src="img/profilepics/batman_profilepic.jpg" class="profile student-img">--></td>
                    <td>Bat</td>
                    <td>Man</td>
                    <td>theman@marvel.com</td>
                    <td>12345678</td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</body>

<script src="js/theme.js"></script>

<!-- Include jQuery (for simplicity) 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->

<!--
<script>
    $(document).ready(function() {
        var coursecode = "6400";
        $.ajax({
            url: "services/get-students.php",
            type: "POST",
            data: {
                authorize: 'gradeplus',
                coursecode: coursecode
            },
            success: function (response) {
                if (response.success) {
                    response.data.forEach(function(student) {
                        var profilePicSrc = 'data:image/jpeg;base64,' + student.profilePicture;
                        console.log(profilePicSrc);
                        console.log(student.dname);
                        console.log(student.username);
                        var row = `
                            <tr>
                                <td><img src="${profilePicSrc}" alt="Profile Picture" class="profile-pic"></td>
                                <td>${student.dname}</td>
                                <td>${student.username}</td>
                            </tr>`;
                        $('#student-body').append(row);
                    });
                } else if (response.error) {
                    $('#error-message').text(response.message);
                } else {
                    $('#error-message').text('No students found for this course.');
                }
            },
            error: function(xhr, status, error) {
                console.log("Failed to update classlist: ", status, error);
            }
        });
    });

</script>

-->
