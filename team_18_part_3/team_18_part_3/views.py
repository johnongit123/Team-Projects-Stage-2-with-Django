from django.shortcuts import render
from django.shortcuts import redirect
from django.contrib import messages
from chat.models import Employee

def login_page(request):
    if request.method == 'POST':
        email = request.POST['employee_email']
        password = request.POST['employee_password']
        print(email, password)
        try:
            user = Employee.objects.get(employee_email=email, employee_password=password)
            request.session['employee_id'] = user.pk
            return redirect('chat-list')
        except Employee.DoesNotExist:
            messages.error(request, 'Invalid email or password.')
            return render(request, 'login.html')
    return render(request, 'login.html')


def signup_page(request):
    return render(request, 'signup.html')


def forgot_password_page(request):
    return render(request, 'forgot_password.html')