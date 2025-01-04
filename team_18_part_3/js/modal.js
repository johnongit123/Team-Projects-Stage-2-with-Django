window.addEventListener('load', function () {
    $(document).on("click", ".close-modal", function () {
        $(this).closest(".modal-background").attr("data-hidden", "true");
    })

    // modal to open is specified by data-target-id attribute
    $(document).on("click", ".open-modal", function () {
        // get target elem from data-target-id
        const targetID = $(this).attr("data-target-id")
        const targetElem = $("#"+targetID)

        // open the target modal
        targetElem.closest(".modal-background").attr("data-hidden", "false");
    })
})