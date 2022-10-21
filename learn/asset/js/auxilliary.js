function fetchsubject() {
    var classid = $("#classid").val();
    var action = 'fetchsubject';
    err = '<option value="">select</option>';
    if (classid != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                action: action,
            },
            success: function (data) {
                $("#subject").html(data);
            },
        });
    } else {
        alert("Select Class");
        $("#subject").html(err);
    }
}

function fetchscheme() {
    var subject = $("#subject").val();
    var action = 'fetchscheme';
    if (subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: action,
            },
            success: function (data) {
                $("#schemedata").html(data);
            },
        });
    } else {
        err = '<option value="">select</option>';
        alert("Select Subect");
        $("#subject").html(err);
    }
}

function add_topic_to_scheme() {
    var classid = $("#classid").val();
    var subject = $("#subject").val();
    var topic = $("#topic").val();
    var week = $("#week").val();
    var action = 'add_topic_to_scheme';
    if ((classid != "") & (subject != "") & (topic != "") & (week != "")) {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                subject: subject,
                topic: topic,
                week: week,
                action: action,
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchscheme',
            },
            success: function (data) {
                $("#schemedata").html(data);
                $("#topic").val("");
            },
        });
    } else {
        alert("One of the required details is missing. Check and try again");
    }
}

function modify_topic_in_scheme() {
    var classid = $("#classid").val();
    var subject = $("#subject").val();
    var topic = $("#topic").val();
    var week = $("#week").val();
    var topicid = $("#topicid").val();
    var action = 'modify_topic_in_scheme';
    if ((classid != "") & (subject != "") & (topic != "") & (week != "")) {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                subject: subject,
                topic: topic,
                week: week,
                topicid: topicid,
                action: action,
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchscheme',
            },
            success: function (data) {
                $("#schemedata").html(data);
            },
        });
    } else {
        alert("One of the required details is missing. Check and try again");
    }
}

function remove_topic_from_scheme() {
    var classid = $("#classid").val();
    var subject = $("#subject").val();
    var topic = $("#topic").val();
    var week = $("#week").val();
    var topicid = $("#topicid").val();
    var action = 'remove_topic_from_scheme';
    if ((classid != "") & (subject != "") & (topic != "") & (week != "")) {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                subject: subject,
                topic: topic,
                week: week,
                topicid: topicid,
                action: action,
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchscheme',
            },
            success: function (data) {
                $("#schemedata").html(data);
                $("#classid").val("");
                $("#subject").val("");
                $("#week").val("");
                $("#topic").val("");
            },
        });
    } else {
        alert("One of the required details is missing. Check and try again");
    }
}


function fetchnote() {
    var subject = $("#subject").val();
    var action = 'fetchtopic';
    if (subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: action,
            },
            success: function (data) {
                $("#topic_list").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchnote',
            },
            success: function (data) {
                $("#notedata").html(data);
            },
        });
    } else {
        err = '<option value="">select</option>';
        alert("Select Subect");
        $("#subject").html(err);
    }
}

function switch_type() {
    var note_type = document.getElementById("note_type");
    var selectedValue = note_type.options[note_type.selectedIndex].value;
    if (selectedValue == "text") {
        $('#summernote_div').show();
        $('#weblink_div').hide();
        $('#summernote').attr('required', '');
        $('#summernote').attr('data-error', 'Note of Lesson field is required.');
    }
    else if (selectedValue == "online") {
        $('#weblink_div').show();
        $('#summernote_div').hide();
        $('#weblink').attr('required', '');
        $('#weblink').attr('data-error', 'Link to Online Note of Lesson field is required.');
    }
    else {
        alert("Select a Learning Material Type");
        $('#weblink_div').hide();
        $('#summernote_div').hide();
    }
}


function add_enote() {
    var classid = $("#classid").val();
    var subject = $("#subject").val();
    var topic = $("#topic_list").val();
    var note_type = $("#note_type").val();
    var text = $("#summernote").val();
    var link = $("#weblink").val();
    var action = 'add_enote';
    var context = 'enote';
    if (note_type == "text") {
        var content = text;
    } else if (note_type == "online") {
        var content = link;
    }
    if ((classid != "") & (subject != "") & (topic != "") & (content != "") & (note_type != "")) {

        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                subject: subject,
                topic: topic,
                note_type: note_type,
                content: content,
                context: context,
                action: action,
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchnote',
            },
            success: function (data) {
                $("#notedata").html(data);
                $("#note_type").val("");
                $("#summernote").val("");
                $("#weblink").val("");
                $('#weblink_div').hide();
                $('#summernote_div').hide();
            },
        });
    } else {
        alert("One of the required details is missing. Check and try again");
    }
}

function modify_enote() {
    var noteid = $("#noteid").val();
    var classid = $("#classid").val();
    var subject = $("#subject").val();
    var topic = $("#topic_list").val();
    var note_type = $("#note_type").val();
    var text = $("#summernote").val();
    var link = $("#weblink").val();
    var action = 'modify_enote';
    var context = 'enote';
    if (note_type == "text") {
        var content = text;
    } else if (note_type == "online") {
        var content = link;
    }
    if ((classid != "") & (subject != "") & (topic != "") & (content != "") & (note_type != "")) {

        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                subject: subject,
                topic: topic,
                note_type: note_type,
                content: content,
                context: context,
                action: action,
                noteid: noteid,
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchnote',
            },
            success: function (data) {
                $("#notedata").html(data);
            },
        });
    } else {
        alert("One of the required details is missing. Check and try again");
    }
}
function remove_enote() {
    var noteid = $("#noteid").val();
    var classid = $("#classid").val();
    var subject = $("#subject").val();
    var topic = $("#topic_list").val();
    var note_type = $("#note_type").val();
    var text = $("#summernote").val();
    var link = $("#weblink").val();
    var action = 'remove_enote';
    var context = 'enote';
    if (note_type == "text") {
        var content = text;
    } else if (note_type == "online") {
        var content = link;
    }
    if ((classid != "") & (subject != "") & (topic != "") & (content != "") & (note_type != "")) {

        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                subject: subject,
                topic: topic,
                note_type: note_type,
                content: content,
                context: context,
                action: action,
                noteid: noteid,
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchnote',
            },
            success: function (data) {
                $("#notedata").html(data);
                $("#classid").val("");
                $("#subject").val("");
                $("#topic_list").val("");
                $("#note_type").val("");
                $("#summernote").val("");
                $("#weblink").val("");
                $('#weblink_div').hide();
                $('#summernote_div').hide();
            },
        });
    } else {
        alert("One of the required details is missing. Check and try again");
    }
}

function fetchtask() {
    var subject = $("#subject").val();
    var action = 'fetchtopic';
    if (subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: action,
            },
            success: function (data) {
                $("#topic_list").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchtask',
            },
            success: function (data) {
                $("#notedata").html(data);
            },
        });
    } else {
        err = '<option value="">select</option>';
        alert("Select Subect");
        $("#subject").html(err);
    }
}

function add_task() {
    var classid = $("#classid").val();
    var subject = $("#subject").val();
    var topic = $("#topic_list").val();
    var grade = $("#grade").val();
    var due_date = $("#due_date").val();
    var note_type = $("#note_type").val();
    var text = $("#summernote").val();
    var link = $("#weblink").val();
    var action = 'add_task';
    var context = 'task';
    if (note_type == "text") {
        var content = text;
    } else if (note_type == "online") {
        var content = link;
    }
    if ((classid != "") & (subject != "") & (topic != "") & (content != "") & (note_type != "")) {

        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                subject: subject,
                topic: topic,
                note_type: note_type,
                content: content,
                grade: grade,
                due_date: due_date,
                context: context,
                action: action,
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchtask',
            },
            success: function (data) {
                $("#notedata").html(data);
                $("#note_type").val("");
                $("#summernote").val("");
                $("#weblink").val("");
                $("#grade").val("");
                $("#due_date").val("");
                $('#weblink_div').hide();
                $('#summernote_div').hide();
            },
        });
    } else {
        alert("One of the required details is missing. Check and try again");
    }
}

function modify_task() {
    var classid = $("#classid").val();
    var subject = $("#subject").val();
    var topic = $("#topic_list").val();
    var grade = $("#grade").val();
    var due_date = $("#due_date").val();
    var note_type = $("#note_type").val();
    var text = $("#summernote").val();
    var link = $("#weblink").val();
    var action = 'modify_task';
    var context = 'task';
    var questid = $("#questid").val();
    if (note_type == "text") {
        var content = text;
    } else if (note_type == "online") {
        var content = link;
    }
    if ((classid != "") & (subject != "") & (topic != "") & (content != "") & (note_type != "")) {

        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                subject: subject,
                topic: topic,
                note_type: note_type,
                content: content,
                grade: grade,
                due_date: due_date,
                context: context,
                action: action,
                questid: questid,
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchtask',
            },
            success: function (data) {
                $("#notedata").html(data);
            },
        });
    } else {
        alert("One of the required details is missing. Check and try again");
    }
}
function remove_task() {
    var classid = $("#classid").val();
    var subject = $("#subject").val();
    var topic = $("#topic_list").val();
    var grade = $("#grade").val();
    var due_date = $("#due_date").val();
    var note_type = $("#note_type").val();
    var text = $("#summernote").val();
    var link = $("#weblink").val();
    var action = 'remove_task';
    var context = 'task';
    var questid = $("#questid").val();
    if (note_type == "text") {
        var content = text;
    } else if (note_type == "online") {
        var content = link;
    }
    if ((classid != "") & (subject != "") & (topic != "") & (content != "") & (note_type != "")) {

        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                classid: classid,
                subject: subject,
                topic: topic,
                note_type: note_type,
                content: content,
                grade: grade,
                due_date: due_date,
                context: context,
                action: action,
                questid: questid,
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                subject: subject,
                action: 'fetchtask',
            },
            success: function (data) {
                $("#notedata").html(data);
                $("#classid").val("");
                $("#subject").val("");
                $("#topic_list").val("");
                $("#note_type").val("");
                $("#summernote").val("");
                $("#weblink").val("");
                $("#grade").val("");
                $("#due_date").val("");
                $('#weblink_div').hide();
                $('#summernote_div').hide();
            },
        });
    } else {
        alert("One of the required details is missing. Check and try again");
    }
}

function class_dashboard() {
    var allocated_class = $("#allocated_class").val();
    var action = 'load_dashboard';
    if (allocated_class != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_class: allocated_class,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#loader").show();
                    $("#board").hide();
                },
            },
            success: function (data) {
                $("#class_dashboard").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#loader").hide();
            }
        });
    } else {
        alert("Select Allocated Class");
    }
}

function class_dashboard() {
    var allocated_class = $("#allocated_class").val();
    var action = 'load_dashboard';
    if (allocated_class != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_class: allocated_class,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#loader").show();
                    $("#board").hide();
                },
            },
            success: function (data) {
                $("#class_dashboard").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#loader").hide();
            }
        });
    } else {
        err = '<option value="">Select Allocated Class</option>';
        alert("Select Allocated Class");
        $("#allocated_class").html(err);
    }
}
function show_learners() {
    var allocated_class = $("#allocated_class").val();
    var action = 'show_learners';
    if (allocated_class != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_class: allocated_class,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#response_loader").show();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#response").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#response_loader").hide();
                $("#response").show();
            }
        });
    } else {
        err = '<option value="">Select Allocated Class</option>';
        alert("Select Allocated Class");
        $("#allocated_class").html(err);
    }
}
function show_subjects() {
    var allocated_class = $("#allocated_class").val();
    var action = 'show_subjects';
    if (allocated_class != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_class: allocated_class,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#response_loader").show();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#response").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#response_loader").hide();
                $("#response").show();
            }
        });
    } else {
        err = '<option value="">Select Allocated Class</option>';
        alert("Select Allocated Class");
        $("#allocated_class").html(err);
    }
}
function show_fully_paid() {
    var allocated_class = $("#allocated_class").val();
    var action = 'show_fully_paid';
    if (allocated_class != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_class: allocated_class,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#response_loader").show();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#response").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#response_loader").hide();
                $("#response").show();
            }
        });
    } else {
        err = '<option value="">Select Allocated Class</option>';
        alert("Select Allocated Class");
        $("#allocated_class").html(err);
    }
}


function toggle_modify() {
    $("#view_profile").hide();
    $("#edit_profile").show();
}


function modify_learner() {
    var fullname = $("#fullname").val();
    var gender = $("#gender").val();
    var date_of_birth = $("#date_of_birth").val();
    var phone = $("#phone").val();
    var email = $("#email").val();
    var action = 'modify_learner';
    var img = $("#imageInput").val();
    if (img != "") {
        upload = 'yes';
        var imagebase64data = myCanvas.toDataURL("image/png");
        imagebase64data = imagebase64data.replace('data:image/png;base64,', '');
    } else {
        upload = 'no';
    }

    if (fullname != "" & gender != "" & date_of_birth != "" & phone != "" & email != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                fullname: fullname,
                gender: gender,
                date_of_birth: date_of_birth,
                phone: phone,
                email: email,
                imagebase64data: imagebase64data,
                action: action,
                upload: upload,
                beforeSend: function () {
                    // Show image container
                    $("#view_profile").hide();
                    $("#edit_profile").hide();
                    $("#loader").show();
                },
            },
            success: function (data) {
                $("#info").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#edit_profile").show();
                $("#loader").hide();
            }
        });
        var allocated_class = $("#classid").val();
        var action = 'show_learners';
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_class: allocated_class,
                action: action,
            },
            success: function (data) {
                $("#response").html(data);
            },
        });
    } else {
        alert("One of the required information is missing");
    }
}   


function scoresheet_dashboard() {
    var allocated_subject = $("#allocated_subject").val();
    var action = 'load_scoresheet_dashboard';
    if (allocated_subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_subject: allocated_subject,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#loader").show();
                    $("#board").hide();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#myscoresheet_dashboard").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#loader").hide();
            }
        });
    } else {
        alert("Select Allocated Subject");
    }
}

function ca_score_manager(){
    var allocated_subject = $("#allocated_subject").val();
    var action = 'ca_score_manager';
    if (allocated_subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_subject: allocated_subject,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#loader").show();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#response").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#loader").hide();
                $("#response").show();
            }
        });
    } else {
        alert("Select Allocated Subject");
    } 
}
function exam_score_manager(){
    var allocated_subject = $("#allocated_subject").val();
    var action = 'exam_score_manager';
    if (allocated_subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_subject: allocated_subject,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#loader").show();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#response").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#loader").hide();
                $("#response").show();
            }
        });
    } else {
        alert("Select Allocated Subject");
    } 
}


function total_score_manager(){
    var allocated_subject = $("#allocated_subject").val();
    var action = 'total_score_manager';
    if (allocated_subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_subject: allocated_subject,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#loader").show();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#response").html(data);
            },
            complete: function (data) {
                // Hide image container
                $("#loader").hide();
                $("#response").show();
            }
        });
    } else {
        alert("Select Allocated Subject");
    } 
}


        
function submit_ca_scores() {
    var action = "record_ca_scores_for_all";
    var allocated_subject = $("#allocated_subject").val();
    var score = document.getElementsByName('score[]');
    var userid = document.getElementsByName('userid[]');
    const  all_users = [];
    const  all_scores = [];
    for (var i = 0 , y = 0;
         i < userid.length ,
          y < score.length ; 
         i++,
         y++) {
            if( userid[i] != ""){
               var user = userid[i]; 
            }else{
                var user = 0;
            }
            if( score[y] != ""){
               var grade = score[y]; 
            }else{
                var grade = 0;
            }
        all_users.push(user.value);
        all_scores.push(grade.value);
    }
   
    if (allocated_subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_subject: allocated_subject,
                all_users : all_users,
                all_scores : all_scores,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#response_loader").show();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        var allocated_subject = $("#allocated_subject").val();
        var action = 'ca_score_manager';
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_subject: allocated_subject,
                action: action,
            },
            success: function (data) {
                $("#response").html(data);
            },
            
            complete: function (data) {
                // Hide image container
                $("#response_loader").hide();
                $("#response").show();
            }
        });
    } else {
        alert("Select Allocated Subject");
    }
}

function submit_exam_scores() {
    var action = "record_exam_scores_for_all";
    var allocated_subject = $("#allocated_subject").val();
    var score = document.getElementsByName('score[]');
    var userid = document.getElementsByName('userid[]');
    const  all_users = [];
    const  all_scores = [];
    for (var i = 0 , y = 0;
         i < userid.length ,
          y < score.length ; 
         i++,
         y++) {
            if( userid[i] != ""){
               var user = userid[i]; 
            }else{
                var user = 0;
            }
            if( score[y] != ""){
               var grade = score[y]; 
            }else{
                var grade = 0;
            }
        all_users.push(user.value);
        all_scores.push(grade.value);
    }
   
    if (allocated_subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_subject: allocated_subject,
                all_users : all_users,
                all_scores : all_scores,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#response_loader").show();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        var allocated_subject = $("#allocated_subject").val();
        var action = 'exam_score_manager';
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_subject: allocated_subject,
                action: action,
            },
            success: function (data) {
                $("#response").html(data);
            },
            
            complete: function (data) {
                // Hide image container
                $("#response_loader").hide();
                $("#response").show();
            }
        });
    } else {
        alert("Select Allocated Subject");
    }
}

function weekly_score_manager() {
    let week = prompt("Please enter week number(Digits only)", "1");
    if (week != null & week >= 1 & week <= 12) {
        var week_num = week;
        var allocated_subject = $("#allocated_subject").val();
        var action = 'weekly_score_manager';
        if (allocated_subject != "") {
            $.ajax({
                url: "../../app/ajax_query.php",
                method: "POST",
                data: {
                    allocated_subject: allocated_subject,
                    week_num: week_num,
                    action: action,
                    beforeSend: function () {
                        // Show image container
                        $("#loader").show();
                        $("#response").hide();
                    },
                },
                success: function (data) {
                    $("#response").html(data);
                },
                complete: function (data) {
                    // Hide image container
                    $("#loader").hide();
                    $("#response").show();
                }
            });
        } else {
            alert("Select Allocated Subject");
        }
    } else {
        alert('You need to input week number between 1 and 12');
    }
}

function record_weekly_scores_for_all() {
    var action = "record_weekly_scores_for_all";
    var allocated_subject = $("#allocated_subject").val();
    var week_num = $("#week_num").val();
    var score = document.getElementsByName('score[]');
    var userid = document.getElementsByName('userid[]');
    const  all_users = [];
    const  all_scores = [];
    for (var i = 0 , y = 0;
         i < userid.length ,
          y < score.length ; 
         i++,
         y++) {
            if( userid[i] != ""){
               var user = userid[i]; 
            }else{
                var user = 0;
            }
            if( score[y] != ""){
               var grade = score[y]; 
            }else{
                var grade = 0;
            }
        all_users.push(user.value);
        all_scores.push(grade.value);
    }
   
    if (allocated_subject != "") {
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                week_num: week_num,
                allocated_subject: allocated_subject,
                all_users : all_users,
                all_scores : all_scores,
                action: action,
                beforeSend: function () {
                    // Show image container
                    $("#response_loader").show();
                    $("#response").hide();
                },
            },
            success: function (data) {
                $("#info").html(data);
            },
        });
        var allocated_subject = $("#allocated_subject").val();
        var weekly = week_num.slice(5);
        var action = 'weekly_score_manager';
        $.ajax({
            url: "../../app/ajax_query.php",
            method: "POST",
            data: {
                allocated_subject: allocated_subject,
                week_num: weekly,
                action: action,
            },
            success: function (data) {
                $("#response").html(data);
            },
            
            complete: function (data) {
                // Hide image container
                $("#response_loader").hide();
                $("#response").show();
            }
        });
    } else {
        alert("Select Allocated Subject");
    }
}