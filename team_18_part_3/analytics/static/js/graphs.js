let graphData = null
let taskData = null

window.addEventListener("load", async () => {
    console.log("Initializing Page")
    graphData = await getProjectGraphs()
    taskData = await getTaskData()

    const graphListElem = $("#graph-list")
    renderGraphs()

    // re-render graphs when page theme is switched
    switchThemeListeners.push(() => { renderGraphs() })

    /* Open Edit Modal */
    graphListElem.on("click", ".open-modal[data-target-id='edit-graph-modal']", function () {
        const graphContainer = $(this).closest('.graph').find('.graph-container')
        const graphId = graphContainer.attr("id")
        const graph = graphData[graphId]

        /* copy data from graph into modal */
        $("#edit-graph-modal").attr("graph-id", graphId)

        // set title
        $("#input-title").val(graph.title)

        // set type dropdown
        switch (graph.type) {
            case "pie": {$("#dropdown-type").val("pie"); break}
            case "bar": {$("#dropdown-type").val("bar"); break}
            case "column": {$("#dropdown-type").val("column"); break}
        }

        // set content dropdown
        switch (graph.content) {
            case "count": {$("#dropdown-content").val("count"); break}
            case "duration": {$("#dropdown-content").val("duration"); break}
        }

        // check correct filters
        $(".filters > input[type='checkbox']").attr("checked", false) // uncheck all filters
        $("#filter-completed").attr("checked", graph.filters.complete)
        $("#filter-not-started").attr("checked", graph.filters.not_started)
        $("#filter-ongoing").attr("checked", graph.filters.ongoing)
        $("#filter-overdue").attr("checked", graph.filters.overdue)
        $("#filter-paused").attr("checked", graph.filters.paused)
    })


    /* Open Remove Modal */
    graphListElem.on("click", ".open-modal[data-target-id='remove-graph-modal']", function () {
        console.log("Opening Remove Graph Modal")
        const graphContainer = $(this).closest('.graph').find('.graph-container')
        const graphId = graphContainer.attr("id")
        const removeGraphModal = $("#remove-graph-modal")

        removeGraphModal.attr("data-graph-id", graphId)

        console.log(graphId, removeGraphModal, graphContainer)
    })


    /* Edit Modal On Submit */
    $("#edit-graph-modal").on("submit", async function (event) {
        event.preventDefault()
        await updateGraph(
            $("#edit-graph-modal").attr("graph-id"),
            $("#input-title").val(),
            $("#dropdown-type").val(),
            $("#dropdown-content").val(),
            {
                'complete': $("#filter-completed:checked").length > 0,
                'not_started': $("#filter-not-started:checked").length > 0,
                'ongoing': $("#filter-ongoing:checked").length > 0,
                'overdue': $("#filter-overdue:checked").length > 0,
                'paused': $("#filter-paused:checked").length > 0,
            }
        )
    })

    /* New Graph Modal On Submit */
    $("#new-graph-modal").on("submit", async function (event) {
        event.preventDefault()
        await addNewGraph(
            $("#new-input-title").val(),
            $("#new-dropdown-type").val(),
            $("#new-dropdown-content").val(),
            {
                'complete': $("#new-filter-completed:checked").length > 0,
                'not_started': $("#new-filter-not-started:checked").length > 0,
                'ongoing': $("#new-filter-ongoing:checked").length > 0,
                'overdue': $("#new-filter-overdue:checked").length > 0,
                'paused': $("#new-filter-paused:checked").length > 0,
            }
        )
    })

    /* Remove Graph Modal On Submit */
    $("#remove-graph-modal").on("submit", async function (event) {
        event.preventDefault()
        await removeGraph($(this).attr("data-graph-id"))
    })
})


async function getProjectGraphs() {
    return new Promise((resolve, reject) => {
        // send GET request to endpoint to get graph data
        console.log("Sending GET request to '/api/get-project-graphs'")
        $.ajax({
            url: '../api/get-project-graphs',
            type: 'get',
            dataType: 'json',
            data: {'project_id': $("#graph-list").attr("data-project-id")},
            success: function (data, status, xhr) {
                console.log("SUCCESS | Graph Data", data)
                // console.log(data, status, xhr)
                resolve(data); // Resolve the promise with the data
            },
            error: function (jqXhr, textStatus, errorMessage) {
                console.log('Error: ' + errorMessage)
                // console.log(jqXhr, textStatus, errorMessage)
                reject(new Error(errorMessage)) // Reject the promise with an error
            },
        })
    })
}

async function getTaskData() {
    return new Promise((resolve, reject) => {
        // send GET request to endpoint to get task data
        console.log("Sending GET request to '/api/get-project-task-counts'")
        $.ajax({
            url: '../api/get-project-task-counts',
            type: 'get',
            dataType: 'json',
            data: {'project_id': $("#graph-list").attr("data-project-id")},
            success: function (data, status, xhr) {
                console.log("SUCCESS | Task Data")
                // console.log(data, status, xhr)
                resolve(data)
            },
            error: function (jqXhr, textStatus, errorMessage) {
                console.log('Error: ' + errorMessage)
                // console.log(jqXhr, textStatus, errorMessage)
                reject(new Error(errorMessage)) // Reject the promise with an error
            },
        })
    })
}

async function addNewGraph(title, type, content, filters) {
    console.log("ADDING NEW GRAPH |", title, type, content, filters)
    const returnData = await new Promise((resolve, reject) => {
        // send GET request to endpoint to get task data
        console.log("Sending POST request to '/api/create-graph'")
        $.ajax({
            url: "../api/create-graph",
            type: "post",
            data: {
                "project_id": $("#graph-list").attr("data-project-id"),
                "graph_title": title,
                "graph_type": type,
                "graph_content": content,
                "graph_filter_complete": filters.complete,
                "graph_filter_not_started": filters.not_started,
                "graph_filter_ongoing": filters.ongoing,
                "graph_filter_overdue": filters.overdue,
                "graph_filter_paused": filters.paused,
            },
            success: function (data, status, xhr) {
                console.log("SUCCESS | CREATED GRAPH")
                // console.log(data, status, xhr)
                resolve(data)
            },
            error: function (jqXhr, textStatus, errorMessage) {
                console.log("Error: " + errorMessage)
                // console.log(jqXhr, textStatus, errorMessage)
                reject(new Error(errorMessage)) // Reject the promise with an error
            },
        })
    })

    // update graph data (instead of re-fetching data by refreshing page)
    const graphId = returnData.graph_id
    graphData[graphId] = {
        title: title,
        type: type,
        content: content,
        filters: filters,
    }
    renderGraphs()
}

async function updateGraph(id, title, type, content, filters) {
    graphData[id].title = title
    graphData[id].type = type
    graphData[id].content = content
    graphData[id].filters = filters

    // update database
    await new Promise((resolve, reject) => {
        // send GET request to endpoint to get task data
        console.log("Sending POST request to '/api/update-graph'")
        $.ajax({
            url: "../api/update-graph",
            type: "post",
            data: {
                // "project_id": $("#graph-list").attr("data-project-id"),
                "graph_id": id,
                "graph_title": title,
                "graph_type": type,
                "graph_content": content,
                "graph_filter_complete": filters.complete,
                "graph_filter_not_started": filters.not_started,
                "graph_filter_ongoing": filters.ongoing,
                "graph_filter_overdue": filters.overdue,
                "graph_filter_paused": filters.paused,
            },
            success: function (data, status, xhr) {
                console.log("SUCCESS | UPDATED GRAPH")
                // console.log(data, status, xhr)
                resolve(data)
            },
            error: function (jqXhr, textStatus, errorMessage) {
                console.log("Error: " + errorMessage)
                // console.log(jqXhr, textStatus, errorMessage)
                reject(new Error(errorMessage)) // Reject the promise with an error
            },
        })
    })

    // re-render graphs
    renderGraphs()
}


async function removeGraph(graphId) {
    delete graphData[graphId]
    renderGraphs()
    return new Promise((resolve, reject) => {
        // send GET request to endpoint to get task data
        console.log("Sending POST request to '/api/remove-graph'")
        $.ajax({
            url: "../api/update-graph",
            type: "post",
            data: {
                // "project_id": $("#graph-list").attr("data-project-id"),
                "graph_id": graphId,
            },
            success: function (data, status, xhr) {
                console.log("SUCCESS | REMOVED GRAPH")
                // console.log(data, status, xhr)
                resolve(data)
            },
            error: function (jqXhr, textStatus, errorMessage) {
                console.log("Error: " + errorMessage)
                // console.log(jqXhr, textStatus, errorMessage)
                reject(new Error(errorMessage)) // Reject the promise with an error
            },
        })
    })
}

function renderGraphs() {
    console.log("RENDERING GRAPHS")

    const graphListElem = $("#graph-list")
    graphListElem.empty()

    // render each graph into its own list item
    for (const graphId in graphData) {
        const graphHtml = `
        <li class="graph">
            <div class="options">
                <button type="button" class="button open-modal" style="--_order: 1" data-target-id="edit-graph-modal" title="Edit Graph"><i class="bx bx-edit"></i></button>
                <button type="button" class="button open-modal" style="--_order: 2" data-target-id="remove-graph-modal" title="Remove Graph"><i class="bx bx-trash"></i></button>
            </div>
            <div id="${graphId}" class="graph-container"></div>
        </li>`
        graphListElem.append(graphHtml)

        // render Canvas JS Graph into given container
        renderGraph(graphId, graphData[graphId], taskData)
    }

    graphListElem.append(`
        <li class="new-graph-button open-modal" data-target-id="new-graph-modal" title="Add New Graph">
            <i class="bx bx-plus"></i>
        </li>
    `)
}


/* --- Customizable Options --- */
// const GraphTypes = {
//     Pie: Symbol("pie"),
//     Bar: Symbol("bar"),
//     StackedBar: Symbol("stackedBar"),
//     Column: Symbol("column"),
// }
// const GraphContents = {
//     TaskCount: Symbol("# of Tasks"),
//     TaskDuration: Symbol("Total Task Duration (hrs)"),
// }
// const TaskFilters = {
//     Ongoing: Symbol("Ongoing"),
//     NotStarted: Symbol("Not Started"),
//     Completed: Symbol("Completed"),
//     Paused: Symbol("Paused"),
//     Overdue: Symbol("Overdue"),
// }
// class Graph {
//     constructor(title, graphType, graphContentType, taskFilters = {'complete': false, 'not_started': false, 'ongoing': false, 'paused': false, 'overdue': false}) {
//         this.canvasJsChart = null
//         this.title = title
//         this.graphType = graphType
//         this.setGraphContentType(graphContentType)
//         this.taskFilters = taskFilters
//     }
//
//     setGraphContentType(graphContentType)
//     {
//         this.graphContentType = graphContentType
//         switch (this.graphContentType) {
//             case "count": {
//                 this.axisLabel = "# of Tasks"
//                 this.units = "Tasks"
//                 break
//             }
//             case "duration": {
//                 this.axisLabel = "Total Task Duration (hrs)"
//                 this.units = "hrs"
//                 break
//             }
//             default: {
//                 throw new TypeError(`Invalid Graph Content! (${this.graphContentType})`)
//             }
//         }
//     }
//
//     render(containerId, taskData) {
//         // make a copy to avoid editing original data
//         taskData = JSON.parse(JSON.stringify(taskData))
//
//         // get current bg & text color
//         const documentElementStyle = getComputedStyle(document.documentElement)
//         const bgColor = documentElementStyle.getPropertyValue("--bg-1")
//         const bgColor2 = documentElementStyle.getPropertyValue("--bg-3")
//         const textColor = documentElementStyle.getPropertyValue("--text-color")
//         const font = "monospace"
//
//         // set baseline Canvas JS options
//         const options = {
//             backgroundColor: bgColor,
//             animationEnabled: true,
//             animationDuration: 550,
//             title: {
//                 text: this.title,
//                 horizontalAlign: "center",
//                 fontSize: 28,
//                 fontFamily: font,
//                 fontColor: textColor,
//                 padding: {top: 10, left: 0, right: 0, bottom: 20},
//             },
//             toolTip: {
//                 shared: false,
//                 content: "{label}: {y} " + this.units + " {name}",
//                 fontFamily: "monospace",
//             },
//             axisX: {
//                 labelFontFamily: font,
//                 labelFontSize: 20,
//                 labelFontColor: textColor,
//
//             },
//             axisY: {
//                 title: this.axisLabel,
//                 titleFontFamily: font,
//                 titleFontSize: 24,
//                 titleFontColor: textColor,
//                 labelFontFamily: font,
//                 labelFontSize: 20,
//                 labelFontColor: textColor,
//             },
//             data: []
//         }
//
//
//         // define dataset being used
//         let dataset;
//         if (this.graphContentType === "duration") {
//             dataset = taskData["task_durations"]
//         } else if (this.graphContentType === "count") {
//             dataset = taskData["task_counts"]
//         }
//
//         // change options based on graph type
//         if (this.graphType === "pie") {
//             options.toolTip.content = "{label}: {y} " + this.units
//             options.data.push({
//                 type: this.graphType,
//                 name: this.axisLabel,
//                 color: bgColor2,
//                 dataPoints: dataset["all"].map(value => {
//                     value.exploded = true;
//                     return value
//                 }),
//                 indexLabel: "{label} - {y} " + this.units,
//                 indexLabelPlacement: "inside",
//                 indexLabelFontSize: 24,
//                 indexLabelFontFamily: font,
//                 indexLabelFontColor: "white",
//             })
//             console.log("PIE CHART DATA POINTS", options.data[0].dataPoints)
//         } else {
//             const addData = (name, dataPoints, color) => {
//                 options.data.push({
//                     type: this.graphType,
//                     name: name,
//                     color: color,
//                     dataPoints: dataPoints,
//                     indexLabel: "{y}",
//                     indexLabelPlacement: "outside",
//                     indexLabelFontSize: 18,
//                     indexLabelFontFamily: font,
//                     indexLabelFontColor: textColor,
//                 })
//             }
//
//             // add data of respective filters
//             if (this.taskFilters.complete) {
//                 addData("Complete", dataset["complete"], "#5f9732")
//             }
//             if (this.taskFilters.not_started) {
//                 addData("Not Started", dataset["not-started"], "#b7b7b7")
//             }
//             if (this.taskFilters.ongoing) {
//                 addData("Ongoing", dataset["ongoing"], "#4d8bf7")
//             }
//             if (this.taskFilters.overdue) {
//                 addData("Overdue", dataset["overdue"], "#bb4d2d")
//             }
//             if (this.taskFilters.paused) {
//                 addData("Paused", dataset["paused"], "#ecb645")
//             }
//         }
//
//         // ensure container is clear
//         $(`#${containerId}`).empty()
//
//         // define the canvas js chart
//         this.canvasJsChart = new CanvasJS.Chart(containerId, options)
//
//         console.log("RENDERING CHART | OPTIONS:", options)
//
//         // render chart
//         this.canvasJsChart.render()
//     }
// }

function renderGraph(containerId, graph, taskData) {
    let axisLabel = ""
    let units = ""
    if (graph.content === "count") {
        axisLabel = "# of Tasks"
        units = "Tasks"
    } else if (graph.content === "duration") {
        axisLabel = "Total Task Duration (hrs)"
        units = "hrs"
    }

    // make a copy to avoid editing original data
    taskData = JSON.parse(JSON.stringify(taskData))

    // get current bg & text color
    const documentElementStyle = getComputedStyle(document.documentElement)
    const bgColor = documentElementStyle.getPropertyValue("--bg-1")
    const bgColor2 = documentElementStyle.getPropertyValue("--bg-3")
    const textColor = documentElementStyle.getPropertyValue("--text-color")
    const font = "monospace"

    // set baseline Canvas JS options
    const options = {
        backgroundColor: bgColor,
        animationEnabled: true,
        animationDuration: 550,
        title: {
            text: graph.title,
            horizontalAlign: "center",
            fontSize: 28,
            fontFamily: font,
            fontColor: textColor,
            padding: {top: 10, left: 0, right: 0, bottom: 20},
        },
        toolTip: {
            shared: false,
            content: "{label}: {y} " + units + " {name}",
            fontFamily: "monospace",
        },
        axisX: {
            labelFontFamily: font,
            labelFontSize: 20,
            labelFontColor: textColor,

        },
        axisY: {
            title: axisLabel,
            titleFontFamily: font,
            titleFontSize: 24,
            titleFontColor: textColor,
            labelFontFamily: font,
            labelFontSize: 20,
            labelFontColor: textColor,
        },
        data: []
    }


    // define dataset being used
    let dataset;
    if (graph.content === "duration") {
        dataset = taskData["task_durations"]
    } else if (graph.content === "count") {
        dataset = taskData["task_counts"]
    }

    // change options based on graph type
    if (graph.type === "pie") {
        options.toolTip.content = "{label}: {y} " + units
        options.data.push({
            type: graph.type,
            name: axisLabel,
            color: bgColor2,
            dataPoints: dataset["all"].map(value => {
                value.exploded = true;
                return value
            }),
            indexLabel: "{label} - {y} " + units,
            indexLabelPlacement: "inside",
            indexLabelFontSize: 24,
            indexLabelFontFamily: font,
            indexLabelFontColor: "white",
        })
    } else {
        const addData = (name, dataPoints, color) => {
            options.data.push({
                type: graph.type,
                name: name,
                color: color,
                dataPoints: dataPoints,
                indexLabel: "{y}",
                indexLabelPlacement: "outside",
                indexLabelFontSize: 18,
                indexLabelFontFamily: font,
                indexLabelFontColor: textColor,
            })
        }

        // add data of respective filters
        if (graph.filters.complete) {
            addData("Complete", dataset["complete"], "#5f9732")
        }
        if (graph.filters.not_started) {
            addData("Not Started", dataset["not-started"], "#b7b7b7")
        }
        if (graph.filters.ongoing) {
            addData("Ongoing", dataset["ongoing"], "#4d8bf7")
        }
        if (graph.filters.overdue) {
            addData("Overdue", dataset["overdue"], "#bb4d2d")
        }
        if (graph.filters.paused) {
            addData("Paused", dataset["paused"], "#ecb645")
        }
    }

    // ensure container is clear
    $(`#${containerId}`).empty()

    // render chart
    // console.log("RENDERING CHART | OPTIONS:", options)
    const canvasJsChart = new CanvasJS.Chart(containerId, options)
    canvasJsChart.render()
}
