from django.db import models

# Create your models here.
class Chat(models.Model):
     chat_id = models.AutoField(primary_key=True, db_column='Chat ID')
     chat_name = models.TextField(db_column='Chat Name')

     class Meta:
         db_table = 'chat table'
	
class Employee(models.Model):
    employee_id = models.AutoField(primary_key=True, db_column='Employee ID')
    employee_first_name = models.CharField(max_length=255, db_column='Employee First Name')
    employee_surname = models.CharField(max_length=255, db_column='Employee Surname')
    employee_email = models.EmailField(unique=True, db_column='Employee Email')
    employee_password = models.CharField(max_length=255, db_column='Employee Password')
    
    class Meta:
    	db_table = 'employee table'

class ChatChatRecipients(models.Model):
    chat_chat_recipients_id = models.AutoField(primary_key=True, db_column='Chat-Chat Recipients ID')
    chat_id = models.ForeignKey(Chat, on_delete=models.CASCADE, db_column='Chat ID')
    chat_recipients_id = models.ForeignKey(Employee, on_delete=models.CASCADE, db_column='Chat Recipients ID')
    chat_muted = models.BooleanField(default=True, db_column='Chat Muted')
    chat_favourited = models.BooleanField(default=True, db_column='Chat Favourited')
    is_read = models.BooleanField(default=True, db_column='Is Read')
    
    class Meta:
        db_table = 'chat-chat recipients table'
        
class Message(models.Model):
	message_id = models.AutoField(primary_key = True, db_column='Message ID')
	message_text = models.TextField(db_column='Message Text')
	message_media = models.TextField(db_column='Message Media')
	message_date = models.DateField(db_column='Message Date')
	message_time = models.TimeField(db_column='Message Time')
	message_drafted = models.BooleanField(default = True, db_column='Message Drafted')
	
	class Meta:
		db_table = 'message table'

class ChatMessage(models.Model):
	chat_id = models.ForeignKey(Chat, on_delete = models.CASCADE, db_column='Chat ID')
	message_id = models.ForeignKey(Message, on_delete = models.CASCADE, db_column='Message ID')
	
	class Meta:
		db_table = 'chat-message table'

class ChatDateTime(models.Model):
    chat = models.OneToOneField(Chat, on_delete=models.CASCADE, db_column='Chat ID', primary_key=True)
    date_created = models.DateField(db_column='Date Created')
    time_created = models.TimeField(db_column='Time Created')

    class Meta:
        db_table = 'chat-date/time table'
	
class EmployeeBlock(models.Model):
	blocking_employee_id = models.ForeignKey(Employee, related_name = 'blocking_employee', on_delete = models.CASCADE, db_column='Blocking Employee ID', primary_key=True)
	blocked_employee_id = models.ForeignKey(Employee, related_name = 'blocked_employee', on_delete = models.CASCADE, db_column='Blocked Employee ID')
	
	class Meta:
		db_table = 'employee-block table'
	
class Group(models.Model):
	group_id = models.AutoField(primary_key = True, db_column='Group ID')
	group_name = models.TextField(db_column='Group Name')
	group_description = models.TextField(db_column='Group Description')
	group_created_date = models.DateField(db_column='Group Created Date')
	group_created_time = models.TimeField(db_column='Group Created Time')
	
	class Meta:
		db_table = 'group table'
	
class GroupAdmin(models.Model):
	group_id = models.ForeignKey(Group, on_delete = models.CASCADE, db_column='Group ID')
	admin_id = models.ForeignKey(Employee, on_delete = models.CASCADE, db_column='Admin ID', primary_key=True)
	
	class Meta:
		db_table = 'group-admin table'
	
class GroupChat(models.Model):
	group_id = models.ForeignKey(Group, on_delete = models.CASCADE, db_column='Group ID', primary_key=True)
	chat_id = models.ForeignKey(Chat, on_delete = models.CASCADE, db_column='Chat ID')
	
	class Meta:
		db_table = 'group-chat table'

class Invitation(models.Model):
	invitation_id = models.AutoField(primary_key = True, db_column='Invitation ID')
	invitation_text = models.TextField(db_column='Invitation Text')
	
	class Meta:
		db_table = 'invitation table'
	
class InvitationAdmin(models.Model):
	invitation_id = models.ForeignKey(Invitation, on_delete = models.CASCADE, db_column='Invitation ID')
	admin_id = models.ForeignKey(GroupAdmin, on_delete = models.CASCADE, db_column='Admin ID')
	
	class Meta:
		db_table = 'invitation-admin table'
	
class InvitationRecipients(models.Model):
	invitation_id = models.ForeignKey(Invitation, on_delete = models.CASCADE, db_column='Invitation ID')
	recipient_id = models.ForeignKey(Employee, on_delete = models.CASCADE, db_column='Recipient ID')
	
	class Meta:
		db_table = 'invitation-recipients table'

class InvitationChat(models.Model):
	invitation_id = models.ForeignKey(Invitation, on_delete=models.CASCADE, db_column='Invitation ID', primary_key=True)
	chat_id = models.ForeignKey(Chat, on_delete=models.CASCADE, db_column='Chat ID')
	
	class Meta:
		db_table = 'invitation-chat table'
	
class MessageReceiver(models.Model):
	message_id = models.ForeignKey(Message, on_delete = models.CASCADE, db_column='Message ID', primary_key=True)
	receiver_id = models.IntegerField(db_column='Receiver ID')
	
	class Meta:
		db_table = 'message-receiver table'
	
class MessageSender(models.Model):
	message_id = models.ForeignKey(Message, on_delete = models.CASCADE, db_column='Message ID', primary_key=True)
	sender_id = models.IntegerField(db_column='Sender ID')
	
	class Meta:
		db_table = 'message-sender table'
	
class Notification(models.Model):
	notification_id = models.BigAutoField(primary_key = True, db_column='Notification ID')
	notification_text = models.TextField(db_column='Notification Text')
	notification_read = models.BooleanField(default = False, db_column='Notification Read')
	
	class Meta:
		db_table = 'notification table'
	
class NotificationChat(models.Model):
	notification_id = models.ForeignKey(Notification, on_delete = models.CASCADE, db_column='Notification ID')
	chat_id = models.IntegerField(db_column='Chat ID')
	
	class Meta:
		db_table = 'notification-chat table'

class Report(models.Model):
	report_id = models.BigAutoField(primary_key = True, db_column='Report ID')
	report_reason = models.TextField(db_column='Report Reason')
	
	class Meta:
		db_table = 'report table'

class ReportedEmployee(models.Model):
	report_id = models.ForeignKey(Report, on_delete = models.CASCADE, db_column='Report ID')
	reported_employee_id = models.ForeignKey(Employee, on_delete = models.CASCADE, db_column='Reported Employee ID')
	
	class Meta:
		db_table = 'reported-employee table'
	
class ReportingEmployee(models.Model):
	report_id = models.ForeignKey(Report, on_delete = models.CASCADE, db_column='Report ID')
	reporting_employee = models.ForeignKey(Employee, on_delete = models.CASCADE, db_column='Reporting Employee ID')
	
	class Meta:
		db_table = 'reporting-employee table'