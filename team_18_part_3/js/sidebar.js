window.addEventListener("load", function () {
    const sidebar = document.querySelector(".sidebar")

    $("#menuBtn").on("click", () => {
        sidebar.classList.toggle('active')
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
$(".switch-to-dark").on("click", ()=>{changeTheme("dark"); reRenderCharts()})
$(".switch-to-light").on("click", ()=>{changeTheme("light"); reRenderCharts()})

// re-render charts if on page
function reRenderCharts() {
    // no charts on pages other than project details
    if ($(".project-details").length === 0) return

    // re-render the chart if on the page
    $("#columnChartContainer").empty()
    $("#pieChartContainer").empty()
    getDataAndRenderCharts(); // defined in project-details.js
}

// changes theme and sets it in local storage
function changeTheme(theme) {
    $("html").attr("data-theme", theme)
    localStorage.setItem("theme", theme) // sets the theme in local storage, so it remembers between page visits
}



document.getElementById('invite').onclick = function () {
    navigator.clipboard.writeText("http://team014.sci-project.lboro.ac.uk/pages/signup.html");
    alert("Invite Link Copied");

};