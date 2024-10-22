function main() {
    var isCourseOpen = false;
    var dname = $("span.display-name").text();
    //Switch to Account Settings
    $("a.accountservice").click(()=>{
        $("div.course-list").fadeOut(200);
        $("div.account-settings").fadeIn(200);
        $("h2.top-info-header").text("Account Settings");
        if (isCourseOpen){
            $("a.backuserdashboard").click();
        };
    })

    //Switch to Course List
    $("a.account-settings-back").click(()=>{
        $("div.course-list").fadeIn(200);
        $("div.account-settings").fadeOut(200);
        $("h2.top-info-header").text("Welcome "+dname+"!");
    })

    //Edit Accounts Settings
    $(".edit-account-settings-btn").click(()=>{
        $("div.update-form").fadeIn(200);
        $("div.account-item").fadeOut(200);
    })

    // Return to Account Settings
    $(".return-btn").click(() => {
        $("div.update-form").fadeOut(200);
        $("div.account-item").fadeIn(200);
    });

    // Update Account Settings
    $(".save-btn").click(() => {

        // Get the new values from the input fields
        let newname = $("#new-user-name").val()
        let newdname = $("#new-display-name").val();
        let newemail = $("#new-account-email").val();
        let newpassword = $("#new-account-password").val();
        let profilePicture = $("#new-profile-pic")[0].files[0];

        // Check if any of the fields are empty
        if (!newname && !newdname && !newemail && !newpassword && !profilePicture) {
            $("p.status-text").text("Fields cannot be left blank");
            $("p.status-text").slideDown();
            setTimeout(() => {
                $("p.status-text").slideUp();
            }, 3000);
            return;
        }

        // Update the username backend integration
        if (newname) {
            $.ajax({
                url: 'services/update-username.php', 
                type: 'POST', // Send the data using POST
                data: {
                    authorize: "gradeplus", // Send the authorization token
                    newname: newname }, // Send the new username
                dataType: 'json',  
                success: function(response) {
                    if (response.success) {
                        console.log("Username updated successfully!");
                        window.location.reload(true); //Adding true here clears the browser cache, ensuring the account.php file updates the session variables.
                    } else if (response.error) {
                        console.error("Error updating username.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update username:", status, error);
                }
            });
        }

        //See username update ajax for comments
        if (newdname) {
            $.ajax({
                url: 'services/update-dname.php',
                type: 'POST',
                data: { 
                    authorize: "gradeplus",
                    newdname: newdname },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                       console.log("Display name updated successfully!");
                       window.location.reload(true); 
                    } else if (response.error) {
                        console.error("Error updating display name.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update display name:", status, error);
                }
            });
        }

        //See username update ajax for comments
        if (newemail) {
            $.ajax({
                url: 'services/update-email.php',
                type: 'POST',
                data: {
                    authorize: "gradeplus",  
                    newemail: newemail },
                dataType: 'json', 
                success: function(response) {
                    if (response.success) {
                        console.log("Email updated successfully!");
                        window.location.reload();
                    } else if (response.error) {
                        console.error("Error updating Email.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update Email:", status, error);
                }
            });
        }

        // See username update ajax for comments. However, success handling logs out the user instead of simply window refreshing.
        if (newpassword) {
            $.ajax({
                url: 'services/update-password.php',
                type: 'POST',
                data: { 
                    authorize: "gradeplus",
                    newpassword: newpassword },
                dataType: 'json',  
                success: function(response) {
                    if (response.success) {
                        console.log("Password updated successfully!");
                        // Log out the user after updating the password
                        $.ajax({
                            url: 'services/logout.php',
                            type: 'POST',
                            data: { 
                                authorize: "gradeplus"
                            },
                            dataType: 'json',
                            success: function(logoutResponse) {
                                if (logoutResponse.success) {
                                    console.log("Logged out successfully.");
                                    // Redirect to the login page or home page after logout
                                    window.location.href = 'login.php';
                                } else {
                                    console.error("Error during logout.");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Failed to log out:", status, error);
                            }
                        });
                    } else if (response.error) {
                        console.error("Error updating profile pic.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update profile pic:", status, error);
                }
            });
        }
        
        /* Update the profile picture 

        var formData = new FormData();
        formData.append('profilePicture', profilePicture);
        
        if (profilePicture) {
            $.ajax({
                url: 'services/profilepic-upload.php',
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from automatically transforming the data into a query string
                contentType: false, // Tell jQuery not to set any content type header
                success: function(response) {
                    if (response.success) {
                        console.log("Profile picture updated successfully!");
                        window.location.reload(true);
                    } else if (response.error) {
                        console.error("Error updating profile picture:", response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update profile picture:", status, error);
                }
            });
        } */
    })     
        

    //Delete Account
    $(".delete-account-btn").click(()=>{
        $("div.delete-account-item").fadeOut(200);
        $("div.delete-account-safety").fadeIn(200);
    })

    //Delete Confirmation
    $(".delete-account-confirm-btn").click(()=>{
        $.ajax({
            url: 'services/delete-user.php',
            type: 'POST',
            data: {
                authorize: "gradeplus"
            },
            dataType: 'json', 
            success: function(response) {
                if (response.success) {
                    console.log("User deleted successfully!");
                    window.location.href = 'login.php';
                } else if (response.error) {
                    console.error("Error deleting user.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Failed to delete user: ", status, error);
            }
        });
    })

    //Cancel Delete
    $(".delete-account-cancel-btn").click(()=>{
        $("div.delete-account-safety").fadeOut(200);
        $("div.delete-account-item").fadeIn(200);
    })


    //Add or Enroll Course Modal
    $("a.addenrolcourse").click(()=>{
        if ($("a.addenrolcourse").attr("id")==="enroltrue"){
            $("div.modal-content h4").text("Enter Invite Code");
            $("div.course-name").hide();
            $("div.upload-banner").hide();
            $("a.addenrol-modal-add").hide();
        } else {
            $("div.modal-content h4").text("Add Course");
            $("a.addenrol-modal-enrol").hide();
        }
        $("div.modal").fadeIn(100);
    })

    $("a.addenrol-modal-enrol").click(() => {
        if (!$("input[name='coursecode']").val()) {
            $("p.status-text").text("Field cannot be left blank");
            $("p.status-text").slideDown();
            setTimeout(() => {
                $("p.status-text").slideUp();
            }, 3000);
            return;
        }

        $.ajax({
            url: "services/enroll-course.php",
            type: "POST",
            data: {username: $("span.user-name").text(), invite_code: $("input[name='coursecode']").val(), authorize: "gradeplus"},
            dataType : "json",
            success: (response) => {
                if (response["invalid"] == 1) {
                    $("p.status-text").text("Invalid Invalid Code");
                    $("p.status-text").slideDown();
                    setTimeout(() => {
                        $("p.status-text").slideUp();
                    }, 3000);
                    return;
                } else if (response["error"] == 1) {
                    $("p.status-text").text("500 - Server Error");
                    $("p.status-text").slideDown();
                    setTimeout(() => {
                        $("p.status-text").slideUp();
                    }, 3000);
                    return;
                } else if (response["exists"] == 1) {
                    $("p.status-text").text("Already Enrolled in Course");
                    $("p.status-text").slideDown();
                    setTimeout(() => {
                        $("p.status-text").slideUp();
                    }, 3000);
                    return;
                } else {
                    window.location.reload();
                }
            }
        });
    })

    $("a.addenrol-modal-add").click(() => {
        let formData = new FormData();
        let courseCode = $("input[name='coursecode']").val();
        let courseName = $("input[name='coursename']").val();
        let bannerFile = $("input[name='coursebanner']")[0].files[0];

        if (!courseCode || !courseName) {
            $("p.status-text").text("Fields cannot be left blank");
            $("p.status-text").slideDown();
            setTimeout(() => {
                $("p.status-text").slideUp();
            }, 3000);
            return;
        }

        formData.append("coursecode", courseCode);
        formData.append("coursename", courseName);
        formData.append("banner", bannerFile);
        formData.append("instructor_name", $("span.user-name").text());
        formData.append("instructor_dname", $("span.display-name").text());
        formData.append("authorize", "gradeplus");

        $.ajax({
            url: "services/add-course.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType : "json",
            success: (response) => {
                if (response["success"] != 1) {
                    $("p.status-text").text("500 - Server Error");
                    $("p.status-text").slideDown();
                    setTimeout(() => {
                        $("p.status-text").slideUp();
                    }, 3000);
                    return;
                } else {
                    window.location.reload();
                }
            }
        });
    })

    $("a.addenrol-modal-cancel").click(() => {
        $("div.modal").fadeOut(100);
        $("div.input-field input").val("");
    })

    //Closing a Course
    $("a.backuserdashboard").click(()=>{
        isCourseOpen = false;
        $("ul.side-nav").animate({left: '-20rem'}, {
            duration: 100,
            easing: 'swing'
        });
        $("div.coursedash").fadeOut(200,()=>{
            $("div.courseholder").fadeIn(200);
        });
    })
    
    function loadContent(url, headerText) {
        $("h3.coursedash-header").text(headerText);
        $("div.coursedash-content").fadeOut(200, function () {
            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {
                    $("div.coursedash-content").html(data).fadeIn(200);
                },
                error: function (xhr, status, error) {
                    console.error("Failed to load content:", status, error);
                }
            });
        });
    }

    // Assignments
    $("a.assignments").click(() => {
        loadContent('assignments.php', 'Assignments');
    });

    // Grades
    $("a.grades").click(() => {
        loadContent('grades.php', 'Grades');
    });

    // Peer Reviews
    $("a.peer-reviews").click(() => {
        loadContent('peer_reviews.php', 'Peer Reviews');
    });

    // Discussions
    $("a.discussions").click(() => {
        loadContent('discussions.php', 'Discussions');
    });

    // Classlist
    $("a.classlist").click(() => {
        loadContent('classlist.php', 'Classlist');
    });

    // Settings
    $("a.csettings").click(() => {
        loadContent('course_settings.php', 'Course Settings');
    });

    $("#file-picker-btn").click(()=>{
        $("input[name='coursebanner']").click();
    });
    $('#coursecode').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    function retrieve_courses() {
    //Retrieving Courses
    $.ajax({
        url: "services/retrieve-course.php",
        type: "POST",
        data: {
            "username": $("span.user-name").text(),
            "authorize": "gradeplus"
        },
        dataType : "json",
        success: (response) => {
            if (response["success"] != 1) {
                return;
            } else {
                let courses = response["courses"];
                let courseHolder = $("div.course-list-holder");
                courseHolder.empty();
                courses.forEach((course) => {
                    let pinnedlogo = "keep_off";
                    if (course["pinned"] == 1) {
                        pinnedlogo = "keep";
                    }
                    let courseCard = `
                    <div class="card course-card std-hover bwcolor" style="background-color: var(--bwcolor); position: relative;">
                        <div class="card-image" style="height: 12rem; overflow: hidden;">
                            <img style="object-fit: cover;" src="${course["course_banner"]}" alt="Unable to Load Image">
                            <span class="card-title"><span class="card-title-code">${course["course_code"]}</span>
                        </div>
                        <div class="card-content">
                            <p>${course["course_name"]}</p>
                            <p class='secondary'>${course["instructor_name"]}</p>
                        </div>
                         <a id="${course["invite_code"]}" style="position: absolute; top: 1rem; right: 1rem;" class='pin btn-floating halfway-fab waves-effect waves-light green addenrolcourse'><i
                                class='material-symbols-outlined'>${pinnedlogo}</i></a></span>
                    </div>`;
                    courseHolder.append(courseCard);
                });

                if (courses.length == 0) {
                    courseHolder.append(
                        `<div class="card std-hover bwcolor" style="background-color: var(--bwcolor); position: relative;">
                            <div class="card-image" style="height: 12rem; overflow: hidden;">
                            <img class="addcourseimg" style="object-fit: cover;" src="img/addcourse.png" alt="Unable to Load Image">
                        </div>
                            <div class="card-content">
                                <p>No Courses Found</p>
                            </div>
                        </div>`
                    );
                }

                //Opening a Course
                $("div.course-card").click((event) => {
                    isCourseOpen = true;
                    $("img.side-nav-img").attr("src", $(event.currentTarget).find("img").attr("src"));
                    $("ul.side-nav").animate({left: '0'}, {
                        duration: 100,
                        easing: 'swing'
                    });
                    var coursecode = $(event.currentTarget).find("span.card-title-code").text();
                    var invitecode = $(event.currentTarget).find("a.addenrolcourse").attr("id");
                    $("p.side-nav-course-invite").text(invitecode);
                    $("p.side-nav-course-code").text(coursecode);
                    $("div.courseholder").fadeOut(200,()=>{
                        $("div.coursedash").fadeIn(200).css("display", "flex");
                        $("h3.coursedash-header").text("Assignments");
                    });
                });

                //Pinning a Course
                $("a.pin").click((event)=>{
                    event.stopPropagation();
                    let invite_code = $(event.currentTarget).attr("id");
                    isPinned = $(event.currentTarget).find("i").text() == "keep_off" ? true : false;
                    $.ajax({
                        url: "services/pin-course.php",
                        type: "POST",
                        data: {
                            "username": $("span.user-name").text(),
                            "invitecode": invite_code,
                            "pinned": isPinned ? 1 : 0,
                            "authorize": "gradeplus"
                        },
                        dataType : "json",
                        success: (response) => {
                            if (response["success"] != 1) {
                                return;
                            } else {
                                $("div.course-list-holder").empty();
                                retrieve_courses();
                            }
                        }
                    });
                })
            }
        }});
    }
    retrieve_courses();
}

//Finish Loading
$(window).on("load", () => {   
    $("div.loader").fadeOut(200);
    setTimeout(() => {
        $("div.mainapp").fadeIn(200);
    }, 200);
    main();
});