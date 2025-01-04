from .models import Employee, ChatChatRecipients, Message, ChatMessage, EmployeeBlock, Chat
from .models import Group, GroupAdmin, GroupChat, Invitation, InvitationAdmin, InvitationChat
from .models import InvitationRecipients, MessageReceiver, MessageSender, Notification 
from .models import NotificationChat, Report, ReportedEmployee, ReportingEmployee
from django.db.models import Case, Value, When, IntegerField, F, Q
from django.core.exceptions import ObjectDoesNotExist
from datetime import datetime
	
def get_chat_members(chat_id):
    # Get the member ids of members of the specified chat
    member_ids = ChatChatRecipients.objects.filter(chat_id=chat_id).values_list('chat_recipients_id', flat=True)
    try:
        # Get the group id related to the specified chat
        group_ids = GroupChat.objects.filter(chat_id=chat_id).values_list('group_id', flat=True)
        if group_ids:  # Are there values in group ids
            if isinstance(group_ids, int):  # If group ids is a single integer
                group_ids = [group_ids]  # Convert it to a list if it's a single integer
            # Get the admin ids for the chat
            admin_ids = GroupAdmin.objects.filter(group_id__in=group_ids).values_list('admin_id', flat=True)
        else:
            admin_ids = []
    except GroupChat.DoesNotExist:
        group_ids = []
        admin_ids = []
    
    # Get the member details
    member_details = []
    for member_id in member_ids:
        member = Employee.objects.get(employee_id=member_id)
        member_data = {
            'id': member_id,
            'first_name': member.employee_first_name,
            'surname': member.employee_surname,
            'is_admin': member_id in admin_ids
        }
        member_details.append(member_data)
        
    return member_details

def get_muted_status(chat_id, current_employee_id):
	chat_status = ChatChatRecipients.objects.get(chat_id=chat_id, chat_recipients_id=current_employee_id)
	status = chat_status.chat_muted
	if status == True:
		is_muted = 1
	else:
		is_muted = 0
		
	print(is_muted)
	
	muted_status = {
		'is_muted': is_muted
	}
	
	return muted_status

def get_blocked_status(current_employee_id, chosen_employee_id):
    try:
        block_status = EmployeeBlock.objects.get(blocking_employee_id=current_employee_id, blocked_employee_id=chosen_employee_id)
        is_blocked = 1
    except ObjectDoesNotExist:
        is_blocked = 0
    
    print(is_blocked)
    
    return is_blocked
	
def get_chat_messages(chat_id, current_employee_id):
    message_ids = ChatMessage.objects.filter(chat_id=chat_id).values_list('message_id', flat=True)
    messages = Message.objects.filter(message_id__in=message_ids).values_list('message_id', 'message_text', 'message_date', 'message_time')
    message_sender_ids = MessageSender.objects.filter(message_id__in=message_ids).values_list('sender_id', flat=True)

    message_details = {}
    
    for message_id, message_text, message_date, message_time in messages:
        try:
            sender_index = list(message_ids).index(message_id)
            if sender_index < len(message_sender_ids):
                sender_id = str(message_sender_ids[sender_index])
                message_sender = Employee.objects.filter(employee_id=sender_id).values_list('employee_first_name', 'employee_surname')
                sender_first_name, sender_surname = message_sender[0] if message_sender else ('Anonymous', 'User')
            else:
                raise IndexError("Sender index out of range")
        except ValueError:
            # Handle the case when message_id is not found in message_ids
            sender_id = None
            sender_first_name = 'Anonymous'
            sender_surname = 'User'
        except IndexError:
            # Handle the case when sender_index is out of range
            sender_id = None
            sender_first_name = 'Anonymous'
            sender_surname = 'User'
        
        is_sender = str(current_employee_id) == sender_id
        
        message_details[message_id] = {
            'message_id': message_id,
            'message_text': message_text,
            'message_date': message_date,
            'message_time': message_time,
            'sender_id': sender_id,
            'sender_first_name': sender_first_name,
            'sender_surname': sender_surname,
            'is_sender': is_sender
        }
        
    return message_details
    
def group_status(chat_id):
	try:
		group_chat = GroupChat.objects.get(chat_id=chat_id)
		return True
	except GroupChat.DoesNotExist:
		return False
	
def add_chat_message(chat_id, current_employee_id, message_text):
    print("Chat ID:", chat_id)
    print("Current Employee ID:", current_employee_id)
    print("Message Text:", message_text)
    
    # Convert chat_id to integer if needed
    chat_id = int(chat_id)
    
    # Get current datetime for message date and time
    message_date = datetime.now().date()
    message_time = datetime.now().time().strftime('%H:%M:%S')

    chat_senders_instance = ChatChatRecipients.objects.get(chat_id=chat_id, chat_recipients_id=current_employee_id)
    
    # Create a new message
    message = Message.objects.create(
        message_text=message_text,
        message_media="",  # Assuming message_media is optional
        message_date=message_date,
        message_time=message_time,
        message_drafted=False
    )

    ChatChatRecipients.objects.filter(chat_id=chat_id).update(is_read=False)
    ChatChatRecipients.objects.filter(chat_id=chat_id, chat_recipients_id=current_employee_id).update(is_read=True)

    chat = Chat.objects.get(chat_id=chat_id)
    
    # Link the message to the chat
    ChatMessage.objects.create(chat_id=chat, message_id=message)
    
    # Link the message to the sender
    #sender_instance = ChatChatRecipients.objects.get(chat_id=chat_id, chat_recipients_id=current_employee_id)
    
    # Get the chat_recipients_id from the sender_instance
    #sender_recipient_id = sender_instance.chat_recipients_id
    
    # Create the MessageSender instance
    MessageSender.objects.create(message_id=message, sender_id=current_employee_id)
    
    # Get recipient ids excluding the sender
    recipients_ids = ChatChatRecipients.objects.filter(chat_id=chat_id).exclude(chat_recipients_id=current_employee_id).values_list('chat_recipients_id', flat=True)
    print(recipients_ids)
    
    # Link the message to the recipients
    for recipient_id in recipients_ids:
        print("recipient is", recipient_id)
        chat_recipients_instance = ChatChatRecipients.objects.get(chat_id=chat_id, chat_recipients_id=recipient_id)
        print("message", message)
        print("chat recipient", chat_recipients_instance.chat_recipients_id)
        MessageReceiver.objects.create(message_id=message, receiver_id=recipient_id)
        
    # Create notification
    employee_name = Employee.objects.get(employee_id=current_employee_id).employee_first_name
    current_employee = Employee.objects.get(employee_id=current_employee_id)
    chat_instance = ChatChatRecipients.objects.filter(chat_id=chat_id, chat_recipients_id=current_employee).values_list('chat_id', flat=True)
    notification_text = ""
    
    if len(message_text) > 41:
        notification_message_text = message_text[:41] + "..."
        notification_text = f"{employee_name} sent: {notification_message_text}"
    else:
        notification_text = f"{employee_name} sent: {message_text}"
    
    print(notification_text)
    
    # Create the notification
    notification = Notification.objects.create(notification_text=notification_text, notification_read=0)
    
    # Link the notification to the chat
    NotificationChat.objects.create(notification_id=notification, chat_id=chat_id)

#query to block employee
def block_an_employee(current_employee_id, employee_to_block_id):
    current_employee = Employee.objects.get(employee_id=current_employee_id)
    employee_to_block = Employee.objects.get(employee_id=employee_to_block_id)
    
    # adds a new record to the employee-block table
    EmployeeBlock.objects.create(blocking_employee_id=current_employee, blocked_employee_id=employee_to_block)
    
#query to unblock employee
def unblock_an_employee(current_employee_id, employee_to_unblock_id):
    #delete the record from the employee-block table
    EmployeeBlock.objects.filter(blocking_employee_id=current_employee_id, blocked_employee_id=employee_to_unblock_id).delete()

def report_employee_without_message(employee_report_reason, reported_employee_id, current_employee_id):
    # Adds a new entry in the report table with the reason for reporting
    report = Report.objects.create(report_reason=employee_report_reason)
    
    # Adds a new entry in the reported employee table - links the report with the reported employee
    reported_employee = Employee.objects.get(employee_id=reported_employee_id)
    ReportedEmployee.objects.create(report_id=report, reported_employee_id=reported_employee)
    
    # Adds a new entry in the reporting employee table - links the report with the reporting employee
    current_employee = Employee.objects.get(employee_id=current_employee_id)
    ReportingEmployee.objects.create(report_id=report, reporting_employee_id=current_employee.employee_id)

#query to mute a chat
def mute_a_chat(chat_id, current_employee_id):
	#gets the row with the chat id and employee id and updates the chat muted column
	ChatChatRecipients.objects.filter(chat_id=chat_id, chat_recipients_id=current_employee_id).update(chat_muted=1)

#query to unmute chat
def unmute_a_chat(chat_id, current_employee_id):
    #gets the row with the chat id and employee id and updates the chat muted column
    ChatChatRecipients.objects.filter(chat_id=chat_id, chat_recipients_id=current_employee_id).update(chat_muted=0)

def add_a_group_admin(chat_id, current_employee_id, selected_employee_id):
    group_chat = GroupChat.objects.get(chat_id=chat_id)
    current_group_id = group_chat.group_id
    print(current_group_id)
    # Checks the group admin table to see if there is a record with group id and the current employee id as the admin id
    is_admin_exists = GroupAdmin.objects.filter(group_id=current_group_id, admin_id=current_employee_id).exists()
	
    selected_employee = Employee.objects.get(employee_id=selected_employee_id)
    if is_admin_exists:
        # If current employee id is an admin, make the selected employee id an admin too
        GroupAdmin.objects.create(group_id=current_group_id, admin_id=selected_employee)

#query to delete message	
def delete_a_message(message_id):
	#deletes entries from the message-receiver table where the message id matches the specified id
    MessageReceiver.objects.filter(message_id=message_id).delete()
    #deletes entries from the chat-message table where the message id matches the specified id
    ChatMessage.objects.filter(message_id=message_id).delete()
    #deletes entries from the message-sender table where the message id matches the specified id
    MessageSender.objects.filter(message_id=message_id).delete()
    #deletes entries from the message table where the message id matches the specified id
    Message.objects.filter(message_id=message_id).delete()
    #delete notification from notifications table
    #Notification.objects.filter()
    #delete notification from notification chat table

#query to report an employee from a message
def report_employee_with_message(selected_message_id, current_employee_id):
	#gets the message text based on the message id
    message_text = Message.objects.filter(message_id=selected_message_id).values_list('message_text', flat=True).first()
    
    print(message_text);
    #adds an entry to the report table with the message text as the report reason
    report = Report.objects.create(report_reason=message_text)
    #gets the sender id from the message table for the specified message id
    sender_id = MessageSender.objects.filter(message_id=selected_message_id).values_list('sender_id', flat=True).first()
    
    reported_employee = Employee.objects.get(employee_id=sender_id)
    #adds an entry into the reported employee table with the report id and sender id
    ReportedEmployee.objects.create(report_id=report, reported_employee_id=reported_employee)
    
    current_employee = Employee.objects.get(employee_id=current_employee_id)
    #adds an entry into the reporting employee table with the report id and the current employee id
    ReportingEmployee.objects.create(report_id=report, reporting_employee_id=current_employee.employee_id)

#query to send an invitation for a group chat
def send_an_invitation(current_employee_id, chat_id, recipient_email):
    try:
        # Get the current employee
        current_employee = Employee.objects.get(employee_id=current_employee_id)
        
        # Get group id
        group_id = GroupChat.objects.filter(chat_id=chat_id).values_list('group_id', flat=True).first()
        group_name = Group.objects.get(group_id=group_id).group_name
        
        # Check if the current employee is an admin of the group
        admin_instance = GroupAdmin.objects.filter(admin_id=current_employee_id, group_id=group_id).first()
        
        # Check if email exists in the Employee table
        recipient = Employee.objects.get(employee_email=recipient_email)
        
        if admin_instance:
            invitation_text = f"{current_employee.employee_first_name} has invited you to the {group_name}"
            # Create an invitation object
            invitation = Invitation.objects.create(invitation_text=invitation_text)
            chat = Chat.objects.get(chat_id=chat_id)
            # Create an entry in the InvitationChat table
            InvitationChat.objects.create(invitation_id=invitation, chat_id=chat)
            # Create an entry in the InvitationAdmin table
            invitation_admin = InvitationAdmin.objects.create(invitation_id=invitation, admin_id=admin_instance)
            # Create an entry in the InvitationRecipients table
            InvitationRecipients.objects.create(invitation_id=invitation, recipient_id=recipient)
    except ObjectDoesNotExist:
        # Handle the case where the recipient email does not exist in the Employee table
        raise ValueError(f"Incorrect email entered: {recipient_email}")
	
#query to leave a group chat
#non-admin can leave chat
#admin can leave chat and deletes the chat if they are the only admin
def leave_this_group(chat_id, current_employee_id):
    current_employee = Employee.objects.get(employee_id=current_employee_id)
        
    # Get the group id from the chat id
    group_id = GroupChat.objects.filter(chat_id=chat_id).values_list('group_id', flat=True).first()
    
    # Check if the current employee is an admin of the group
    admin_instance = GroupAdmin.objects.filter(admin_id=current_employee_id, group_id=group_id).first()
    
    if admin_instance:
        admin_count = GroupAdmin.objects.filter(group_id=group_id).count()
        if admin_count == 1:
            print("only admin")
            # Get all members from chat
            member_ids = ChatChatRecipients.objects.filter(chat_id=chat_id).values_list('chat_recipients_id', flat=True)
		
            for member_id in member_ids:
                chat = Chat.objects.get(chat_id=chat_id)
                employee = Employee.objects.get(employee_id=member_id)
                # Delete the members from the chat chat recipients table
                ChatChatRecipients.objects.filter(chat_id=chat.chat_id, chat_recipients_id=employee.employee_id).delete()
                
            GroupAdmin.objects.filter(group_id=group_id, admin_id=current_employee_id).delete()
            Chat.objects.filter(chat_id=chat_id).delete()
        else:
            print("other admin")
            # If there are other admins, just remove the current employee
            ChatChatRecipients.objects.filter(chat_id=chat_id, chat_recipients_id=current_employee_id).delete()
            GroupAdmin.objects.filter(group_id=group_id, admin_id=current_employee_id).delete()
    else:
        print("not an admin")
        # If the current employee is not an admin, simply remove from chat recipients
        ChatChatRecipients.objects.filter(chat_id=chat_id, chat_recipients_id=current_employee_id).delete()
        

# Query to leave a one-on-one chat - deletes the chat
def leave_this_individual_chat(chat_id, current_employee_id):
    current_employee = Employee.objects.get(employee_id=current_employee_id)
    
    # Get all members from chat
    member_ids = ChatChatRecipients.objects.filter(chat_id=chat_id).values_list('chat_recipients_id', flat=True)

    for member_id in member_ids:
        chat = Chat.objects.get(chat_id=chat_id)
        employee = Employee.objects.get(employee_id=member_id)
        # Delete the members from the chat chat recipients table
        ChatChatRecipients.objects.filter(chat_id=chat.chat_id, chat_recipients_id=employee.employee_id).delete()

    Chat.objects.filter(chat_id=chat_id).delete()




















	
