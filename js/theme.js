
function themeSet() {
    if (localStorage.getItem("isDark") == "true") {
        $("i.theme").animate({
            rotate: '360deg',
            opacity: '0'
        }, 250, () => {
            $("i.theme").animate({
                "opacity": "1",
                "rotate": '0deg'
            }, 250);
            checkTheme();
        });
    } else {
        $("i.theme").animate({
            rotate: '360deg',
            opacity: '0'
        }, 250, () => {
            $("i.theme").animate({
                "opacity": "1",
                "rotate": '0deg'
            }, 250);
            checkTheme();
        });
    }
}

$("a.theme").click(function() {
    var isDark = localStorage.getItem("isDark");
    localStorage.setItem("isDark", isDark == "true" ? "false" : "true");
    themeSet();
});

function checkTheme() {
    if (localStorage.getItem("isDark") == null) {
        localStorage.setItem("isDark", "false");
    }
    if (localStorage.getItem("isDark") == "true") { // Dark Mode
        $("i.theme").text("light_mode");
        $("span.theme").text("Light");
        $("img.indexback").fadeOut(250);
        $("img.indexback2").fadeIn(250);
        $(".nav-wrapper").removeClass("green darken-1").addClass("grey darken-4");
        $(".btn-large").removeClass("green darken-1").addClass("grey darken-4");
        $(".page-footer-holder").removeClass("green darken-1").addClass("grey darken-4");
        $(".bwcolor").removeClass("white black-text").addClass("grey darken-4 white-text");
        $(".bwcolortext").removeClass("black-text").addClass("white-text");
        $(".bwcolornotext").removeClass("white").addClass("grey darken-4");
        $("img.addcourseimg").attr("src","img/addcoursedark.png");
        $("body").css({"--transparent":"rgba(0,0,0,0.75)","--hover-color":"rgb(20,20,20)","--hover-color-2":"rgb(60,60,60)","--bwcolor":"rgb(33,33,33)","--font-color": "white", "--warn-color": "yellow","background-color":"black"});
    } else { // Light Mode
        $("i.theme").text("dark_mode");
        $("span.theme").text("Dark");
        $("img.indexback2").fadeOut(250);
        $("img.indexback").fadeIn(250);
        $(".nav-wrapper").removeClass("grey darken-4").addClass("green darken-1");
        $(".btn-large").removeClass("grey darken-4").addClass("green darken-1");
        $(".page-footer-holder").removeClass("grey darken-4").addClass("green darken-1");
        $(".bwcolor").removeClass("grey darken-4 white-text").addClass("white black-text");
        $(".bwcolortext").removeClass("white-text").addClass("black-text");
        $(".bwcolornotext").removeClass("grey darken-4").addClass("white");
        $("img.addcourseimg").attr("src","img/addcourse.png");
        $("body").css({"--transparent":"rgba(255,255,255,0.75)","--hover-color":"rgb(200,200,200)","--hover-color-2":"rgb(240,240,240)","--bwcolor":"rgb(252,252,252)","--font-color": "black", "--warn-color": "red","background-color":"#225325"});
    }
}

checkTheme();
