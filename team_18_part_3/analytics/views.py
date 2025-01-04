from django.shortcuts import render
from django.http import HttpResponse
from django.db import IntegrityError
from rest_framework.decorators import api_view
from rest_framework.response import Response

from .models import Projects, Tasks, Project_Employee, Project_Task, Project_Graph, User_Tasks
from chat.models import Employee


# Create your views here.
def test_page(request):
    current_employee_id = request.session.get('employee_id')
    return render(request, "analytics_test_page.html")


def projects_page(request):
    current_employee_id = request.session.get('employee_id')
    projects = Projects.objects.all().values()
    project_employee = Project_Employee.objects.all().values()
    project_tasks = Project_Task.objects.all()
    tasks = Tasks.objects.all().values()

    for project in projects:
        assigned_employees_count = project_employee.filter(project_id=project['project_id']).count()
        project['assigned_employee'] = assigned_employees_count

        project_tasks = project_tasks.filter(project_id=project['project_id'])
        total_duration = 0
        complete_duration = 0
        for task in project_tasks:
            task_data = tasks.filter(task_id=task.task_id_id).first()
            total_duration += task_data['task_duration']

            if task_data['task_status'] == "Complete":
                complete_duration += task_data['task_duration']

        progress = int((complete_duration / total_duration) * 100 if total_duration != 0 else 0)
        project['progress'] = progress

    context = {
        'projects_list': projects,
        'project_employee': project_employee
    }
    return render(request, "projects-list.html", context)


def dashboard(request, project_id):
    current_employee_id = request.session.get('employee_id')
    project = Projects.objects.filter(project_id=project_id).first()
    context = {
        'project_id': project_id,
        'project_name': project.project_name,
        'project_deadline': project.project_deadline,
        'project_status': project.project_status,
        'project_colour': project.project_colour,
    }
    return render(request, "dashboard.html", context)


@api_view(['GET'])
def get_project_graphs(request):
    project_id = request.GET.get('project_id')
    if not project_id:
        return Response({'error': 'Project ID is required.'}, status=400)

    graphs = {}
    graph_table_data = Project_Graph.objects.filter(project_id=project_id)
    for graph in graph_table_data:
        graphs[graph.graph_id] = {
            'title': graph.graph_title,
            'content': graph.graph_content,
            'type': graph.graph_type,
            'filters': {
                'complete': graph.graph_filter_complete,
                'not_started': graph.graph_filter_not_started,
                'ongoing': graph.graph_filter_ongoing,
                'paused': graph.graph_filter_paused,
                'overdue': graph.graph_filter_overdue,
            },
        }

    return Response(graphs)


@api_view(['GET'])
def get_project_task_counts(request):
    # Ensure project_id is provided in the request
    project_id = request.GET.get('project_id')
    print(project_id)
    if not project_id:
        return Response({'error': 'Project ID is required.'}, status=400)

    # Retrieve project task IDs
    project_task_ids = Project_Task.objects.filter(project_id=project_id).values_list('task_id_id', flat=True)

    # Retrieve project task data
    project_task_data = {}
    for task_id in project_task_ids:
        task = Tasks.objects.filter(task_id=task_id).first()
        if task:
            project_task_data[task_id] = {
                'status': task.task_status,
                'duration': task.task_duration,
            }

    # Retrieve employee IDs associated with project tasks
    task_employee = {}
    for task_id in project_task_ids:
        task_employee[task_id] = list(User_Tasks.objects.filter(task_id=task_id).values_list('user_id', flat=True))

    # Retrieve project employee IDs
    project_employee_ids = list(Project_Employee.objects.filter(project_id=project_id).values_list('user_id', flat=True))

    # Retrieve employee names
    employee_names = dict(Employee.objects.filter(employee_id__in=project_employee_ids).values_list('employee_id', 'employee_first_name'))
    print("names", employee_names)
    # Initialize task counts
    task_counts = {
        'all': [{"label": name, "y": 0} for name in employee_names.values()],
        'complete': [{"label": name, "y": 0} for name in employee_names.values()],
        'not-started': [{"label": name, "y": 0} for name in employee_names.values()],
        'ongoing': [{"label": name, "y": 0} for name in employee_names.values()],
        'overdue': [{"label": name, "y": 0} for name in employee_names.values()],
        'paused': [{"label": name, "y": 0} for name in employee_names.values()],
    }

    # Initialize task durations
    task_durations = {
        'all': [{"label": name, "y": 0} for name in employee_names.values()],
        'complete': [{"label": name, "y": 0} for name in employee_names.values()],
        'not-started': [{"label": name, "y": 0} for name in employee_names.values()],
        'ongoing': [{"label": name, "y": 0} for name in employee_names.values()],
        'overdue': [{"label": name, "y": 0} for name in employee_names.values()],
        'paused': [{"label": name, "y": 0} for name in employee_names.values()],
    }

    # Update the counts
    for task_id, task_data in project_task_data.items():
        # Get employee names
        employees = task_employee.get(task_id, [])
        for employee_id in employees:
            employee_name = employee_names.get(employee_id)
            if employee_name:
                key = ""
                if task_data['status'] == "Not Started":
                    key = "not-started"
                else:
                    key = task_data['status'].lower()
                    print(key)
                # Update task counts
                for index, entry in enumerate(task_counts["all"]):
                    if entry["label"] == employee_name:
                        task_counts["all"][index]["y"] += 1

                for index, entry in enumerate(task_counts[key]):
                    if entry["label"] == employee_name:
                        task_counts[key][index]["y"] += 1

                # Update task durations
                for index, entry in enumerate(task_durations["all"]):
                    if entry["label"] == employee_name:
                        task_durations["all"][index]["y"] += task_data['duration']

                for index, entry in enumerate(task_durations[key]):
                    if entry["label"] == employee_name:
                        task_durations[key][index]["y"] += task_data['duration']

    return Response({
        'task_counts': task_counts,
        'task_durations': task_durations
    })


@api_view(['POST'])
def create_graph(request):
    current_employee_id = request.session.get('employee_id')
    project_id = request.POST.get('project_id')
    graph_title = request.POST.get('graph_title')
    graph_content = request.POST.get('graph_content')
    graph_type = request.POST.get('graph_type')
    graph_filters = {
        'complete': request.POST.get('graph_filter_complete') == "true",
        'not_started': request.POST.get('graph_filter_not_started') == "true",
        'ongoing': request.POST.get('graph_filter_ongoing') == "true",
        'overdue': request.POST.get('graph_filter_overdue') == "true",
        'paused': request.POST.get('graph_filter_paused') == "true",
    }

    try:
        # Create a new ProjectGraph object and save it to the database
        project_graph = Project_Graph.objects.create(
            project_id=Projects.objects.get(project_id=project_id),
            graph_title=graph_title,
            graph_content=graph_content,
            graph_type=graph_type,
            graph_filter_complete=graph_filters['complete'],
            graph_filter_not_started=graph_filters['not_started'],
            graph_filter_ongoing=graph_filters['ongoing'],
            graph_filter_overdue=graph_filters['overdue'],
            graph_filter_paused=graph_filters['paused'],
        )
        return Response({'graph_id': project_graph.graph_id})
    except IntegrityError:
        return HttpResponse("Error: Graph already exists!")

    # if project_id and graph_title and graph_content and graph_type:
    #
    # else:
    #     return HttpResponse("Error: Please fill in all fields!")


@api_view(['POST'])
def remove_graph(request):
    current_employee_id = request.session.get('employee_id')
    graph_id = request.GET.get('graph_id')

    if graph_id is None:
        return HttpResponse("Error: Please provide a project ID!")

    try:
        # Check if the graph exists
        project_graph = Project_Graph.objects.get(graph_id=graph_id)

        # Delete the graph
        project_graph.delete()

        return HttpResponse("Graph removed successfully!")
    except Project_Graph.DoesNotExist:
        return HttpResponse("Error: Graph does not exist!")


@api_view(['POST'])
def update_graph(request):
    current_employee_id = request.session.get('employee_id')

    # project_id = request.POST.get('project_id')
    graph_id = request.POST.get('graph_id')
    graph_title = request.POST.get('graph_title')
    graph_content = request.POST.get('graph_content')
    graph_type = request.POST.get('graph_type')
    graph_filters = {
        'complete': request.POST.get('graph_filter_complete') == "true",
        'not_started': request.POST.get('graph_filter_not_started') == "true",
        'ongoing': request.POST.get('graph_filter_ongoing') == "true",
        'overdue': request.POST.get('graph_filter_overdue') == "true",
        'paused': request.POST.get('graph_filter_paused') == "true",
    }

    try:
        # Retrieve the graph object to be updated
        project_graph = Project_Graph.objects.get(graph_id=graph_id)

        # Update the fields if provided
        if graph_title:
            project_graph.graph_title = graph_title
        if graph_content:
            project_graph.graph_content = graph_content
        if graph_type:
            project_graph.graph_type = graph_type

        project_graph.graph_filter_complete = graph_filters['complete']
        project_graph.graph_filter_not_started = graph_filters['not_started']
        project_graph.graph_filter_ongoing = graph_filters['ongoing']
        project_graph.graph_filter_overdue = graph_filters['overdue']
        project_graph.graph_filter_paused = graph_filters['paused']

        # Save the updated graph
        project_graph.save()

        return HttpResponse("Graph updated successfully!")
    except Project_Graph.DoesNotExist:
        return HttpResponse("Error: Graph does not exist!")
