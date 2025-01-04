
function search(){
    const text = document.getElementById("searchText").value.toLowerCase()
    const titles = document.querySelectorAll(".title")
    for (let i = 0; i < titles.length; i++) {
        if(!(titles[i].innerText.toLowerCase().includes(text))){
            titles[i].parentElement.parentElement.style.display = "none"
            //case dependant
        }else{
            titles[i].parentElement.parentElement.style.display = "list-item"
        }
    }
}


// edit comment button
$(".edit-button").on("click", function () {
    // find li.comment
    const commentElem = $(this).closest(".comment")

    // if not already editing, make it editable
    if (!commentElem.hasClass("editing")) {
        commentElem.addClass("editing")
        commentElem.find(".comment-content").attr("contenteditable", "true")
    }
    // hide edit button, show form (to submit changes)
    $(this).hide()
    commentElem.find("form").show()
})
$(".save-changes").on("click", function () {
    const commentElem = $(this).closest(".comment")
    const commentContentElem = commentElem.find(".comment-content")

    // if editing, make it not editable, submit changes in form
    if (commentElem.hasClass("editing")) {
        commentElem.removeClass("editing")
        commentContentElem.attr("contenteditable", "false")
        commentContentElem.focus()

        const newContentText = commentContentElem.text()
        commentElem.find("input.comment-content").val(newContentText)

        const form = commentElem.find("form")
        form.submit()
    }
    // hide form, show edit button
    form.hide()
    commentElem.find(".edit-button").show()
})