$("#typed").change(function() {
  if ($(this).val() == "text") {
    $('#lessondiv').show();
    $('#lesson').attr('required', '');
    $('#lesson').attr('data-error', 'This field is required.');
  } else {
    $('#lessondiv').hide();
    $('#lesson').removeAttr('required');
    $('#lesson').removeAttr('data-error');
  }
});
$("#typed").trigger("change");

