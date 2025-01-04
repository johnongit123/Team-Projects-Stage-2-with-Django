window.addEventListener("load", function () {
    // hide all to start with
    $(".new-project-task-button").hide()


    $("#switch-project").change(function () {
        const projectID = $(this).val()

        // handle when all projects is selected
        if (projectID === "0") {
            $("#to-do-list > li").show()
            $(".new-project-task-button").hide()
            return
        }

        // show new project task button
        console.log("Project ID:", projectID)
        $(".new-project-task-button[data-project-id="+projectID+"]").show()

        // only show tasks with the selected project id
        $("#to-do-list > li").each(function () {
            const element = $(this)
            console.log("Li Element:", element)
            element.show()
            if (element.attr("data-project-id") !== projectID) {
                element.hide()
            }
        })
    })

    // used to toggle classes on specific elements
    $(".toggle-class").on("click", function () {
        const taskID =  $(this).attr("data-target-id")
        const toggleClass =  $(this).attr("data-toggle-class")
        $("#" + taskID).toggleClass(toggleClass)
    })

    const taskLists = $(".task-list")

    // when edit button is clicked - copy task data to edit form
    taskLists.on("click", ".edit-task", function () {
        //console.log("edit task button clicked")

        // get data attributes
        const taskID =  $(this).attr("data-task-id")
        const taskName =  $(this).attr("data-task-name")
        const taskColour =  $(this).attr("data-task-colour")
        const taskDescription = $(this).attr("data-task-description")
        const taskDeadline = $(this).attr("data-task-deadline")
        const taskDuration = $(this).attr("data-task-duration")
        const taskStatus = $(this).attr("data-task-status")

        // copy attributes to input fields
        $("#edited-task-id").val(taskID)
        $("#edited-task-name").val(taskName)
        $("#edited-task-colour").val(taskColour)
        $("#edited-task-description").val(taskDescription)
        $("#edited-task-deadline").val(taskDeadline)
        $("#edited-task-duration").val(taskDuration)
        $("#edited-task-status").val(taskStatus)
    })

    // when X button clicked
    taskLists.on("click", ".task .red-circle-button", function () {
        //console.log("delete task button clicked")

        // copy task id to confirm delete form
        const taskID =  $(this).attr("data-task-id")
        $("#delete-task-id").val(taskID)
    })

    moveCompletedTasksToEnd()
})

function moveCompletedTasksToEnd() {
    /* Move Completed Tasks to end of respective list */
    $(".task").each(function () {
        // check if task is completed (has the check circle icon)
        if ($(this).has("i.bx.bx-check-circle").length === 0) {
            return
        }

        const projectID = $(this).attr("data-project-id")
        if (projectID) {
            // move to the end of project to do list
            $("#to-do-list").append(this)
        }
        else {
            // move to the end of private to do list
            $("#to-do-list1").append(this)
        }
    })
}