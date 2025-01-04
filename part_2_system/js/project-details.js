window.addEventListener("load", function () {
    $(".toggle-class > input").on("click", function () {
      console.log("Toggle button clicked")
      const targetID = $(this).parent().attr("data-target-id")
      const targetElem = $(`#${targetID}`)
      const addClass = $(this).parent().attr("data-add-class")
      if ($(this).is(":checked")) {
          targetElem.addClass(addClass)
      }
      else {
            targetElem.removeClass(addClass)
      }
    })


    $("#to-do-list").on("click", ".move-task", function () {
        console.log("Move Task Button Clicked")
        const memberID = $(this).attr("data-member-id")
        const taskID = $(this).attr("data-task-id")
        $("#move-task-id").val(taskID)

        /*console.log($("new-project-member-id"))*/
        $("#project-member-id").find("option").each(function () {
            console.log("Option:", $(this).val(), $(this).attr("selected"));
            if ($(this).val() === memberID) {
                $(this).attr("disabled", "true")
            }
            else {
                $(this).attr("disabled", null)
                $(this).attr("selected", true)
            }
        })
    })

    const cachedMemberTasks = [] // used to cache results of ajax requests
    $(".view-tasks").on("click", function () {
        const memberID = $(this).attr("data-member-id")

        // copy employee card to task view modal
        const modal = $("#project-member-tasks-modal")
        const memberCardElem = $(this).closest(".employee-card").clone()

        // remove the view tasks button
        memberCardElem.find(".view-tasks").remove()

        const employeeList = modal.find(".employee-list")
        employeeList.empty()
        employeeList.append(memberCardElem)

        // add data-member-id to new-project-task modal
        $("#project-task-user-id").val(memberID)

        // check if task data already stored
        if (cachedMemberTasks[memberID] !== undefined) {
            // add task list to to-do-list
            renderTasks(memberID, cachedMemberTasks[memberID])
            return
        }

        // perform ajax request to get task list for member
        console.log("Task data not stored - performing ajax post request")
        $.post(
            "../other/project-queries.php",
            {
                action: "getMemberTaskData",
                projectID: $("html").attr("data-project-id"),
                memberID: memberID
            },
            function (memberTaskData) {
                console.log("Post Request Response:", memberTaskData)
                cachedMemberTasks[memberID] = memberTaskData // cache result

                // render task list
                renderTasks(memberID, memberTaskData)
            },
            "json"
        )
    })

    getDataAndRenderCharts()
})


function renderTasks(memberID, taskData) {
    console.log("Rendering Tasks for Member:", memberID, taskData)

    /* Task Data -> HTML */
    const todoListElem = $("#to-do-list")
    todoListElem.empty()

    // check if no tasks found
    if (taskData.length === 0) {
        todoListElem.append("<h3>No tasks found for this user!</h3>")
        return
    }

    for (const taskID in taskData) {
        const task = taskData[taskID]

        let complete = ""
        if (task["TaskStatus"] === "Complete") {
            complete = "complete"
        }

        let moveTaskButton = ""
        const html = $("html")
        if (html.attr("data-is-manager") === "1") {
            moveTaskButton = `
            <!-- Move Task Button -->
            <!-- JS Copies task id to move task form -->
            <button type="button" class="button task-option-button move-task open-modal"
            data-target-id="move-task-form"
            data-task-id="${task["TaskID"]}"
            data-member-id="${memberID}">
            <i class="bx bx-transfer"></i></button>`
        }


        todoListElem.append(`
        <li class="burger-layout task ${complete}" data-project-id="${task["ProjectID"]}">
            <div class="row-container" style="background-color: ${task["Colour"]}">
            <h3>${task["TaskName"]}</h3>
    
            <!-- Edit Button -->
            <!-- JS Copies data accross to edit form -->
            <button type="button" class="right button task-option-button edit-task open-modal"
                    data-target-id="edit-task-form"
                    data-task-id="${task["TaskID"]}"
                    data-task-colour="${task["Colour"]}"
                    data-task-name="${task["TaskName"]}"
                    data-task-description="${task["TaskDescription"]}"
                    data-task-deadline="${task["DueDate"]}"
                    data-task-duration="${task["TaskDuration"]}"
                    data-task-status="${task["TaskStatus"]}"
            ><i class="bx bx-edit"></i></button>
    
            ${moveTaskButton}
    
            <!-- X button -->
            <button type="button" class="red-circle-button open-modal"
                    data-target-id="confirm-delete-task"
                    data-task-id="${task["TaskID"]}"
            ><i class="bx bx-x"></i></button> <!-- 🞮 -->
        </div>
            <p class="description">${task["TaskDescription"]}</p>
    
            <div class="row">
                <div> <p>Deadline</p> <b>${task["DueDateFormatted"]}</b> </div>
                <div> <p>Duration</p> <b>${task["TaskDuration"]} hrs</b> </div>
                <div> <p>Status</p> <i class="bx ${task["TaskStatusClass"]}"></i> </div>
            </div>
        </li>
        `)
    }
    moveCompletedTasksToEnd()
}

/* Chart Functions */
let cachedMemberTaskCountData = null
function getDataAndRenderCharts() {
    const html = $("html")
    const projectID = html.attr("data-project-id")
    const userID = html.attr("data-user-id")

    if (cachedMemberTaskCountData === null) {
        console.log("Peforming ajax post request - getMemberTaskCountData")
        // get task data for charts
        $.post("../other/project-queries.php",
            {
                action: "getMemberTaskCountData",
                projectID: projectID,
                userID: userID
            },
            function (memberTaskCountData) {
                cachedMemberTaskCountData = memberTaskCountData

                console.log("Data:", memberTaskCountData)
                renderCharts(memberTaskCountData)
            },
            "json"
        )
    }
    else {
        renderCharts(cachedMemberTaskCountData)
    }
}

function renderCharts(memberTaskCountData) {
    console.log("Creating Chart", memberTaskCountData)
    const html = $("html")
    const currentUserID = html.attr("data-user-id")
    const theme = html.attr("data-theme")

    const barDataPointsComplete = []
    const barDataPointsIncomplete = []
    const pieDataPoints = []

    // set data points for charts
    for (const memberID in memberTaskCountData) {
        const countData = memberTaskCountData[memberID]
        console.log("MemberID:", memberID, "=>", countData)

        // set username to "You" if it is the current user
        if (memberID === currentUserID) {
            countData["Username"] = "You"
        }

        // convert to integers
        const totalComplete = parseInt(countData["TotalComplete"])
        const totalIncomplete = parseInt(countData["TotalIncomplete"])
        const totalDuration = parseInt(countData["TotalDuration"])

        // add data points
        barDataPointsComplete.push({label: countData["Username"], y: totalComplete})
        barDataPointsIncomplete.push({label: countData["Username"], y: totalIncomplete})
        pieDataPoints.push({x: countData["Username"], y: totalDuration})
    }

    console.log(pieDataPoints)

    // create and render charts
    const columnChart = getColumnChart("columnChartContainer", theme, barDataPointsComplete, barDataPointsIncomplete)
    columnChart.render()
    const pieChart = getPieChart("pieChartContainer", theme, pieDataPoints)
    pieChart.render()
}


function getColumnChart(chartContainer, theme, dataPointsCompleted, dataPointsIncomplete) {
    return new CanvasJS.Chart(chartContainer, {
        backgroundColor: (theme === "light") ? "#F2F2F2" : "#373E43",
        animationEnabled: true,
        animationDuration: 650,
        title: {
            text: "Tasks Overview",
            horizontalAlign: "center",
            fontSize: 24,
            fontFamily: "monospace",
            fontColor: (theme === "light") ? "#000000" : "#FFFFFF",
            padding: {top: 10, left: 0, right: 0, bottom: 20},
        },
        toolTip: {
            shared: true,
            content: "{name}: {y}",
            fontFamily: "monospace",
        },
        axisX: {
            labelFontFamily: "monospace",
            labelFontSize: 24,
            labelFontColor: (theme === "light") ? "#000000" : "#FFFFFF",
         },
        axisY: {
            title: "# of Tasks",
            titleFontFamily: "monospace",
            titleFontColor: (theme === "light") ? "#000000" : "#FFFFFF",
            interval: 1,
            labelFontFamily: "monospace",
            labelFontSize: 24,
            labelFontColor: (theme === "light") ? "#000000" : "#FFFFFF",
        },
        data: [{
            type: "stackedBar",
            name: "Complete Tasks",
            dataPoints: dataPointsCompleted,
        },
        {
            type: "stackedBar",
            name: "Incomplete Tasks",
            dataPoints: dataPointsIncomplete,
        }],
    })
}

function getPieChart(chartContainer, theme, dataPoints) {
    return new CanvasJS.Chart(chartContainer, {
        backgroundColor: (theme === "light") ? "#F2F2F2" : "#373E43",
        animationEnabled: true,
        animationDuration: 550,
        title: {
            text: "Task Allocation",
            horizontalAlign: "center",
            fontSize: 24,
            fontFamily: "monospace",
            fontColor: (theme === "light") ? "#000000" : "#FFFFFF",
            padding: {top: 10, left: 0, right: 0, bottom: 20},
        },
        toolTip: {
            shared: true,
            content: "{name}: {y} hrs",
            fontFamily: "monospace",
        },
        data: [{
            type: "pie",
            name: "Total Task Duration",
            dataPoints: dataPoints,
            indexLabel: "{x} - {y} hrs",
            indexLabelPlacement: "inside",
            indexLabelFontSize: 16,
            indexLabelFontFamily: "monospace",
            indexLabelFontColor: "#FFFFFF",
        }],
    })

}