// ul element containing the tasks
const to_do_list = $('#to-do-list')



// ### Edit Task Event Listeners ###

// when edit button is clicked
$('#edit-tasks').on("click", () => {

    if ($('#edit-tasks').is(':checked')) {
        // when editing tasks
        to_do_list.addClass('editing')

        // allow edit text for task title & desc
        $('#to-do-list li').find('h3, img').attr('contenteditable', true);
    }
    else {
        // when finished editing tasks

        // remove editing styles
        to_do_list.removeClass('editing')

        // set contenteditable to false for task title & desc
        $('#to-do-list li').find('h3, img').attr('contenteditable', false);
    }

})


// when mark complete clicked
to_do_list.on("click", "li .top-section input[type='checkbox']", (event) => {

    let checkbox_clicked = $(event.target)
    if (checkbox_clicked.is(':checked'))
        checkbox_clicked.closest('li').remove()
    // else
    //     checkbox_clicked.closest('li').removeClass('complete')

});

// when X button clicked
to_do_list.on("click", "li .top-section button", (event) => {

    $(event.target).closest('li').remove();

});





// ### New Task Event Listeners ###

// when new task clicked
$('#create-new-task').on("click", () => {

    if ($('#create-new-task').is(':checked')) {
        $('#form-background').css('display', 'initial')


        // set the section minimum height to accommodate new task form
        const top = $('#new-task-form').offset().top
        const height =  $('#new-task-form').outerHeight()
        const sectionMinHeight = top + height + 25
        $('#to-do-list-section').css('min-height', Math.max(sectionMinHeight, $(document).height()) + 'px')
    }
    else {
        $('#form-background').css('display', 'none')
        $('#to-do-list-section').css('min-height', '0')
    }


})


// when form exit button clicked
$('#close-new-task-form').on("click", () => {

    $('#create-new-task').click(); // click create-new-task again to hide form

})


// when new task form submitted
$("#new-task-form").on("submit", (event) => {

    // get the input elements
    const taskTitle = $('#task-title-input')
    const titleColor = $('#task-color-input')
    const taskDesc = $('#task-desc-input')

    // add the task to the list using input element values
    // prepend adds to start (append adds to end)
    $('#to-do-list').prepend(`
    <li>
        <div class="top-section" style="background-color: ${titleColor.val()};">
            <h3>${taskTitle.val()}</h3>
            <label class="edit-option"><input type="checkbox">Mark Complete</label>
            <button type="button" class="edit-option">🞭</button>
        </div>
        <img src="../images/blank-user.png" alt="">
    </li>
    `)

    // reset form to default values
    taskTitle.val(null)
    titleColor.val('royalblue')
    taskDesc.val(null)

    // click new task button again to hide form
    $('#create-new-task').click();

    // prevent page reloading on submit
    event.preventDefault()
s
});


