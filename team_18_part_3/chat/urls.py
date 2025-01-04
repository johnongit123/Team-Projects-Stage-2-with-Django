from django.contrib import admin
from django.urls import path, include
from django.conf.urls.static import static
from django.conf import settings  # Import settings
from . import views
from .views import chat_list_view

urlpatterns = [
	#api backend for chats
	path('api/mainchats/<int:chat_id>/', views.chats, name='chats'),
	path('create_chat/', views.create_chat_message, name='create_chat'),
	path('block_employee/', views.block_employee, name='block_employee'),
	path('unblock_employee/', views.unblock_employee, name='unblock_employee'),
	path('report_employee_no_message/', views.report_employee_no_message, name='report_employee_no_message'),
	path('mute_chat/', views.mute_chat, name='mute_chat'),
	path('unmute_chat/', views.unmute_chat, name='unmute_chat'),
	path('add_group_admin/', views.add_group_admin, name='add_group_admin'),
	path('delete_message/', views.delete_message, name='delete_message'),
	path('report_employee_message/', views.report_employee_message, name='report_employee_message'),
	path('send_invitation/', views.send_invitation, name='send_invitation'),
	path('muted_status/', views.muted_status, name='muted_status'),
	path('blocked_status/', views.blocked_status, name='blocked_status'),
	#not done these yet
	path('leave_group_chat/', views.leave_group_chat, name='leave_group_chat'),
	path('leave_individual_chat/', views.leave_individual_chat, name='leave_individual_chat'),
	#frontend view for chats page
    path('chats/', views.frontend_chats_view, name='frontend-chats'), #works
    # API Backend for chat list
    path('api/chats/', views.chat_list_view, name='chat-list'),  # Route API requests to the chat_list_view function
    # API Backend for invitations
    path('api/invitations/', views.invitation_list_view, name='invitation-list'),  # Route API requests to the invitation_list_view function
    # API Backend for denying invitations
    path('api/deny-invitation/<int:invitation_id>/', views.deny_invitation_view, name='deny-invitation'),
    # API Backend for accepting invitations
    path('api/accept-invitation/<int:invitation_id>/', views.accept_invitation_view, name='accept-invitation'),
    # API Backend for toggling chat favorited status
    path('api/toggle-favourite/<int:chat_id>/', views.toggle_favourite_view, name='toggle-favourite'),
    # API Backend for toggling mute status on chats
    path('api/toggle-mute/<int:chat_id>/', views.toggle_mute_view, name='toggle_mute'),
    # API Backend for creating a single chat 
    path('api/invite_single_chat/', views.create_chat, name='invite_single_chat'),
    # API Backend for deleting chat
    path('api/delete-chat/<int:chat_id>/', views.delete_chat, name='delete-chat'),
    # API Backend for leaving group chat
    path('api/leave-chat/<int:chat_id>/', views.leave_chat, name='leave-chat'),
    # Frontend view for chat list page
    path('chat-list/', views.frontend_chat_list_view, name='frontend-chat-list'),
    # API Backend to get list of employee first names and emails
    path('api/get_employee_list/', views.get_employee_list, name='get_employee_list'),
    # API Backend to create a group chat
    path('api/create_group_chat/', views.create_group_chat, name='create_group_chat'),
    #frontent view for login page
    path('login/', views.frontend_login_view, name='frontend-login-page')
] + static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
