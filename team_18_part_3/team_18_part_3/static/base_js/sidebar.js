switchThemeListeners = []

window.addEventListener("load", function () {
    const sidebar = $(".sidebar")

    // set sidebar to open if set to open in local storage
    if (localStorage.getItem("sidebar") === "open") {
        sidebar.addClass("active")
    }

    // add the animations after setting it to open/closed (so sidebar appears to stay the same between pages)
    setTimeout(() => {sidebar.addClass("animations")}, 50)

    // opening/closing sidebar
    $("#menuBtn").on("click", () => {
        sidebar.toggleClass("active")

        // set if sidebar open or not in local storage
        if (sidebar.hasClass("active")) {
            localStorage.setItem("sidebar", "open")
        } else {
            localStorage.setItem("sidebar", "closed")
        }
    })

    // check if not in local storage
    const localStorageTheme = localStorage.getItem("theme")
    if (localStorageTheme === null) {
        // set theme to system preference
        if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
            changeTheme("dark")
        }
        else {
            changeTheme("light")
        }
    }
    else {
        changeTheme(localStorageTheme)
    }
})


// check if system preference changes
window.matchMedia("(prefers-color-scheme: dark)").addEventListener('change', event => {
    const theme = event.matches ? "dark" : "light";
    changeTheme(theme)
});


// switch color scheme on respective button press
$(".switch-to-dark").on("click", ()=>{changeTheme("dark")})
$(".switch-to-light").on("click", ()=>{changeTheme("light")})


// changes theme and sets it in local storage
function changeTheme(theme) {
    $("html").attr("data-theme", theme)
    localStorage.setItem("theme", theme) // sets the theme in local storage, so it remembers between page visits

    // run each listener
    switchThemeListeners.forEach(listener => { listener() })
}
