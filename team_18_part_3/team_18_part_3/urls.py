"""
URL configuration for part_3 project.

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/5.0/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""
from django.contrib import admin
from django.urls import path, include
from django.conf.urls.static import static
from django.conf import settings  # Import settings
from team_18_part_3 import settings
from . import views

urlpatterns = [
    path('login/', views.login_page),
    path('signup/', views.signup_page),
    path('forgot-password/', views.forgot_password_page),
    path('admin/', admin.site.urls),
    path('chat/', include('chat.urls'), name='chat-list'),
    path('analytics/', include('analytics.urls'), name='data-analytics'),
] + static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)  # Use STATIC_ROOT