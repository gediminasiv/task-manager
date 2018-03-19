$(document).ready(function(){
    $('#appbundle_task_client').change(function(){
        return false;
        var clientId = $(this).val() == '-' ? 0 : $(this).val();

        $('#appbundle_task_project').html('');

        $.get('/project-row/' + clientId, function (data) {

            for (key in data.projects) {
                project = data.projects[key];

                $('#appbundle_task_project').append('<option value="' + project.id + '">' + project.title + '</option>');
            }

            $('#appbundle_task_project').val(data.projects[0].id);
        });
    });
});
