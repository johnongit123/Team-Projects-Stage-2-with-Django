from django.urls import path
from django.conf.urls.static import static
from team_18_part_3 import settings
from . import views

urlpatterns = [
    # pages urls
    path('test/', views.test_page),
    path('projects/', views.projects_page),
    path('dashboard/<str:project_id>', views.dashboard),

    path('api/get-project-task-counts', views.get_project_task_counts),
    path('api/get-project-graphs', views.get_project_graphs),
    path('api/create-graph', views.create_graph),
    path('api/remove-graph', views.remove_graph),
    path('api/update-graph', views.update_graph),
] + static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
