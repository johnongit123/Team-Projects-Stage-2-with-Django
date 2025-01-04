window.addEventListener('load', function () {
    // hide all modals by default
    $(".modal").attr("data-hidden", "true")

    // close the closest modal
    $(document).on("click", ".close-modal", function () {
        $(this).closest(".modal").attr("data-hidden", "true");
        $("body").attr("data-modal-open", "false")
    })

    // modal to open is specified by data-target-id attribute
    $(document).on("click", ".open-modal", function () {
        // get target elem from data-target-id
        const targetID = $(this).attr("data-target-id")
        const targetElem = $("#"+targetID)

        // open the target modal
        targetElem.attr("data-hidden", "false")
        $("body").attr("data-modal-open", "true")
    })
})