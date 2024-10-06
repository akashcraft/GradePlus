var topinfo = $("h2.top-info-header").text();
var isCourseOpen = false;
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
    $("h2.top-info-header").text(topinfo);
})

//Add or Enroll Course Modal
$(".addenrolcourse").click(()=>{
    if ($("p.addenrolcourse-text").attr("id")==="enroltrue"){
        $("div.modal-content h4").text("Enter Course Code");
        $("div.course-name").hide();
        $("div.upload-banner").hide();
    } else {
        $("div.modal-content h4").text("Add Course");
    }
    $("div.modal").fadeIn(100);
})

$("a.addenrol-modal-close").click(() => {
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

    if (!bannerFile) {
        bannerFile = new File([""], "/img/card.jpg", { type: "image/jpeg" });
    }

    formData.append("coursecode", courseCode);
    formData.append("coursename", courseName);
    formData.append("banner", bannerFile);
    formData.append("instructorname", $("span.user-name").text());
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
                $("div.modal").fadeOut(100);
                $("div.input-field input").val("");
            }
        }
    });
})

$("a.addenrol-modal-cancel").click(() => {
    $("div.modal").fadeOut(100);
    $("div.input-field input").val("");
})

//Opening a Course
$("div.course-card").click((event) => {
    isCourseOpen = true;
    $("ul.side-nav").animate({left: '0'}, {
        duration: 100,
        easing: 'swing'
    });
    var coursecode = $(event.currentTarget).find("span.card-title").text();
    $("p.side-nav-course-code").text(coursecode);
    $("div.courseholder").fadeOut(200,()=>{
        $("div.coursedash").fadeIn(200).css("display", "flex");
        $("h3.coursedash-header").text("Assignments");
    });
});

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

//Testing Only
//$("div.course-card").click();

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

//Finish Loading
$(window).on("load", () => {
    $("div.loader").fadeOut(200);
    setTimeout(() => {
        $("div.mainapp").fadeIn(200);
    }, 200);
    $("#file-picker-btn").click(()=>{
        $("input[name='coursebanner']").click();
    });
    $('#coursecode').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});