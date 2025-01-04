//MODALS

//show specific modal
function showModal(modalId) {
    document.querySelector('.modal-overlay').style.display = 'block';
    document.getElementById(modalId + '-modal').style.display = 'block'; 
}


// Function to show modal when creating new chat
function createNewChat() {
    resetSelectedUsersForGroupChat(); 
    showModal('new-chat');
}


// Show modal when viewing invitations
function viewInvitations(){
    showModal('invitation-list')


}

// Function to reset selected users for the Group Chat modal
function resetSelectedUsersForGroupChat() {
    selectedUsersForGroupChat = [];
    localStorage.removeItem('selectedUsersForGroupChat');
    resetUIForSelectedUsersForGroupChat();
    updateInviteUsersCount();  // Update count on invite user

    // Clear the group chat name input field
    document.getElementById('group-chat-name').value = '';
}

// Function to update the count of invited users displayed in the UI
function updateInviteUsersCount() {
    const numSelectedUsers = selectedUsersForGroupChat.length;
    const inviteUsersButton = document.getElementById('invite-users-button');
    inviteUsersButton.querySelector('.invite-users-count').textContent = numSelectedUsers;
}
// Function to reset UI for selected users for the Group Chat modal
function resetUIForSelectedUsersForGroupChat() {
    const userElements = document.querySelectorAll('.invite-circle.selected');
    userElements.forEach(circle => {
        circle.classList.remove('selected');
        circle.textContent = '◯'; 
    });
}


// Array to store selected users for the Group Chat modal
let selectedUsersForGroupChat = JSON.parse(localStorage.getItem('selectedUsersForGroupChat')) || [];

// Function to toggle user selection for Group Chat modal
function toggleUserSelectionForGroupChat(userEmail) {
    const index = selectedUsersForGroupChat.indexOf(userEmail);
    if (index === -1) {
        selectedUsersForGroupChat.push(userEmail);
    } else {
        selectedUsersForGroupChat.splice(index, 1);
    }
    // Save selected users for Group Chat modal to local storage
    localStorage.setItem('selectedUsersForGroupChat', JSON.stringify(selectedUsersForGroupChat));
}

// Function to filter users based on the search input
function filterUsers() {
    const searchInput = document.getElementById('user-search').value.toLowerCase();
    const userListDiv = document.getElementById('user-list');
    const users = userListDiv.getElementsByClassName('invite-user');

    for (let user of users) {
        const userInfo = user.getElementsByClassName('invite-user-info')[0];
        const userEmail = userInfo.textContent.toLowerCase();
        if (userEmail.includes(searchInput)) {
            user.style.display = 'block';
        } else {
            user.style.display = 'none';
        }
    }
}


// Function to open the Invite Users modal
function openInviteModal() {
    closeModal();
    showModal('invite-users');

    fetch('/chat/api/get_employee_list/')
        .then(response => response.json())
        .then(data => {
            const userListDiv = document.getElementById('user-list');
            userListDiv.innerHTML = '';

            if (data.employee_list && Array.isArray(data.employee_list)) {
                data.employee_list.forEach(employee => {
                    const employeeDiv = document.createElement('div');
                    employeeDiv.classList.add('invite-user');

                    const userInfoSpan = document.createElement('span');
                    userInfoSpan.textContent = `${employee.employee_first_name} - ${employee.employee_email}`;
                    userInfoSpan.classList.add('invite-user-info');
                    employeeDiv.appendChild(userInfoSpan);

                    const circleIcon = document.createElement('span');
                    circleIcon.classList.add('invite-circle');
                    circleIcon.textContent = '◯';
                    employeeDiv.appendChild(circleIcon);

                    // Check if user is already selected
                    if (selectedUsersForGroupChat.includes(employee.employee_email)) {
                        circleIcon.classList.add('selected');
                        circleIcon.textContent = '✔️';
                    }

                    userListDiv.appendChild(employeeDiv);
                    circleIcon.addEventListener('click', () => {
                        toggleUserSelectionForGroupChat(employee.employee_email);
                        circleIcon.classList.toggle('selected');
                        circleIcon.textContent = circleIcon.classList.contains('selected') ? '✔️' : '◯';
                    
                        // Update the number next to the "Invite Users" button
                        const inviteUsersButton = document.getElementById('invite-users-button');
                        const numSelectedUsers = selectedUsersForGroupChat.length;
                        console.log('inviteUsersButton:', inviteUsersButton); 
                        console.log('numSelectedUsers:', numSelectedUsers); 
                        inviteUsersButton.querySelector('.invite-users-count').textContent = numSelectedUsers;
                    });
                });
            }
        })
        .catch(error => {
            console.error('Error fetching employee list:', error);
        });

    // Pass selected users to the Invite Users modal
    const inviteUsersButton = document.getElementById('invite-users-button');
    const numSelectedUsers = selectedUsersForGroupChat.length;
    console.log('inviteUsersButton:', inviteUsersButton); 
    console.log('numSelectedUsers:', numSelectedUsers); 
    inviteUsersButton.querySelector('.invite-users-count').textContent = numSelectedUsers;
}
// Function to confirm the invitation and return to the group chat modal
function confirmInvite() {
    // Pass the selected users back to the Group Chat modal
    const selectedUsers = selectedUsersForGroupChat.slice(); 

    closeModal();

    // Show the Group Chat modal
    showModal('group-chat');

    // Update the Group Chat modal with the selected users
    updateGroupChatWithSelectedUsers(selectedUsers);
}

// Function to update the Group Chat modal with selected users
function updateGroupChatWithSelectedUsers(selectedUsers) {
    console.log('Updating Group Chat with selected users:', selectedUsers);
}



//API'S for displaying chats
function fetchChatsFromApi() {
    console.log("Attempting to fetch chats from API...");
    fetch('/chat/api/chats/')
        .then(response => {
            console.log("Response received:", response);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log("Data received:", data);
            if (data.chats && Array.isArray(data.chats)) {
                // Sort chats based on the sortDirection
                data.chats.sort((a, b) => {
                    // Handle case where latest_message is null
                    if (!a.latest_message && !b.latest_message) {
                        return 0; 
                    } else if (!a.latest_message) {
                        return 1; 
                    } else if (!b.latest_message) {
                        return -1;
                    }

                    const dateA = new Date(a.latest_message.message_date + ' ' + a.latest_message.message_time);
                    const dateB = new Date(b.latest_message.message_date + ' ' + b.latest_message.message_time);
                    return sortDirection === 'newest' ? dateB - dateA : dateA - dateB;
                });
                displayChats(data.chats);
            } else {
                console.error('Unexpected data format received:', data);
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

function displayChats(chats) {
    const chatList = document.getElementById('chat-list');
    chatList.innerHTML = '';  // Clear the chat list before adding new data

    chats.forEach(chat => {
        const chatElement = document.createElement('div');
        chatElement.classList.add('chat-item', chat.is_group_chat ? 'group-chat' : 'one-on-one-chat');
        
        const isUnread = !chat.is_read;
        chatElement.classList.add(isUnread ? 'unread' : 'read');

        chatElement.dataset.chatId = chat.chat_id;
        chatElement.dataset.chatName = chat.chat_name;

        const chatNameHTML = chat.is_group_chat ?
            `<span class="chat-name">${chat.chat_name} <span class="group-chat-indicator">👥</span></span>` :
            `<span class="chat-name">${chat.chat_name}</span>`;
        
        const unreadIndicator = isUnread ? '<span class="unread-indicator"></span>' : '';
        
        const chatPreviewHTML = chat.latest_message ? 
            `<p class="chat-preview">${unreadIndicator}<strong>${chat.latest_message.sender}:</strong> ${chat.latest_message.message_text}</p>` :
            `<p class="chat-preview">No messages yet</p>`;

 
        const timestampHTML = chat.latest_message ? formatTimestamp(chat.latest_message.message_date, chat.latest_message.message_time) : 'No timestamp';
        
        // Define the action label based on chat type
        const actionLabel = chat.is_group_chat ? 'Leave Chat' : 'Delete Chat';

        // Build the innerHTML for the chat item
        chatElement.innerHTML = `
            <div class="chat-info" onclick="openChat('${chat.chat_name}')">
                ${chatNameHTML}
                ${chatPreviewHTML}
            </div>
            <div class="chat-meta">
                <span class="star-icon ${chat.chat_favourited ? 'favourited' : ''}">${chat.chat_favourited ? '★' : '☆'}</span>
                <div class="chat-options">
                    <span class="menu-dots">⋮</span>
                    <div class="dropdown-menu" style="display: none;">
                        <div class="dropdown-item" onclick="${chat.is_group_chat ? 'leaveChat' : 'deleteChat'}(event, '${chat.chat_id}')">${actionLabel}</div>
                        <div class="dropdown-item" onclick="toggleMute(event, '${chat.chat_id}')">
                            <span class="mute-icon">${chat.chat_muted ? '🔇' : '🔊'}</span> ${chat.chat_muted ? 'Unmute' : 'Mute'}
                        </div>
                    </div>
                </div>
                <span class="timestamp">${timestampHTML}</span>
            </div>
        `;

        // Add event listeners after setting HTML
        chatElement.querySelector('.chat-meta > .star-icon').addEventListener('click', function(event) {
            event.stopPropagation();
            toggleFavourite(event, chat.chat_id, this); 
        });
        chatElement.querySelector('.chat-meta > .chat-options').addEventListener('click', showMenu);

        chatList.appendChild(chatElement);
    });

    updateNoChatsMessage();
    updateUnreadCount();
}

function formatTimestamp(date, time) {
    const messageDate = new Date(date);
    const messageTime = new Date(`1970-01-01T${time}`);
    const now = new Date();

    if (messageDate.toDateString() === now.toDateString()) {
        return `Today, ${formatTime(messageTime)}`;
    } else {
        return `${messageDate.toLocaleDateString()}, ${formatTime(messageTime)}`;
    }
}

function formatTime(time) {
    let hours = time.getHours();
    const minutes = time.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; 
    const formattedTime = `${hours}:${minutes < 10 ? '0' + minutes : minutes} ${ampm}`;
    return formattedTime;
}

function startIndependentChat() {
    const email = document.getElementById('independent-chat-email').value;
    if (!email) {
        alert('Please enter an email address.');
        return;
    }

    fetch('/chat/api/invite_single_chat/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRFToken': getCSRFToken()
        },
        body: JSON.stringify({ invited_email: email })  
    })
    .then(response => {
        if (!response.ok) {
      
            return response.json().then(data => {
                throw new Error(data.error || 'Unknown server error');
            });
        }
        return response.json();
    })
    .then(data => {
        alert(data.success); 
        document.getElementById('independent-chat-email').value = ''; // Reset the email input field
        closeModal();

        // Fetch the updated chat list from the API and update the DOM
        fetchChatsFromApi();
    })
    .catch(error => {
        console.error('Fetch error:', error.message);
        alert(error.message); // Reflect the error in the UI 
    });
}

//Invitation API's

// Function to fetch invitations from the API
function fetchInvitationsFromApi() {
    console.log("Attempting to fetch invitations from API...");
    fetch('/chat/api/invitations/')
        .then(response => {
            console.log("Response received:", response);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log("Data received:", data);
            if (data.invitations && Array.isArray(data.invitations)) {
                displayInvitations(data.invitations);
                updateInvitationCount(data.invitation_count); // Update the invitation count

            } else {
                console.error('Unexpected data format received:', data);
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

// Function to display invitations in the modal
function displayInvitations(invitations) {
    const invitationList = document.getElementById('invitation-list');
    invitationList.innerHTML = '';  // Clear the invitation list before adding new data

    invitations.forEach(invitation => {
        const invitationElement = document.createElement('div');
        invitationElement.classList.add('invitation');
        invitationElement.dataset.invitationId = invitation.invitation_id; // Set the invitation ID as a data attribute
        invitationElement.innerHTML = `
            <p>${invitation.invitation_text}</p>
            <button class="accept-invitation" onclick="acceptInvitation(${invitation.invitation_id})">Accept</button>
            <button class="deny-invitation" onclick="denyInvitation(${invitation.invitation_id})">Deny</button>
        `;
        invitationList.appendChild(invitationElement);
    });
}

// Function to update the invitation count in the HTML
function updateInvitationCount(count) {
    console.log("Updating invitation count to:", count);
    const invitationCountElement = document.getElementById('invitation-count');
    invitationCountElement.textContent = count === 0 ? '0' : count;
}

function denyInvitation(invitationId) {
    console.log("Denying invitation with ID:", invitationId);
    fetch(`/chat/api/deny-invitation/${invitationId}/`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({}),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to deny invitation');
        }
      
        return response.json();
    })
    .then(data => {
        console.log("Invitation denied successfully:", data);
        // Remove the denied invitation from the UI
        removeInvitationFromUI(invitationId);
        // Decrement the invitation count in the UI
        decrementInvitationCount();
    })
    .catch(error => {
        console.error('Error denying invitation:', error);
        // Handle errors and display appropriate messages to the user
    });
}

function acceptInvitation(invitationId) {
    console.log("Accepting invitation with ID:", invitationId);
    fetch(`/chat/api/accept-invitation/${invitationId}/`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to accept invitation');
        }
        return response.json();
    })
    .then(data => {
        console.log("Invitation accepted successfully:", data);
        // Remove the accepted invitation from the UI
        removeInvitationFromUI(invitationId);

        decrementInvitationCount();

        fetchChatsFromApi();
    })
    .catch(error => {
        console.error('Error accepting invitation:', error);
    });
}

// Function to decrement the invitation count
function decrementInvitationCount() {
    const invitationCountElement = document.getElementById('invitation-count');
    let currentCount = parseInt(invitationCountElement.textContent || '0', 10);
    currentCount = Math.max(0, currentCount - 1); // Ensure count doesn't go negative
    invitationCountElement.textContent = currentCount;
}

// Function to remove the denied invitation from the UI
function removeInvitationFromUI(invitationId) {
    const invitationElement = document.querySelector(`.invitation[data-invitation-id="${invitationId}"]`);
    if (invitationElement) {
        invitationElement.remove();
    }
}

//CHAT INTERACTIONS

// Open specific chat
function openChat(chatId) {
    console.log("Opening chat:", chatId);

    window.location.href = `/chat/chats/?chatId=${chatId}`;
}


function startGroupChat() {
    //logic to handle group chat creation
    console.log('Creating group chat...');
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
                    chat.style.display = 'none';
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
    const unreadChats = document.querySelectorAll('.chat-item.unread').length; // Count unread chats
    document.getElementById('unread-count').textContent = unreadChats;
}


function updateNoChatsMessage() {
    const chatList = document.getElementById('chat-list');
    const noChatsMessage = document.getElementById('no-chats-message');
    // Check if there are visible chats
    const visibleChats = chatList.querySelectorAll('.chat-item:not([style*="display: none"])');
    if(visibleChats.length === 0) {
        // If no chats are visible, show the no chats message
        noChatsMessage.style.display = 'block';
    } else {
        // If there are visible chats, hide the no chats message
        noChatsMessage.style.display = 'none';
    }
}

//INVITATION FUNCTIONALITY

// Function to open the invitation modal
function openInvitationModal() {
    document.getElementById('invitation-list-modal').style.display = 'block';
}

// Function to close the invitation modal
function closeInvitationModal() {
    document.getElementById('invitation-list-modal').style.display = 'none';
}


// Toggle mute when icon is clicked on specific chat
function toggleMute(event, chatId) {
    event.stopPropagation(); 
    console.log('Toggling mute for chat ID:', chatId);

    const muteIcon = event.currentTarget.querySelector('.mute-icon');
    const muteTextElement = muteIcon.nextSibling.nodeType === 3 ? muteIcon.nextSibling : muteIcon.nextElementSibling;

    fetch(`/chat/api/toggle-mute/${chatId}/`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRFToken': getCSRFToken()
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error:', data.error);
       
        } else {
            console.log('Toggle successful:', data.message);
      
            if (data.muted) {
                muteIcon.textContent = '🔇'; // Change to muted icon
                muteTextElement.nodeValue = ' Unmute'; 
            } else {
                muteIcon.textContent = '🔊'; // Change to unmuted icon
                muteTextElement.nodeValue = ' Mute'; 
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
      
    });
}
// Utility function to check if an element is near the bottom of its parent 
function isNearBottom(element) {
    const rect = element.getBoundingClientRect();
    const parentRect = element.offsetParent.getBoundingClientRect();
    return (rect.bottom + rect.height) >= parentRect.bottom;
}


// Function to close any other open menus when one is opened currently
function showMenu(event) {
    event.stopPropagation();  

  
    const chatItem = event.target.closest('.chat-item');
    const menu = chatItem.querySelector('.dropdown-menu');

  
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';

    // Close any other open menus
    document.querySelectorAll('.chat-item .dropdown-menu').forEach(otherMenu => {
        if (otherMenu !== menu) {
            otherMenu.style.display = 'none';
        }
    });
}


function closeAllDropdowns() {
    document.querySelectorAll('.chat-item .dropdown-menu').forEach(function(menu) {
        menu.style.display = 'none';
    });
}


// Function to open confirmation modal
function openConfirmationModal(action, chatId) {
    const modal = document.getElementById('confirmation-modal');
    modal.dataset.chatId = chatId;
    modal.dataset.action = action;
    modal.style.display = 'block';
    
    // Update modal title and confirm button text based on the action
    document.getElementById('modal-title').textContent = `Are you sure you want to ${action.toLowerCase()} this chat?`;
    const confirmBtn = document.getElementById('confirm-btn');
    confirmBtn.textContent = action; // Set button text to "Leave" or "Delete"
    confirmBtn.onclick = function() { confirmAction(chatId); };
}


// Function to close all modals and the overlay
function closeModal() {
    const modalOverlay = document.querySelector('.modal-overlay');
    modalOverlay.style.display = 'none';

    const modals = document.querySelectorAll('.my-modal');
    modals.forEach(modal => modal.style.display = 'none');

    // Reset the email input for creating a single chat
    const emailInput = document.getElementById('independent-chat-email');
    if (emailInput) {
        emailInput.value = ''; // Clear the input field
    }

    // Check and reset selected users if necessary when closing modals
    if (shouldResetUsers) {
        resetSelectedUsersForGroupChat();  // This will also update the UI
        shouldResetUsers = false;  // Reset the flag after use
    }
}


let shouldResetUsers = false;


function createNewChat() {
    shouldResetUsers = true;  
    closeModal();  
    showModal('new-chat');
}


function handleDeleteSuccess(chatId) {
    // Find the chat item in the UI
    const chatItem = document.querySelector(`.chat-item[data-chat-id="${chatId}"]`);
    if (chatItem) {
        // Add the class that triggers the animation
        chatItem.classList.add('removing');

        // Set a timeout to remove the chat item from the DOM after the animation
        setTimeout(() => {
            chatItem.remove();
        }, 400); // Match this timeout to the duration of the animation
    } else {
        console.error(`Chat item with ID ${chatId} not found.`);
    }
}
// Function to handle errors when deleting a chat record
function handleDeleteError(error) {
    console.error('Error deleting chat:', error);
    
}


function deleteChat(event, chatId) {
    event.stopPropagation();
    openConfirmationModal('Delete', chatId);
}

function leaveChat(event, chatId) {
    event.stopPropagation(); 
    openConfirmationModal('Leave', chatId); 
}


function confirmAction(chatId) {
    const modal = document.getElementById('confirmation-modal');
    const action = modal.dataset.action;

    if (action === 'Delete') {
        sendDeleteRequest(chatId);
    } else if (action === 'Leave') {
        sendLeaveRequest(chatId);
    }
}

// AJAX call to delete single chat
function sendDeleteRequest(chatId) {
    fetch(`/chat/api/delete-chat/${chatId}/`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRFToken': getCSRFToken()
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to delete chat');
        }
        return response.json();
    })
    .then(data => {
        closeModal();
        handleDeleteSuccess(chatId);
    })
    .catch(error => {
        closeModal();
        handleDeleteError(error);
    });
}

function sendLeaveRequest(chatId) {
    const chatItem = document.querySelector(`.chat-item[data-chat-id="${chatId}"]`);
    
    // Check if the chatItem exists in the DOM
    if (!chatItem) {
        console.error("Chat item not found");
        return;
    }

    // Trigger the removing animation
    chatItem.classList.add('removing');

    fetch(`/chat/api/leave-chat/${chatId}/`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRFToken': getCSRFToken()
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to leave chat');
        }
        return response.json();
    })
    .then(data => {
        // Wait for the animation to complete before removing the chat item
        setTimeout(() => {
            chatItem.remove();
            closeModal(); 
            fetchChatsFromApi(); // Refresh the chat list
        }, 400); // This timeout should match the duration of your CSS animation
    })
    .catch(error => {
        console.error('Error leaving chat:', error);
        alert(error.message); // Show an error message
    });
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

// AJAX call to toggle favourite chat
function toggleFavourite(event, chatId, starIcon) {
    event.stopPropagation();

    if (!starIcon) {
        console.error('No star icon found in chat item');
        return;
    }

    fetch(`/chat/api/toggle-favourite/${chatId}/`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRFToken': getCSRFToken()
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error:', data.error);
          
            starIcon.classList.toggle('favourited');
            starIcon.textContent = starIcon.classList.contains('favourited') ? '★' : '☆';
        } else {
            console.log('Toggle successful:', data.message);
     
            starIcon.classList.toggle('favourited', data.favourited);
            starIcon.textContent = data.favourited ? '★' : '☆';
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        starIcon.classList.toggle('favourited');
        starIcon.textContent = starIcon.classList.contains('favourited') ? '★' : '☆';
    });
}

function getCSRFToken() {
    const cookies = document.cookie.split(';');
    for (const cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'csrftoken') {
            return value;
        }
    }
    return '';
}

// AJAX call to create group chat
function createGroupChat() {
    const groupName = document.getElementById('group-chat-name').value;
    if (!groupName) {
        alert('Please enter a name for the group chat.');
        return;
    }

    const groupDescription = "This group chat is used for members of the text-chat subsystem to communicate";  // Default description

    console.log('Attempting to create group chat with name:', groupName);
    console.log('Selected users for invite:', selectedUsersForGroupChat);

    const requestData = {
        group_name: groupName,
        group_description: groupDescription,  // Adding group description
        invitees: selectedUsersForGroupChat 
    };

    console.log('Request data being sent:', requestData);

    fetch('/chat/api/create_group_chat/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRFToken': getCSRFToken()
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Response received:', response);
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || 'Unknown server error');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Group chat created successfully:', data);
        alert('Group chat created successfully!');
        closeModal(); // Close the modal after creation
        fetchChatsFromApi(); // Refresh the chat list
    })
    .catch(error => {
        console.error('Error creating group chat:', error);
        alert(error.message);
    });
}

let sortDirection = 'newest'; // Default sort chats by newest

function toggleSortOptions() {
    const sortOptions = document.getElementById('sort-options');
    sortOptions.style.display = sortOptions.style.display === 'block' ? 'none' : 'block';
}

function applySortOption(sortOrder) {
    sortDirection = sortOrder;
    document.querySelector('.sort-text').textContent = sortOrder.charAt(0).toUpperCase() + sortOrder.slice(1); // Update button text to "Newest" or "Oldest"
    document.getElementById('sort-options').style.display = 'none'; // Hide the dropdown menu
    fetchChatsFromApi(); // Re-fetch and sort the chats
}

// Delegated event handling for the chat list
document.getElementById('chat-list').addEventListener('click', function(event) {
    let targetElement = event.target;

 
    if (targetElement && (targetElement.matches('.star-icon') || targetElement.closest('.star-icon'))) {
        targetElement = targetElement.closest('.star-icon'); 

        if (!targetElement) {
            console.error("Failed to find star icon from the clicked element.");
            return;
        }

        const chatItem = targetElement.closest('.chat-item');
        if (chatItem) {
            const chatName = chatItem.dataset.chatName;
            toggleFavourite(event, chatName, targetElement);
            return; 
        }
    }

    if (event.target.closest('.chat-item')) {
        const chatItem = event.target.closest('.chat-item');
        if (chatItem) {
            const chatId = chatItem.dataset.chatId;  // Using chatId
            openChat(chatId);
            console.log("Chat opened for ID:", chatId);
        }
    }
});

document.addEventListener('click', function(event) {
    // Handling clicks outside to close dropdowns
    if (!event.target.matches('.menu-dots')) {
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(function(menu) {
            menu.style.display = 'none';
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded");

    initEventHandlers();
    fetchChatsFromApi();
    fetchInvitationsFromApi();
});

function initEventHandlers() {
    const chatList = document.getElementById('chat-list');
    chatList.addEventListener('click', handleChatListClick);

    const filterButtons = document.querySelectorAll('.filter-button');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            filterChats(this.dataset.filter);
        });
    });

    const sortOptions = document.getElementById('sort-options');
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#sort-button') && !event.target.closest('#sort-options')) {
            sortOptions.style.display = 'none';
        }
    });

    document.querySelectorAll('.sort-option').forEach(option => {
        option.addEventListener('click', function() {
            applySortOption(this.textContent.trim().toLowerCase());
        });
    });
}

function handleChatListClick(event) {
    const target = event.target;
    
    if (target.matches('.star-icon') || target.closest('.star-icon')) {
        const starIcon = target.closest('.star-icon');
        const chatItem = starIcon.closest('.chat-item');
        if (chatItem) {
            toggleFavourite(event, chatItem.dataset.chatName, starIcon);
        }
    } else if (target.closest('.chat-item')) {
        const chatItem = target.closest('.chat-item');
        openChat(chatItem.dataset.chatId);
    } else if (target.matches('.menu-dots')) {
        toggleDropdown(target);
    }
}

function toggleDropdown(menuDots) {
    const dropdownMenu = menuDots.nextElementSibling;
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    closeOtherDropdowns(menuDots);
}

function closeOtherDropdowns(exceptMenuDots) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== exceptMenuDots.nextElementSibling) {
            menu.style.display = 'none';
        }
    });
}