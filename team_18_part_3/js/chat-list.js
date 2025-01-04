//MODALS

//show specific modal
function showModal(modalId) {
    document.querySelector('.modal-overlay').style.display = 'block';
    document.getElementById(modalId + '-modal').style.display = 'block'; 
}


//close currently opened modal
function closeModal() {
    document.querySelector('.modal-overlay').style.display = 'none'; 
    //hide all modals
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.style.display = 'none';
    });
}

//show modal when creating new chat
function createNewChat() {
    showModal('new-chat');
}


//show modal when viewing invitations
function viewInvitations(){
    showModal('invitation-list')


}


//CHAT INTERACTIONS

//open specific chat
function openChat(chatName) {
    console.log("Opening chat:", chatName);
}


function startNewChat() {
    //get the email from the input field
    var email = document.getElementById('new-chat-email').value;
    console.log("Starting new chat with:", email);
    closeModal();
}

function startGroupChat() {
    //logic to handle group chat creation
    console.log('Creating group chat...');
    closeModal();
}

function startIndependentChat() {
    //logic to handle independent chat creation
    console.log('Creating independent chat...');
    closeModal();
}



//chat listing and filtering functions

function filterChats(filter) {
    
    const chats = document.querySelectorAll('.chat-item');
    chats.forEach(chat => {
        switch(filter) {
            case 'all':
                chat.style.display = '';
                break;
            case 'individual':
                if(chat.classList.contains('one-on-one-chat')) chat.style.display = '';
                else chat.style.display = 'none';
                break;
            case 'group':
                if(chat.classList.contains('group-chat')) chat.style.display = '';
                else chat.style.display = 'none';
                break;
            case 'unread':
                if(chat.classList.contains('unread')) chat.style.display = '';
                else chat.style.display = 'none';
                break;
            case 'favourites':
                // Check if the star icon exists before trying to access its classList
                const starIcon = chat.querySelector('.star-icon');
                if(starIcon) {
                    chat.style.display = starIcon.classList.contains('favourited') ? '' : 'none';
                } else {
                    // If there's no star icon, we might want to hide the chat or handle this scenario differently
                    console.error('Star icon not found in chat item:', chat);
                    chat.style.display = 'none'; // This is one way to handle it, depending on your desired behavior
                }
                break;
        }
    });
    updateNoChatsMessage();
}


//search chat based on name of chat
function searchChats() {
    var input = document.getElementById('search-input');
    var filter = input.value.toUpperCase();
    var chatList = document.getElementById('chat-list');
    var chats = chatList.getElementsByClassName('chat-item');
    for (var i = 0; i < chats.length; i++) {
        var chatName = chats[i].getElementsByClassName('chat-name')[0].innerText;
        if (chatName.toUpperCase().indexOf(filter) > -1) {
            chats[i].style.display = "";
        } else {
            chats[i].style.display = "none";
        }
    }
    updateNoChatsMessage();
}


function updateUnreadCount() {
    const unreadChats = document.querySelectorAll('.chat-item.unread').length; //count unread chats
    document.getElementById('unread-count').textContent = unreadChats;
}


function updateNoChatsMessage() {
    const chatList = document.getElementById('chat-list');
    const noChatsMessage = document.getElementById('no-chats-message');
    //check if there are visible chats
    const visibleChats = chatList.querySelectorAll('.chat-item:not([style*="display: none"])');
    if(visibleChats.length === 0) {
        //if no chats are visible, show the no chats message
        noChatsMessage.style.display = 'block';
    } else {
        //if there are visible chats, hide the no chats message
        noChatsMessage.style.display = 'none';
    }
}

//INVITATION FUNCTIONALITY
let invitations = [
    { name: 'Backend Team', id: 'backend-team' },
    { name: 'UX/UI Designers', id: 'ux-ui-designers' }
];

function acceptInvitation(chatName) {
    console.log("Accepting invitation to:", chatName);
    //remove the invitation from the array
    invitations = invitations.filter(invitation => invitation.name !== chatName);
    //update the display
    loadInvitations();
}

function denyInvitation(chatName) {
    console.log("Denying invitation to:", chatName);
    //remove the invitation from the array
    invitations = invitations.filter(invitation => invitation.name !== chatName);
    //update the display
    loadInvitations();
}

function loadInvitations() {
    const invitationList = document.getElementById('invitation-list');
    //clear existing invitations
    invitationList.innerHTML = '';
    
    //check if there are no invitations left
    if (invitations.length === 0) {
        invitationList.innerHTML = '<p class="no-invitations">No chat invitations available.</p>';
    } else {
        //add invitations to the list
        invitations.forEach(invitation => {
            const div = document.createElement('div');
            div.className = 'invitation';
            div.innerHTML = `
                <p>Invitation to join the "${invitation.name}" chat.</p>
                <button class="accept-invitation" onclick="acceptInvitation('${invitation.name}')">Accept</button>
                <button class="deny-invitation" onclick="denyInvitation('${invitation.name}')">Deny</button>
            `;
            invitationList.appendChild(div);
        });
    }
}


//toggle mute when icon is clicked on specific chat
function toggleMute(event, chatName) {
    event.stopPropagation(); 
    console.log('Toggling mute for:', chatName);

    const muteIcon = event.currentTarget.querySelector('.mute-icon');

    const muteTextElement = muteIcon.nextSibling.nodeType === 3 ? muteIcon.nextSibling : muteIcon.nextElementSibling;

    if (muteIcon.classList.contains('muted')) {
        muteIcon.classList.remove('muted');
        muteIcon.textContent = '🔈'; 
        //change the text content to "Mute"
        if (muteTextElement.nodeType === 3) { 
            muteTextElement.nodeValue = ' Mute';
        } else if (muteTextElement) { 
            muteTextElement.textContent = 'Mute';
        }
    } else {
        muteIcon.classList.add('muted');
        muteIcon.textContent = '🔇'; 
        //change the text content to "Unmute"
        if (muteTextElement.nodeType === 3) {
            muteTextElement.nodeValue = ' Unmute';
        } else if (muteTextElement) { 
            muteTextElement.textContent = 'Unmute';
        }
    }
}


//utility function to check if an element is near the bottom of its parent 
function isNearBottom(element) {
    const rect = element.getBoundingClientRect();
    const parentRect = element.offsetParent.getBoundingClientRect();
    return (rect.bottom + rect.height) >= parentRect.bottom;
}


//function to close any other open menus when one is opened currently
function showMenu(event) {
    event.stopPropagation();
    const chatItem = event.currentTarget.closest('.chat-item');
    const menu = chatItem.querySelector('.dropdown-menu');
    
    //close any other open menus
    document.querySelectorAll('.chat-item .dropdown-menu').forEach(function(otherMenu) {
        if (otherMenu !== menu) {
            otherMenu.style.display = 'none';
        }
    });

    //toggle the clicked menu
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}


function closeAllDropdowns() {
    document.querySelectorAll('.chat-item .dropdown-menu').forEach(function(menu) {
        menu.style.display = 'none';
    });
}


function leaveGroup(event, chatName) {
    event.stopPropagation();
    console.log('Leave group:', chatName);
    //leave group logic here when implementing dynamic implementation
}

function deleteChat(event, chatName) {
    event.stopPropagation(); 
    console.log('Delete chat:', chatName);
    //delete chat logic here when adding dynamic implementation
}

function toggleSortOptions() {
    var sortOptions = document.getElementById("sort-options");
    var isDisplayed = sortOptions.style.display === "block";
  
    sortOptions.style.display = isDisplayed ? "none" : "block";

   
    if (!isDisplayed) {
        setTimeout(() => { 
            document.addEventListener('click', closeSortOptions, { once: true });
        }, 0);
    }
}


function toggleFavourite(event, chatName) {
    event.stopPropagation();
    var starIcon = event.currentTarget;
    var isFavourited = starIcon.classList.toggle('favourited');
    starIcon.innerHTML = isFavourited ? '&#9733;' : '&#9734;'; //filled or outlined star

    //save or remove the chat from local storage based on its favorited state
    if (isFavourited) {
        localStorage.setItem(chatName, 'favourited');
    } else {
        localStorage.removeItem(chatName);
    }
}
//function to close the sort options
function closeSortOptions(event) {
    var sortOptions = document.getElementById("sort-options");
    var sortButton = document.getElementById("sort-button");
    if (!sortButton.contains(event.target) && !sortOptions.contains(event.target)) {
        sortOptions.style.display = "none";
    }
}


function removeDocumentClickListener() {
    document.removeEventListener('click', closeSortOptions);
}
//close old dropdown when new one is clicked on chat
document.addEventListener('click', closeAllDropdowns);

//close the dropdown if clicked outside
document.addEventListener('click', function(event) {
    if (!event.target.matches('.menu-dots')) {
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(function(menu) {
            menu.style.display = 'none';
        });
    }
});

//update event listeners
document.querySelectorAll('.chat-item .menu-dots').forEach(dot => {
    dot.removeEventListener('click', showMenu); // Remove existing click listener if any
    dot.addEventListener('click', showMenu); // Add new click listener
});


document.addEventListener('click', function(event) {
    if (event.target.matches('.star-icon')) {
        toggleFavourite(event, event.target.dataset.chatName);
    }
    // Other click events for closing dropdowns, etc.
});

//Call updateUnreadCount to initialize the count
updateUnreadCount();
updateNoChatsMessage();

document.addEventListener('DOMContentLoaded', function() {
    //get all filter buttons
    
    const filterButtons = document.querySelectorAll('.filter-button');

 
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
      
            filterButtons.forEach(btn => btn.classList.remove('active'));         
            this.classList.add('active');
        });
    });

    filterChats('all');

    //load and apply the favorited states to the chats
    const chatItems = document.querySelectorAll('.chat-item');
    chatItems.forEach(chatItem => {
        const chatName = chatItem.dataset.chatName; 
        const starIcon = chatItem.querySelector('.star-icon');
        if (localStorage.getItem(chatName) === 'favorited') {
            starIcon.classList.add('favorited');
            starIcon.textContent = '★'; 
        } else {
            starIcon.textContent = '☆'; 
        }
        
       
        starIcon.addEventListener('click', function(event) {
            event.stopPropagation();
            toggleFavourite(event, chatName);
        });
    });
});
