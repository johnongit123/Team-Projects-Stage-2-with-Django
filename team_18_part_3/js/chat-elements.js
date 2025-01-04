document.addEventListener("DOMContentLoaded", function() {
    //on load show latest chats
    var scrollArea = document.getElementById("chat-scroll");
    scrollArea.scrollTop = scrollArea.scrollHeight - scrollArea.clientHeight;

    //click button to show settings modals
    $(document).on('click', '#settings-img', function(event) {
        event.preventDefault();
        chatSettings.style.display = "block";
    });
    
    //click to show member details
    $(document).on('click', '[id^="memberId"]', function(event) {
    	event.preventDefault();
    	var memberId = this.id.replace('memberId', '');
    	console.log(memberId);
    	memberdetails.style.display = "block";
    	chatSettings.style.display = "none";
    });
    
    //click to show invite member form
    $(document).on('click', '#invitemember', function(event) {
    	event.preventDefault();
    	inviteMembers.style.display = "block";
    	chatSettings.style.display = "none";
    });
    
    //click to show delete confirmation
    $(document).on('click', '#deletechat', function(event) {
    	event.preventDefault();
    	deleteChat.style.display = "block";
    	chatSettings.style.display = "none";
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
    	inviteMembers.style.display = "none";
    });
    
    //close delete chat modal when x is clicked
	$(document).on('click', '#deleteClose', function(event) {
    	event.preventDefault();
    	deleteChat.style.display = "none";
    });
	
    //get the <span> element that closes the message options modal and closes them when clicked
    var rCloseBtn = document.getElementsByClassName("receiveClose");
    var sCloseBtn = document.getElementsByClassName("sendClose");

    for (var i = 0; i < rCloseBtn.length; i++) {
        rCloseBtn[i].onclick = function() {
            this.parentElement.parentElement.style.display = "none";
        };
    }
    
    for (var i = 0; i < sCloseBtn.length; i++) {
        sCloseBtn[i].onclick = function() {
            this.parentElement.parentElement.style.display = "none";
        };
    }
    
    //when the user clicks anywhere outside of the modal it closes it
    window.onclick = function(event) {
        var modals = document.getElementsByClassName("modal");
        for (var i = 0; i < modals.length; i++) {
            if (event.target == modals[i]) {
                modals[i].style.display = "none";
            }
        }
    };
    
    //toggle mute chat
    document.getElementById("mutechat").addEventListener("click", function() {
		var img = document.getElementById("sound-img");
		if (img.src.includes("sound.jpg")) {
			img.src = "../../images/chat_images/mute.jpg";
			img.alt = "";
		} else {
			img.src = "../../images/chat_images/sound.jpg";
			img.alt = "";
		}
    });
    
    //toggle make member an admin
    document.getElementById("addAdmin").addEventListener("click", function() {
		var status = document.getElementById("makeAdmin");
		if (status.textContent === "admin") {
			status.textContent = "";
		} else {
			status.textContent = "admin";
		}
	});
	
	//toggle block chat member 
    document.getElementById("blockMember").addEventListener("click", function() {
		var img = document.getElementById("block-img");
		if (img.style.display !== "none") {
			img.style.display = "none";
		} else {
			img.style.display = "inline";
		}
    });
    
    //toggle report chat member 
    document.getElementById("reportMember").addEventListener("click", function() {
		var img = document.getElementById("report-img");
		if (img.style.display !== "none") {
			img.style.display = "none";
		} else {
			img.style.display = "inline";
		}
    });
});