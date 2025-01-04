from django.shortcuts import render

from .models import Employee, ChatChatRecipients, Message, ChatMessage, EmployeeBlock, Chat, ChatDateTime
from .models import Group, GroupAdmin, GroupChat, Invitation, InvitationAdmin, InvitationChat
from .models import InvitationRecipients, MessageReceiver, MessageSender, Notification 
from .models import NotificationChat, Report, ReportedEmployee, ReportingEmployee
from django.db.models import F, Max, Count
from .utils import get_chat_members, get_chat_messages, add_chat_message, mute_a_chat, unmute_a_chat
from .utils import block_an_employee, unblock_an_employee, report_employee_without_message, get_blocked_status
from .utils import delete_a_message, send_an_invitation, group_status, leave_this_individual_chat
from .utils import add_a_group_admin, report_employee_with_message, leave_this_group, get_muted_status
from rest_framework.response import Response
from rest_framework.decorators import api_view
from .serializers import ChatSerializer, InvitationSerializer
from rest_framework.renderers import TemplateHTMLRenderer
from django.middleware.csrf import get_token
from django.http import JsonResponse
from django.http import QueryDict
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth import authenticate, login
from django.contrib import messages
from django.shortcuts import redirect
from django.urls import reverse
from django.shortcuts import render, get_object_or_404
from django.db import transaction, models
from django.utils import timezone
from datetime import datetime
import json

#for login page
# For the frontend
def frontend_login_view(request):
    if request.method == 'POST':
        email = request.POST['email']
        password = request.POST['password']
        try:
            user = Employee.objects.get(employee_email=email, employee_password=password)
            request.session['employee_id'] = user.pk
            return redirect('frontend-chat-list')
        except Employee.DoesNotExist:
            messages.error(request, 'Invalid email or password.')
            return render(request, 'login.html')
    return render(request, 'login.html')

@api_view(['GET'])
def get_employee_list(request):
    current_employee_id = request.session.get('employee_id')
    if not current_employee_id:
        return Response({'error': 'User is not authenticated.'}, status=401)

    try:
        # Dont include the users logged ins credentials
        employee_list = Employee.objects.exclude(pk=current_employee_id).values('employee_first_name', 'employee_email')
        return Response({'employee_list': list(employee_list)})
    except Exception as e:
        return Response({'error': str(e)}, status=500)


# For the frontend
def frontend_chat_list_view(request):
    # HTML template that includes the JavaScript
    # making AJAX calls to the API endpoint
    return render(request, 'chat_list.html')

# For the backend to get the list of chats
@api_view(['GET'])
def chat_list_view(request):
    current_employee_id = request.session.get('employee_id')
    user_chats = ChatChatRecipients.objects.filter(chat_recipients_id=current_employee_id)

    chats = []
    for chat_recipient in user_chats:
        chat = chat_recipient.chat_id
        serializer = ChatSerializer(chat)
        chat_data = serializer.data
        chat_data['chat_muted'] = chat_recipient.chat_muted
        chat_data['chat_favourited'] = chat_recipient.chat_favourited
        chat_data['is_read'] = chat_recipient.is_read  

        # Check if the chat is a group chat by querying the "group-chat" table
        is_group_chat = GroupChat.objects.filter(chat_id=chat.chat_id).exists()
        chat_data['is_group_chat'] = is_group_chat

        # Get the latest message for the chat
        latest_message = Message.objects.filter(chatmessage__chat_id=chat.chat_id).annotate(
            sender_id=F('messagesender__sender_id'),
            latest_message_time=Max('message_time')
        ).order_by('-message_date', '-message_time').first()

        if latest_message:
            sender_id = latest_message.sender_id
            sender_name = Employee.objects.get(employee_id=sender_id).employee_first_name
            message_text = latest_message.message_text
            message_date = latest_message.message_date
            message_time = latest_message.message_time
            
            chat_data['latest_message'] = {
                'sender': sender_name,
                'message_text': message_text,
                'message_date': message_date,
                'message_time': message_time
            }
        else:
            # Fallback to creation time if no latest message
            if is_group_chat:
                group_info = Group.objects.filter(groupchat__chat_id=chat.chat_id).first()
                if group_info:
                    message_date = group_info.group_created_date
                    message_time = group_info.group_created_time
                else:
                    message_date = None
                    message_time = None
            else:
                chat_creation_info = ChatDateTime.objects.filter(chat=chat).first()
                if chat_creation_info:
                    message_date = chat_creation_info.date_created
                    message_time = chat_creation_info.time_created
                else:
                    message_date = None
                    message_time = None

            chat_data['latest_message'] = {
                'sender': 'System',
                'message_text': 'No messages yet',
                'message_date': message_date,
                'message_time': message_time
            }

        chats.append(chat_data)
    return JsonResponse({'chats': chats}, safe=False)

# Backend for retrieving invitations
@api_view(['GET'])
def invitation_list_view(request):
    current_employee_id = request.session.get('employee_id')
    user_invitations = Invitation.objects.filter(invitationrecipients__recipient_id=current_employee_id)

    invitations = []
    invitation_count = user_invitations.count()  # Get the count of invitations

    for invitation in user_invitations:
        serializer = InvitationSerializer(invitation)
        invitations.append(serializer.data)

    return Response({'invitations': invitations, 'invitation_count': invitation_count})  # Include count in the response

@api_view(['POST'])
def deny_invitation_view(request, invitation_id):
	try:
		
		current_employee_id = request.session.get('employee_id')
		
		# Delete the InvitationRecipients record
		InvitationRecipients.objects.filter(invitation_id=invitation_id, recipient_id=current_employee_id).delete()
		
		return Response({'message': 'Invitation denied successfully'})
	except Exception as e:
		return Response({'error': str(e)}, status=500)

@api_view(['POST'])
def accept_invitation_view(request, invitation_id):
    # I'm using exception handling for testing purposes
    try:
        current_employee_id = request.session.get('employee_id')

        # Fetch the corresponding Chat ID for the given Invitation ID
        invitation_chat = InvitationChat.objects.filter(invitation_id=invitation_id).first()
        if not invitation_chat:
            return Response({'error': 'No corresponding chat found.'}, status=404)

        # Check if already a recipient (avoid duplicate entry)
        if ChatChatRecipients.objects.filter(chat_id=invitation_chat.chat_id, chat_recipients_id=current_employee_id).exists():
            return Response({'error': 'Already a chat recipient.'}, status=409)

        # Fetch the Employee instance
        current_employee = Employee.objects.get(employee_id=current_employee_id)

        # Create a new ChatChatRecipient record if not exists
        ChatChatRecipients.objects.create(
            chat_id=invitation_chat.chat_id,
            chat_recipients_id=current_employee,
            chat_muted=False,
            chat_favourited=False,
            is_read = False
        )

        # Delete the invitation recipient record
        deleted_count, _ = InvitationRecipients.objects.filter(invitation_id=invitation_id, recipient_id=current_employee_id).delete()
        if deleted_count == 0:
            return Response({'warning': 'No invitation record was deleted, it might not have existed.'})

        return Response({'message': 'Invitation accepted and recipient added successfully'})
    except Exception as e:
        return Response({'error': str(e)}, status=500)
        
@api_view(['POST'])
def toggle_favourite_view(request, chat_id):
    employee_id = request.session.get('employee_id')
    if not employee_id:
        return Response({'error': 'Authentication required'}, status=401)

    # Use filter to ensure no objects are created if they don't exist
    affected_rows = ChatChatRecipients.objects.filter(
        chat_id=chat_id, 
        chat_recipients_id=employee_id
    ).update(chat_favourited=models.F('chat_favourited').bitxor(True))

    if affected_rows == 0:
        # If no rows are affected, the chat recipient does not exist
        return Response({'error': 'Chat recipient not found'}, status=404)

    # If the update was successful, get new favourited status
    chat_recipient = ChatChatRecipients.objects.get(chat_id=chat_id, chat_recipients_id=employee_id)
    return Response({'message': 'Chat favourited status toggled successfully', 'favourited': chat_recipient.chat_favourited})

# Toggle the mute status of each chat
@api_view(['POST'])
def toggle_mute_view(request, chat_id):
    employee_id = request.session.get('employee_id')
    if not employee_id:
        return Response({'error': 'Authentication required'}, status=401)

    affected_rows = ChatChatRecipients.objects.filter(
        chat_id=chat_id, 
        chat_recipients_id=employee_id
    ).update(chat_muted=models.F('chat_muted').bitxor(True))

    if affected_rows == 0:
        # If no rows are affected, the chat recipient does not exist
        return Response({'error': 'Chat recipient not found'}, status=404)

    # If the update was successful, get new muted status
    chat_recipient = ChatChatRecipients.objects.get(chat_id=chat_id, chat_recipients_id=employee_id)
    return Response({'message': 'Chat muted status toggled successfully', 'muted': chat_recipient.chat_muted})

# Backend for allowing creation of single chat
@api_view(['POST'])
def create_chat(request):
    if request.method == 'POST':
        invited_email = request.data.get('invited_email')  # Use request.data.get() for JSON data

        # Validate email
        try:
            invited = Employee.objects.get(employee_email=invited_email)
        except Employee.DoesNotExist:
            return JsonResponse({'error': 'The invited email is invalid.'}, status=400)

        # Get the creator's ID from the session
        current_employee_id = request.session.get('employee_id')
        if not current_employee_id:
            return JsonResponse({'error': 'User is not authenticated.'}, status=401)

        # Check if the invited user is the same as the creator
        if current_employee_id == invited.pk:
            return JsonResponse({'error': 'Cannot create a chat with yourself.'}, status=400)

        # Fetch the authenticated user
        try:
            creator = Employee.objects.get(pk=current_employee_id)
        except Employee.DoesNotExist:
            return JsonResponse({'error': 'Authenticated user not found.'}, status=404)

        # Format creator's and invited user's names with '&' between them
        creator_name = creator.employee_first_name
        invited_name = invited.employee_first_name

        # Create a new chat with a descriptive name
        chat_name = f"{creator_name} & {invited_name}"
        new_chat = Chat.objects.create(chat_name=chat_name)

        # Add recipients
        ChatChatRecipients.objects.create(chat_id=new_chat, chat_recipients_id=creator, chat_muted=False, chat_favourited=False, is_read=True)
        ChatChatRecipients.objects.create(chat_id=new_chat, chat_recipients_id=invited, chat_muted=False, chat_favourited=False, is_read=False)

        # Record the date and time the chat was created
        date_created = datetime.now().date()
        time_created = datetime.now().time().strftime('%H:%M:%S')
        ChatDateTime.objects.create(chat=new_chat, date_created=date_created, time_created=time_created)

        return JsonResponse({'success': 'Chat created successfully.', 'chat_name': chat_name}, status=201)

    return JsonResponse({'error': 'Invalid request method.'}, status=405)

# Backend for deleting single chat
@api_view(['DELETE'])
def delete_chat(request, chat_id):
    if request.method == 'DELETE':
        try:
            with transaction.atomic():
                # Fetch the chat to be deleted
                chat = Chat.objects.get(chat_id=chat_id)
                
                # Fetch and print all recipients associated with this chat
                recipients = ChatChatRecipients.objects.filter(chat_id=chat_id)
                print("Deleting chat with ID:", chat_id)
                print("Associated recipients:", [(r.chat_recipients_id.employee_id, r.chat_recipients_id.employee_first_name) for r in recipients])
                
                # Delete related ChatMessage entries
                chat_messages = ChatMessage.objects.filter(chat_id=chat_id)
                messages_ids = chat_messages.values_list('message_id', flat=True)
                chat_messages.delete()
                
                # Delete messages associated with these ChatMessage entries
                Message.objects.filter(message_id__in=messages_ids).delete()
                
                # Delete the chat and related recipients
                recipients.delete()
                chat.delete()

                return Response({'message': 'Chat and all related data deleted successfully.'}, status=200)

        except Chat.DoesNotExist:
            return Response({'error': 'Chat not found.'}, status=404)
        except Exception as e:
            print("Error during deletion:", str(e))
            return Response({'error': str(e)}, status=500)
    
    else:
        return Response({'error': 'Invalid request method.'}, status=405)
    
# Backend for leaving group chats
@api_view(['POST'])
def leave_chat(request, chat_id):
    if request.method == 'POST':
        current_employee_id = request.session.get('employee_id')

        try:
            with transaction.atomic():
                # Fetch the chat recipient entry to be removed
                chat_recipient = ChatChatRecipients.objects.get(chat_id=chat_id, chat_recipients_id=current_employee_id)
                
                # Check remaining recipients in the chat
                remaining_recipients = ChatChatRecipients.objects.filter(chat_id=chat_id).exclude(chat_recipients_id=current_employee_id)

                # Remove the current user from the chat recipients
                chat_recipient.delete()

                if not remaining_recipients.exists():
                    # If no other recipients, delete the chat and all related data
                    chat = Chat.objects.get(chat_id=chat_id)

                    # Delete related ChatMessage entries
                    chat_messages = ChatMessage.objects.filter(chat_id=chat_id)
                    messages_ids = chat_messages.values_list('message_id', flat=True)
                    chat_messages.delete()

                    # Delete messages associated with these ChatMessage entries
                    Message.objects.filter(message_id__in=messages_ids).delete()

                    # Delete ChatDateTime entry
                    ChatDateTime.objects.filter(chat=chat).delete()

                    # Also, consider removing entries from GroupChat if it's a group chat
                    GroupChat.objects.filter(chat_id=chat_id).delete()

                    # Finally delete the chat
                    chat.delete()

                    return JsonResponse({'message': 'Chat and all related data deleted successfully as you were the last member.'}, status=200)
                else:
                    return JsonResponse({'message': 'You have left the chat.'}, status=200)

        except ChatChatRecipients.DoesNotExist:
            return JsonResponse({'error': 'Chat recipient not found.'}, status=404)
        except Chat.DoesNotExist:
            return JsonResponse({'error': 'Chat not found.'}, status=404)
        except Exception as e:
            return JsonResponse({'error': str(e)}, status=500)
    else:
        return JsonResponse({'error': 'Invalid request method.'}, status=405)
    
# Backend for creating group chat
@api_view(['POST'])
def create_group_chat(request):
    if request.method != 'POST':
        return Response({'error': 'Invalid request method.'}, status=405)

    group_name = request.data.get('group_name')
    group_description = request.data.get('group_description')
    invitees = request.data.get('invitees', [])
    creator_id = request.session.get('employee_id')

    if not creator_id:
        return Response({'error': 'User is not authenticated.'}, status=401)
    if not group_name:
        return Response({'error': 'Group name is required.'}, status=400)

    try:
        creator = Employee.objects.get(employee_id=creator_id)
    except Employee.DoesNotExist:
        return Response({'error': 'Creator not found.'}, status=404)

    if not invitees:
        return Response({'error': 'At least one invitee is required.'}, status=400)

    try:
        with transaction.atomic():
            # Create the chat and group
            new_chat = Chat.objects.create(chat_name=group_name)
            date_created = datetime.now().date()
            time_created = datetime.now().time().strftime('%H:%M:%S')
            new_group = Group.objects.create(
                group_name=group_name,
                group_description=group_description,
                group_created_date=date_created,
                group_created_time=time_created
            )

            # Link the chat with the group and set the creator as group admin
            GroupChat.objects.create(group_id=new_group, chat_id=new_chat)
            new_group_admin = GroupAdmin.objects.create(group_id=new_group, admin_id=creator)

            # Add the creator to the chat recipients
            ChatChatRecipients.objects.create(
                chat_id=new_chat, chat_recipients_id=creator, chat_muted=False, chat_favourited=False, is_read=True
            )

            # Create a single invitation for all invitees
            invitation = Invitation.objects.create(invitation_text=f"{creator.employee_first_name} has invited you to the {group_name} Groupchat")
            InvitationAdmin.objects.create(invitation_id=invitation, admin_id=new_group_admin)

            # Link the invitation to the chat
            InvitationChat.objects.create(invitation_id=invitation, chat_id=new_chat)

            # Add all valid invitees as recipients
            for email in invitees:
                try:
                    invitee = Employee.objects.get(employee_email=email)
                    InvitationRecipients.objects.create(invitation_id=invitation, recipient_id=invitee)
                except Employee.DoesNotExist:
                    continue  # Skip if no such employee exists

            return Response({
                'success': 'Group chat created successfully. Invitations sent.',
                'chat_id': new_chat.chat_id,
                'group_id': new_group.group_id
            }, status=201)

    except Exception as e:
        return Response({'error': str(e)}, status=500)

#for chats
#for the frontend
def frontend_chats_view(request):
    return render(request, 'chats.html')

#for the backend
@api_view(['GET', 'POST'])
def chats(request, chat_id):
    current_employee_id = request.session.get('employee_id')

    if request.method == 'GET':
        ChatChatRecipients.objects.filter(
            chat_id=chat_id, 
            chat_recipients_id=current_employee_id
        ).update(is_read=True)

    print("chat id: ", chat_id)
    print("current employee_id: ", current_employee_id)

    chat_members = get_chat_members(chat_id)
    chat_messages = get_chat_messages(chat_id, current_employee_id)
    is_group = group_status(chat_id)

    data = {
        'members': chat_members if chat_members else [],
        'messages': chat_messages if chat_messages else [],
        'is_group': is_group,
        'current_employee_id': current_employee_id,
        'chat_id': chat_id
    }

    return JsonResponse(data, content_type='application/json')

@api_view(['POST'])
def muted_status(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')

        is_muted = get_muted_status(chat_id, current_employee_id)

        print(is_muted)
        data = {}

        data['is_muted'] = is_muted

        return JsonResponse(data, content_type='application/json')
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)

@api_view(['POST'])
def blocked_status(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)

    if request.method == 'POST':
        chosen_employee_id = request.POST.get('chosen_employee_id')

        is_blocked = get_blocked_status(current_employee_id, chosen_employee_id)

        print(is_blocked)
        data = {}
        data['is_blocked'] = is_blocked

        return JsonResponse(data, content_type='application/json')
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)

@api_view(['POST'])
def create_chat_message(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')
        message_text = request.POST.get('message_text')
        
        if not message_text.strip():
            return JsonResponse({'error': 'No message entered'}, status=422)
        
        add_chat_message(chat_id, current_employee_id, message_text)

             
        ChatChatRecipients.objects.filter(
            chat_id=chat_id
        ).exclude(
            chat_recipients_id=current_employee_id
        ).update(
            is_read=False
        )
        
        return JsonResponse({'message': 'Chat message created successfully'}, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)

@api_view(['POST'])
def block_employee(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    if request.method == 'POST':
        blocked_employee_id = request.POST.get('blocked_employee_id')
        
        block_an_employee(current_employee_id, blocked_employee_id)
        
        return JsonResponse({'message': 'Employee blocked successfully'}, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)
        
@api_view(['POST'])
def unblock_employee(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    if request.method == 'POST':
        unblocked_employee_id = request.POST.get('unblocked_employee_id')
        
        unblock_an_employee(current_employee_id, unblocked_employee_id)
        
        return JsonResponse({'message': 'Employee unblocked successfully'}, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)       
        
@api_view(['POST'])
def report_employee_no_message(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    if request.method == 'POST':
        reported_employee_id = request.POST.get('reported_employee_id')
        employee_report_reason = request.POST.get('report_reason')
        
        if not employee_report_reason.strip():
            return JsonResponse({'error': 'No report reason entered'}, status=422)
        
        try:
        	report_employee_without_message(employee_report_reason, reported_employee_id, current_employee_id)
        	return JsonResponse({'message': 'Employee reported successfully'}, status=200)
        except ValueError as e:
        	return JsonResponse({'error': 'No report reason entered'}, status=422)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)       

@api_view(['POST'])
def mute_chat(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    
    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')
        mute_a_chat(chat_id, current_employee_id)
        
        return JsonResponse({'message': 'Chat muted successfully'}, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)

@api_view(['POST'])
def unmute_chat(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    
    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')
        unmute_a_chat(chat_id, current_employee_id)
        return JsonResponse({'message': 'Chat unmuted successfully'}, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405) 
        
@api_view(['POST'])
def add_group_admin(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')
        selected_employee_id = request.POST.get('selected_employee_id')
        
        add_a_group_admin(chat_id, current_employee_id, selected_employee_id)
        
        return JsonResponse({'message': 'New admin created successfully'}, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)
 
@api_view(['POST'])
def delete_message(request):
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')
        message_id = request.POST.get('message_id')
        
        delete_a_message(message_id)
        
        return JsonResponse({'message': 'Message deleted successfully'}, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)

@api_view(['POST'])
def report_employee_message(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')
        selected_message_id = request.POST.get('selected_message_id')
        
        report_employee_with_message(selected_message_id, current_employee_id)
        
        return JsonResponse({'message': 'Employee reported successfully'}, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)

@api_view(['POST'])
def send_invitation(request):
    current_employee_id = request.session.get('employee_id')

    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)

    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')
        recipient_email = request.POST.get('recipient_email')

        if not recipient_email.strip():
            return JsonResponse({'error': 'No email entered'}, status=422)
        
        try:
            send_an_invitation(current_employee_id, chat_id, recipient_email)
            return JsonResponse({'message': 'Invitation sent successfully'}, status=200)
        except ValueError as e:
            return JsonResponse({'error': 'Incorrect email entered'}, status=422)
            
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)


@api_view(['POST'])
def leave_group_chat(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    
    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')
        
        leave_this_group(chat_id, current_employee_id)
        
        redirect_url = reverse('frontend-chat-list')
        response_data = {
            'message': 'Left chat successfully',
            'redirect_url': redirect_url
        }
        
        return JsonResponse(response_data, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)


@api_view(['POST'])
def leave_individual_chat(request):
    current_employee_id = request.session.get('employee_id')
    csrf_token = request.headers.get('X-CSRFToken')
    print('CSRF Token:', csrf_token)
    
    if request.method == 'POST':
        chat_id = request.POST.get('chat_id')
        
        leave_this_individual_chat(chat_id, current_employee_id)
        
        redirect_url = reverse('frontend-chat-list')
        response_data = {
        	'message': 'Left chat successfully',
            'redirect_url': redirect_url
        }
        
        return JsonResponse(response_data, status=200)
    else:
        return JsonResponse({'error': 'Method not allowed'}, status=405)      