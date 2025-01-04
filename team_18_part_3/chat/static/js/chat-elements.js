function getCSRFToken() {
	//get the csrf token
    return csrfToken;
}

function getAPIRequests() {
    console.log("Attempting to fetch chats from API...");

    //get the CSRF token
    const csrfToken = getCSRFToken();
    console.log(csrfToken);
	//get the chat id form the url
    const urlParams = new URLSearchParams(window.location.search);
    const chatId = urlParams.get('chatId');
	//pass the chat id to this function in the views.py file
    fetch(`/chat/api/mainchats/${chatId}/`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRFToken': csrfToken
        }
    })
    .then(response => {
        console.log("Response received:", response);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log("Data received:", data);
        //check if the data is received as expected
        if (data && typeof data.is_group === 'boolean' && data.members && Array.isArray(data.members)) {
            const isGroup = data.is_group;
            const members = data.members;
            const messages = data.messages;
            const currentEmployeeId = data.current_employee_id;
            current_chat_id = data.chat_id;
            console.log("current chat id", current_chat_id);
            
            listMembers(members, isGroup, currentEmployeeId);
            
            if (typeof messages === 'object' && Object.keys(messages).length > 0) {
                chatMessages(messages);
            } else {
                console.log('No messages found.');
            }
            //check whether the chat is a group or not
            if (isGroup) {
                console.log('This is a group chat.');
            } else {
                console.log('This is not a group chat.');
            }
        } else {
            console.error('Unexpected data format received:', data);
        }
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

//add the details to the chat setting modal
function listMembers(members, isGroup, currentEmployeeId) {
    const memberList = document.getElementById('chat-members-list');
    const chatOptions = document.getElementById('chat-settings');
    memberList.innerHTML = '';
    chatOptions.innerHTML = '';
    let adminOptions = false;
	console.log(isGroup);
    if (isGroup) {
        members.forEach(member => {
            //make a div element for each member
            const memberElement = document.createElement('div');
            memberElement.classList.add('members');
            memberElement.dataset.memberId = member.id;

            //check if each member is an admin
            const isAdmin = member.is_admin ? 'admin' : '';

			if (member.id == currentEmployeeId) {
				//if the member is an admin, add these elements to the div
				memberElement.innerHTML = `
					<div class="members" id="myDetails">
						<p class="member-name"><span class="admin">${isAdmin}</span>You</p> 
					</div>`;
				if (isAdmin) {
					adminOptions = true;
				} else {
					adminOptions = false;
				}
	
				//append the member element to the members list
				memberList.appendChild(memberElement);
			} else {
				//if the member isn't an admin, add these elements to the div
				memberElement.innerHTML = `
					<div class="members" id="mID${member.id}">
						<p class="member-name"><span class="admin">${isAdmin}</span>${member.first_name} ${member.surname}</p> 
					</div>`;
	
				//append the member element to the members list
				memberList.appendChild(memberElement);
			}
        });
        console.log(adminOptions);
        if (adminOptions) {
		//show these memebrs if logged in member is an admin
        	chatOptions.innerHTML = `
				<div id="invitemember" class="msg-options">
					<p>Invite new chat member</p>
				</div>
				<div id="mutechat" class="msg-options">
					<p><span id="muteChat"><img src="" alt="" id="sound-img"></span>Mute chat</p>
				</div>
				<div id="leavechat" class="msg-options">
					<p>Leave chat</p>
				</div>`;
        } else {
		//show these options if logged in member isn't an admin
        	chatOptions.innerHTML = `
				<div id="invitemember" class="msg-options">
					<p>Invite new chat member</p>
				</div>
				<div id="mutechat" class="msg-options">
					<p><span id="muteChat"><img src="" alt="" id="sound-img"></span>Mute chat</p>
				</div>
				<div id="leavechat" class="msg-options">
					<p>Leave chat</p>
				</div>`;
			document.getElementById("invitemember").style.display = "none";
        }
        
        //open add new member modal
        document.getElementById("invitemember").addEventListener("click", function() {
			event.preventDefault();
    		inviteMembers.style.display = "block";
    		chatSettings.style.display = "none";
		});
		
		document.getElementById("add-btn").addEventListener("click", function() {
			let recipientEmail = "";
			recipientEmail = document.getElementById("input-email").textContent;

			//pass these details to views.py
			const formData = new FormData();
			formData.append('chat_id', current_chat_id);
			formData.append('recipient_email', recipientEmail);
	
			fetch('/chat/send_invitation/', {
				method: 'POST',
				headers: {
					'X-CSRFToken': getCSRFToken() 
				},
				body: formData
			})
			.then(response => {
				if (!response.ok) {
					if (response.status === 422) {
						//error if incorrect email is entered
						document.getElementById("error-message").textContent = "Incorrect email entered. Please try again.";
						throw new Error('New member entry is incorrect');
					} else {
						throw new Error('Network response was not ok');
					}
				}
				return response.json();
			})
			.then(data => {
				console.log('Invitation sent successfully', data);
				document.getElementById("input-email").textContent = "";
				document.getElementById("inviteMembers").style.display = "none";
			})
			.catch(error => {
				console.error('There was a problem with the fetch operation:', error);
			});
		});
    } else {
        members.forEach(member => {
            //make a div element for each member
            const memberElement = document.createElement('div');
            memberElement.classList.add('members');
            memberElement.dataset.memberId = member.id;
			console.log("current employee", currentEmployeeId);
			console.log("current member id", member.id);
			if (member.id == currentEmployeeId) {
				//if member is the logged in member, add these elements to the div
				memberElement.innerHTML = `
					<div class="members" id="myDetails">
						<p class="member-name">You</p> 
					</div>`;
			
				//append the member element to the members list
				memberList.appendChild(memberElement);
			} else {
				//if the member isn;t the logged in member, add these elements to the div
				memberElement.innerHTML = `
					<div class="members" id="mID${member.id}">
						<p class="member-name">${member.first_name} ${member.surname}</p> 
					</div>`;
			
				//append the member element to the members list
				memberList.appendChild(memberElement);
			}
        });
        //add these options for an individual chat
        chatOptions.innerHTML = `
			<div id="mutechat" class="msg-options">
				<p><span id="muteChat"><img src="" alt="" id="sound-img"></span>Mute chat</p>
			</div>
			<div id="leavechat" class="msg-options">
				<p>Leave chat</p>
			</div>`;
    }
    
    var img = document.getElementById("sound-img");

	//send this data to views.py
    const formData = new FormData();
	formData.append('chat_id', current_chat_id);
    
    fetch('/chat/muted_status/', {
		method: 'POST',
		headers: {
			'X-CSRFToken': getCSRFToken()
		},
		body: formData
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Network response was not ok');
		}
		return response.json();
	})
	.then(data => {
		if (data.is_muted.is_muted == 1) {
			//show the image to reflect the chat is muted
			console.log("muted chat");
			img.src = "/static/img/chat_images/mute.jpg";
		} else {
			//show the image to reflect the chat is unmuted
			console.log("unmuted chat");
			img.src = "/static/img/chat_images/sound.jpg";
		}
	})
	.catch(error => {
		console.error('There was a problem with the fetch operation:', error);
	});
    
    $(document).on('click', '[id^="mID"]', function(event) {
        event.preventDefault();
        var memberId = this.id.replace('mID', '');
        makeMemberModal(memberId, members, isGroup, currentEmployeeId);
    });
	
    //toggle mute chat function
    document.getElementById("mutechat").addEventListener("click", function() {
        if (img.src.includes("sound.jpg")) {
		//send thid data to views.py
			const formData = new FormData();
			formData.append('chat_id', current_chat_id);
			
            fetch('/chat/mute_chat/', {
                method: 'POST',
                headers: {
                    'X-CSRFToken': getCSRFToken()
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Chat muted successfully', data);
                img.src = "/static/img/chat_images/mute.jpg";
                img.alt = "";
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
        } else {
		//send this data to views.py
        	const formData = new FormData();
			formData.append('chat_id', current_chat_id);
			
            fetch('/chat/unmute_chat/', {
                method: 'POST',
                headers: {
                    'X-CSRFToken': getCSRFToken() 
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Chat unmuted successfully', data);
                img.src = "/static/img/chat_images/sound.jpg";
                img.alt = "";
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
        }
    });
    
    //open leave chat modal
    document.getElementById("leavechat").addEventListener("click", function() {
		event.preventDefault();
		leaveChat = document.getElementById('leaveChat');
		leaveChat.style.display = "block";
		chatSettings.style.display = "none";
	});
    
    //leave chat functions
    document.getElementById("leave-btn").addEventListener("click", function() {
        if (isGroup) {
		//send this data to views.py
        	const formData = new FormData();
			formData.append('chat_id', current_chat_id);
        	
        	fetch('/chat/leave_group_chat/', {
                method: 'POST',
                headers: {
                    'X-CSRFToken': getCSRFToken()
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Chat left successfully', data);
		    //take user back to chat list after they leave the chat
                if (data.redirect_url) {
					window.location.href = data.redirect_url;
				} else {
					console.error('Redirect URL not found in the response data');
				}
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
        } else {
		//send this data to views.py
        	const formData = new FormData();
			formData.append('chat_id', current_chat_id);
			
            fetch('/chat/leave_individual_chat/', {
                method: 'POST',
                headers: {
                    'X-CSRFToken': getCSRFToken()
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Chat left successfully', data);
		    //take user bakc to chat list page after leaving chat
                if (data.redirect_url) {
					window.location.href = data.redirect_url;
				} else {
					console.error('Redirect URL not found in the response data');
				}
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
        }
    });
}

//load the chat messages
function chatMessages(messages) {
	console.log(messages);
    const chatScroll = document.getElementById('chat-scroll');
    chatScroll.innerHTML = '';
    const staticUrl = chatScroll.getAttribute('data-static-url');
    
    let prevDate = '';

    Object.values(messages).forEach(message_data => {
	    //format the date
    	const date = new Date(message_data.message_date);
    	const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    	const formattedDate = date.toLocaleDateString('en-US', options);

        if (formattedDate !== prevDate) {
            const messageDateElement = document.createElement('p');
            messageDateElement.classList.add('message-date');
            messageDateElement.textContent = formattedDate;
            chatScroll.appendChild(messageDateElement);

            prevDate = formattedDate;
        }

	    //format the time
        const messageTime = message_data.message_time.split(':');
    	const hours = messageTime[0];
    	const minutes = messageTime[1];
    
        const messageElement = document.createElement('div');

	    //create these elements if the logged in user is the sender of the message
        if (message_data.is_sender) {
            messageElement.classList.add('message-area');
            messageElement.innerHTML= `
                <div class="message-send">
                    <div class="message">
                        <p class="message-owner">
                            <span>${message_data.sender_first_name} ${message_data.sender_surname}</span>
                            <span><img src="${staticUrl}" alt="" class="dot-img" id="sendOptions${message_data.message_id}"></span>
                            <div id="sendModal${message_data.message_id}" class="my-modal">
                                <div class="my-modal-content">
                                    <span class="sendClose">&times;</span>
                                    <div class="delete-msg-btn msg-options" id="deleteMessage${message_data.message_id}">
                                        <p>Delete message</p>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p class="message-text">${message_data.message_text}</p>
                        <br>
                        <p class="message-time">${hours}:${minutes}</p>
                    </div>
                    <div class="message-user">
                        <p class="user-initials">${message_data.sender_first_name.slice(0, 1)}${message_data.sender_surname.slice(0, 1)}</p>
                    </div>
                </div>`;
        } else {
		//create these elements of the logged in user isn;t the sender of the message
            messageElement.classList.add('message-area');
            messageElement.innerHTML= `
                <div class="message-receive">
                    <div class="message-user">
                        <p class="user-initials">${message_data.sender_first_name.slice(0, 1)}${message_data.sender_surname.slice(0, 1)}</p>
                    </div>
                    <div class="message">
                        <p class="message-owner">
                            <span>${message_data.sender_first_name} ${message_data.sender_surname}</span>
                            <span><img src="${staticUrl}" alt="" class="dot-img" id="receiveOptions${message_data.message_id}"></span>
                            <div id="receiveModal${message_data.message_id}" class="my-modal">
                                <div class="my-modal-content">
                                    <span class="receiveClose">&times;</span>
									<div class="report-btn msg-options" id="reportMessage${message_data.message_id}">
										<p>Report this message</p>
									</div>
                                </div>
                            </div>
                        </p>
                        <p class="message-text">${message_data.message_text}</p>
                        <br>
                        <p class="message-time">${hours}:${minutes}</p>
                    </div>
                </div>`;
        }
        
        //append the message element to the chat scroll
        chatScroll.appendChild(messageElement);
    });
	
    //click buttons to show message option modals
    $(document).on('click', '[id^="receiveOptions"]', function(event) {
		event.preventDefault();
		var messageId = this.id.replace('receiveOptions', '');
		console.log(messageId);
		var receiveModal = document.getElementById('receiveModal' + messageId); 
		receiveModal.style.display = "block";
	});
	
	$(document).on('click', '[id^="sendOptions"]', function(event) {
		event.preventDefault();
		var messageId = this.id.replace('sendOptions', '');
		console.log(messageId);
		var sendModal = document.getElementById('sendModal' + messageId);
		sendModal.style.display = "block";
	});
	
	//click button to delete message
	$(document).on('click', '[id^="deleteMessage"]', function(event) {
		event.preventDefault();
		var messageId = this.id.replace('deleteMessage', '');
		console.log(messageId);
		//send this data to views.py
		const formData = new FormData();
		formData.append('chat_id', current_chat_id);
		formData.append('message_id', messageId);
		
		fetch('/chat/delete_message/', {
			method: 'POST',
			headers: {
				'X-CSRFToken': getCSRFToken()
			},
			body: formData
		})
		.then(response => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then(data => {
			console.log('Message deleted successfully:', data);
			document.getElementById('sendModal' + messageId).style.display = "none";
			getAPIRequests();
		})
		.catch(error => {
			console.error('There was a problem with the fetch operation:', error);
		});
	});
	
	//click button to report message
	$(document).on('click', '[id^="reportMessage"]', function(event) {
		event.preventDefault();
		var messageId = this.id.replace('reportMessage', '');
		console.log(messageId);

		//send this data to views.py
		const formData = new FormData();
		formData.append('chat_id', current_chat_id);
		formData.append('selected_message_id', messageId);
		
		fetch('/chat/report_employee_message/', {
			method: 'POST',
			headers: {
				'X-CSRFToken': getCSRFToken()
			},
			body: formData
		})
		.then(response => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then(data => {
			console.log('Message reported successfully:', data);
			document.getElementById('receiveModal' + messageId).style.display = "none";
		})
		.catch(error => {
			console.error('There was a problem with the fetch operation:', error);
		});
	});
}

//function to craete and send a chat message
function createChatMessage() {
    const messageText = document.getElementById('message-type').value;
    //send this data to views.py
    const formData = new FormData();
    formData.append('chat_id', current_chat_id);
    formData.append('message_text', messageText);
    
    fetch('/chat/create_chat/', {
        method: 'POST',
        headers: {
            'X-CSRFToken': getCSRFToken()
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 422) {
                throw new Error('Message text cannot be empty');
            } else {
                throw new Error('Network response was not ok');
            }
        }
        return response.json();
    })
    .then(data => {
        console.log('Chat message created successfully:', data);
        getAPIRequests();
        document.getElementById('message-type').value = '';
        scrollToBottom();
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

//function to create the member modal with the correct elements
function makeMemberModal(member_id, members, isGroup, currentEmployeeId) {
	console.log(member_id);
	const memberdetails = document.getElementById("memberdetails");
    memberdetails.style.display = "block";
    const chatSettings = document.getElementById("chatSettings");
    chatSettings.style.display = "none";
    
    const blockImgPath = document.getElementById("memberModalImg").getAttribute("block-img-path");
    const reportImgPath = document.getElementById("memberModalImg").getAttribute("report-image-path");
    
    if (isGroup) {
	    //if the chat is a group, add these elements
    	memberdetails.innerHTML = `
			<div class="my-modal-content">
				<span id="detailsClose">&times;</span>
				<div id="addAdmin${member_id}" class="msg-options" class="addingAdmin">
					<p><span id="makeAdmin" class="admin">${isAdmin}</span> Make <span class="member-first-name">${firstName}</span> an admin</p>
				</div>
				<div id="blockMember${member_id}" class="msg-options">
					<p><span id="block"><img src="${blockImgPath}" alt="" id="block-img"></span> Block <span class="member-first-name">${firstName}</span></p>
				</div>
				<div id="reportMember${member_id}" class="msg-options">
					<p><span id="report"><img src="${reportImgPath}" alt="" id="report-img"></span> Report <span class="member-first-name">${firstName}</span></p>
				</div>
			</div>`;
		
		var fullName = "";
		var firstName = "";
		var isAdmin = "";
		
		for (var i = 0; i < members.length; i++) {
			if (members[i].id == member_id) {
				fullName = members[i].first_name + " " + members[i].surname;
				firstName = members[i].first_name;
				isAdmin = members[i].is_admin ? "admin" : "";
				break;
			}
		}
		
		var currentEmployeeIsAdmin = false;
        for (var i = 0; i < members.length; i++) {
            if (members[i].id == currentEmployeeId && members[i].is_admin) {
                currentEmployeeIsAdmin = true;
                break;
            }
        }
        console.log("admin?", currentEmployeeIsAdmin);
        if (!currentEmployeeIsAdmin) {
        	document.getElementById(`addAdmin${member_id}`).style.display = "none";
        }
			
		const memberModalNames = document.querySelectorAll(".member-first-name");
		const memberMakeAdmin = document.getElementById("makeAdmin");
		
		memberModalNames.forEach(nameElement => {
			nameElement.textContent = firstName;
		});
		memberMakeAdmin.textContent = isAdmin;
		
		//toggle make member an admin
		document.querySelectorAll("[id^='addAdmin']").forEach(element => {
			element.addEventListener("click", function(event) {
				event.preventDefault();
				var memberId = this.id.replace('addAdmin', '');
				console.log(memberId);
				var status = document.getElementById("makeAdmin");
				if (status.textContent === "") {
					//send this data to views.py
					const formData = new FormData();
					formData.append('chat_id', current_chat_id);
					formData.append('selected_employee_id', memberId);
					
					console.log("token", getCSRFToken());
					
					fetch('/chat/add_group_admin/', {
						method: 'POST',
						headers: {
							'X-CSRFToken': getCSRFToken()
						},
						body: formData
					})
					.then(response => {
						if (!response.ok) {
							throw new Error('Network response was not ok');
						}
						return response.json();
					})
					.then(data => {
						console.log('New admin created successfully', data);
						status.textContent = "admin";
					})
					.catch(error => {
						console.error('There was a problem with the fetch operation:', error);
					});
				}
			});
		});
    } else {
	    //add these elements if it is an individual chat
    	memberdetails.innerHTML = `
			<div class="my-modal-content">
				<span id="detailsClose">&times;</span>
				<div id="blockMember${member_id}" class="msg-options">
					<p><span id="block"><img src="${blockImgPath}" alt="" id="block-img"></span> Block <span class="member-first-name">${firstName}</span></p>
				</div>
				<div id="reportMember${member_id}" class="msg-options">
					<p><span id="report"><img src="${reportImgPath}" alt="" id="report-img"></span> Report <span class="member-first-name">${firstName}</span></p>
				</div>
			</div>`;
		
		var fullName = "";
		var firstName = "";
		
		for (var i = 0; i < members.length; i++) {
			if (members[i].id == member_id) {
				fullName = members[i].first_name + " " + members[i].surname;
				firstName = members[i].first_name;
				break;
			}
		}
			
		const memberModalNames = document.querySelectorAll(".member-first-name");
		
		memberModalNames.forEach(nameElement => {
			nameElement.textContent = firstName;
		});
    }
    
    var img = document.getElementById("block-img");
	//send this data to views.py
    const formData = new FormData();
	formData.append('chosen_employee_id', member_id);
    
    fetch('/chat/blocked_status/', {
		method: 'POST',
		headers: {
			'X-CSRFToken': getCSRFToken() 
		},
		body: formData
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Network response was not ok');
		}
		return response.json();
	})
	.then(data => {
		console.log("status", data.is_blocked);
		if (data.is_blocked == 1) {
			//show an image to reflect that the employee is blocked
			console.log("blocked employee");
			img.style.display = "inline";
		} else {
			//show no image if the employee isn't blocked
			console.log("unblocked employee");
			img.style.display = "none";
		}
	})
	.catch(error => {
		console.error('There was a problem with the fetch operation:', error);
	});
	
	//toggle block chat member 
    document.querySelectorAll("[id^='blockMember']").forEach(element => {
		element.addEventListener("click", function() {
			event.preventDefault();
			var memberId = this.id.replace('blockMember', '');
			console.log(memberId);
			var img = this.querySelector("img");
			if (img.style.display === "none" || img.style.display === "") {
				//send this data to views.py
				const formData = new FormData();
				formData.append('blocked_employee_id', memberId);
				
				console.log("token", getCSRFToken());
				
				fetch('/chat/block_employee/', {
					method: 'POST',
					headers: {
						'X-CSRFToken': getCSRFToken()
					},
					body: formData
				})
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					console.log('Employee blocked successfully', data);
					//change the image to show that they've been blocked
					img.style.display = "inline";
				})
				.catch(error => {
					console.error('There was a problem with the fetch operation:', error);
				});
			} else {
				//send this data to views.py
				const formData = new FormData();
				formData.append('unblocked_employee_id', memberId);
				
				console.log("token", getCSRFToken());
				
				fetch('/chat/unblock_employee/', {
					method: 'POST',
					headers: {
						'X-CSRFToken': getCSRFToken()
					},
					body: formData
				})
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					console.log('Employee unblocked successfully', data);
					//hide the image when you unblock the employee
					img.style.display = "none";
				})
				.catch(error => {
					console.error('There was a problem with the fetch operation:', error);
				});
			}
		});
	});
    
    //toggle report chat member
    var memberId;
    
    document.querySelectorAll("[id^='reportMember']").forEach(element => {
		element.addEventListener("click", function() {
			event.preventDefault();
			memberId = this.id.replace('reportMember', '');
			console.log(memberId);
			document.getElementById('confirmReportChat').style.display = 'block';
			document.getElementById("memberdetails").style.display = 'none';
		});
	});
	
	$(document).on('click', '#report-btn', function(event) {
        event.preventDefault();
        var reportReason = document.getElementById("input-reason").textContent;
		console.log(memberId);
		//send this data to views.py
		const formData = new FormData();
		formData.append('reported_employee_id', memberId);
		formData.append('report_reason', reportReason);
		
		console.log("token", getCSRFToken());
		
		fetch('/chat/report_employee_no_message/', {
			method: 'POST',
			headers: {
				'X-CSRFToken': getCSRFToken() 
			},
			body: formData
		})
		.then(response => {
			if (!response.ok) {
				if (response.status === 422) {
					document.getElementById("error-reason").textContent = "Please enter a reason.";
					throw new Error('Report reason cannot be empty');
				} else {
					throw new Error('Network response was not ok');
				}
			}
			return response.json();
		})
		.then(data => {
			console.log('Employee reported successfully', data);
			document.getElementById("input-reason").textContent = "";
			document.getElementById("confirmReportChat").style.display = "none";
		})
		.catch(error => {
			console.error('There was a problem with the fetch operation:', error);
		});
    });
}

//function that scrolls to the bottom of the scroll area
function scrollToBottom() {
    var scrollArea = document.getElementById("chat-scroll");
    scrollArea.scrollTop = scrollArea.scrollHeight - scrollArea.clientHeight;
}

document.addEventListener("DOMContentLoaded", function() {
	let current_chat_id = "";
	getAPIRequests();
	//create a scroll area for the chats and take user to the bottom
	scrollToBottom();
    
    //modals
    //click button to show settings modals
    $(document).on('click', '#settings-img', function(event) {
        event.preventDefault();
        chatSettings.style.display = "block";
    });
	
	//close settings modal when x is clicked
    $(document).on('click', '#settingsClose', function(event) {
    	event.preventDefault();
    	chatSettings.style.display = "none";
    });
	
	//close member details modal when x is clicked
	$(document).on('click', '#detailsClose', function(event) {
    	event.preventDefault();
    	memberdetails.style.display = "none";
    });
    
    //close invite modal when x is clicked
	$(document).on('click', '#inviteClose', function(event) {
    	event.preventDefault();
    	document.getElementById("error-message").textContent = "";
    	inviteMembers.style.display = "none";
    });
    
    //close leave modal when x is clicked
	$(document).on('click', '#leaveClose', function(event) {
    	event.preventDefault();
    	leaveChat.style.display = "none";
    });
    
    //close report reason modal when x is clicked
	$(document).on('click', '#reportReasonClose', function(event) {
    	event.preventDefault();
    	document.getElementById("error-reason").textContent = "";
    	confirmReportChat.style.display = "none";
    });
    
    //get the <span> element that closes the message options modal and closes them when clicked
    $(document).on('click', '.receiveClose', function() {
        $(this).closest('.my-modal').hide();
    });

    $(document).on('click', '.sendClose', function() {
        $(this).closest('.my-modal').hide();
    });
    
    //when the user clicks anywhere outside of the modal it closes it
    window.onclick = function(event) {
        var modals = document.getElementsByClassName("my-modal");
        for (var i = 0; i < modals.length; i++) {
            if (event.target == modals[i]) {
                modals[i].style.display = "none";
            }
        }
    };
	
	//send the message into the chat
	const sendButton = document.getElementById('send-message');
	sendButton.addEventListener('click', function(event) {
		event.preventDefault(); 
		createChatMessage(); 
	}); 
});
