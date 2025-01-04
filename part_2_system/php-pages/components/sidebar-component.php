<?php
// Sidebar Links can be updated for the respective php page


// Returns the sidebar html component -> Doesn't echo the html, just returns it
function getSidebarComponent($active_page_name, $username, $user_img)
{
    // Generate navigation links - UPDATE for the respective php page added, html files are temporary
    if ($_SESSION["user_type"] == "Manager") {
        $nav_links = [
            "overview" => ["link" => "../manager/project-overview.php", "icon" => "bx bx-grid-alt"],
            "employees" => ["link" => "../manager/employee-overview.php", "icon" => "bx bx-user"],
            "to-do-list" => ["link" => "../manager/to-do-list.php", "icon" => "bx bx-task"],
            "forum" => ["link" => "../manager/threads.php", "icon" => "bx bx-chat"],
        ];
    } else if( $_SESSION["user_type"] == "User") {
        $nav_links = [
            "overview" => ["link" => "../employee/project-overview.php", "icon" => "bx bx-grid-alt"],
            "to-do-list" => ["link" => "../employee/to-do-list.php", "icon" => "bx bx-task"],
            "forum" => ["link" => "../employee/threads.php", "icon" => "bx bx-chat"],
        ];
    }

    // Generate sidebar html
    $sidebar = '<div class="sidebar-container">
        <aside class="sidebar">
            <!-- top section -->
            <div class="top">
                <h3>Make-It-All</h3>
                <i class="bx bx-menu" id="menuBtn" tabindex="0"></i>
            </div>

            <!-- user info -->
            <div class="user">
                <img src="'. $user_img . '" alt="User Profile Image" class="user-img">
                <div>
                    <b>'. $username .'</b>
                    <p>'. $_SESSION["user_type"] .'</p>
                </div>
            </div>

            <!-- navigation links -->
            <nav>';

    // Add navigation links to sidebar
    foreach ($nav_links as $page => $data) {
        // unpack data into nav link & icon class
        $link = $data['link'];
        $icon_class = $data['icon'];

        // set current page class if active page matches
        $class = '';
        if ($active_page_name == $page) {
            $class = 'class="current-page"';
        }

        // add nav link to sidebar
        $sidebar .=
            '<a href="' . $link . '" ' . $class . '>
                <i class="'. $icon_class .'"></i>
                <span class="nav-item">' . ucfirst($page) . '</span>
            </a>';
    }

    // Add additional links / buttons to sidebar
    $sidebar .= '<a href="" id="invite">
                    <i class="bx bx-paper-plane"></i>
                    <span class="nav-item">Invite User</span>
                </a>

                <a href="../../index.php">
                    <i class="bx bx-exit" ></i>
                    <span class="nav-item">Logout</span>
                </a>
            </nav>

            <div class="switch-theme">
                <div class="switch-to-light">
                    <i class="bx bx-sun"></i>
                    <p>Light Theme</p>
                </div>

                <div class="switch-to-dark">
                    <i class="bx bx-moon"></i>
                    <p>Dark Theme</p>
                </div>
            </div>
        </aside>
    </div>';

    // return full sidebar html
    return $sidebar;
}
