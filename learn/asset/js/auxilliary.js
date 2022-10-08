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