// editing the topic
/*$("#edit-topic").on("click", function () {
    if ($(this).is(":checked")) {
        $(".topic-details").find("h2, .description").attr("contenteditable", true)
    }
    else {
        $(".topic-details").find("h2, .description").attr("contenteditable", false)
    }
})

// adding a comment
$("#add-comment").on("click", function () {
    // get comment text
    const text = document.getElementById("comment-text").innerText
    if (text.length === 0) return

    // get current date
    const time = new Date().toLocaleDateString();

    // add comment to comments list
    $("#comments").append(`
                        <li class="comment">
                            <div class="comment-info">
                                <h3>User 1</h3>
                                <p class="date">${time}</p>
                            </div>
                            <p>${text}</p>
                        </li>
    `)
    // update number of comments
    $(".comment-container .comment-count").text($(".comments .comment").length + " Comments")

})*/


